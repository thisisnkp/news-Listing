<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LocalSeo;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LocalSeoController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page');
        if (!array_key_exists($page, LocalSeo::PAGES)) {
            $page = null;
        }

        $query = LocalSeo::query()->orderBy('page_slug')->orderBy('city');
        if ($page) {
            $query->where('page_slug', $page);
        }
        $locals = $query->paginate(30)->withQueryString();

        // Count per page for the filter tabs.
        $counts = LocalSeo::selectRaw('page_slug, COUNT(*) as c')
            ->groupBy('page_slug')
            ->pluck('c', 'page_slug');

        return view('admin.local_seos.index', compact('locals', 'page', 'counts'));
    }

    public function create(Request $request)
    {
        $page = $request->get('page', 'home');
        if (!array_key_exists($page, LocalSeo::PAGES)) {
            $page = 'home';
        }
        $local = new LocalSeo(['page_slug' => $page]);
        $cities = $this->knownTestimonialCities();

        return view('admin.local_seos.create', compact('local', 'cities'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('local_seo', 'public');
        }

        LocalSeo::create($data);

        return redirect()
            ->route('admin.local_seos.index', ['page' => $data['page_slug']])
            ->with('success', "Local page created — {$data['city']} ({$data['page_slug']}).");
    }

    public function edit(LocalSeo $local_seo)
    {
        $local = $local_seo;
        $cities = $this->knownTestimonialCities();

        return view('admin.local_seos.edit', compact('local', 'cities'));
    }

    public function update(Request $request, LocalSeo $local_seo)
    {
        $data = $this->validated($request, $local_seo);

        if ($request->hasFile('og_image')) {
            if ($local_seo->og_image) {
                Storage::disk('public')->delete($local_seo->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('local_seo', 'public');
        }

        $local_seo->update($data);

        return redirect()
            ->route('admin.local_seos.index', ['page' => $local_seo->page_slug])
            ->with('success', "Local page updated — {$local_seo->city}.");
    }

    public function destroy(LocalSeo $local_seo)
    {
        if ($local_seo->og_image) {
            Storage::disk('public')->delete($local_seo->og_image);
        }
        $page = $local_seo->page_slug;
        $local_seo->delete();

        return redirect()
            ->route('admin.local_seos.index', ['page' => $page])
            ->with('success', 'Local page deleted.');
    }

    public function removeOgImage(LocalSeo $local_seo)
    {
        if ($local_seo->og_image) {
            Storage::disk('public')->delete($local_seo->og_image);
            $local_seo->update(['og_image' => null]);
        }
        return back()->with('success', 'OG image removed.');
    }

    /**
     * Validate + normalize. On create, page_slug + city_slug must be unique together.
     * FAQ rows arrive as parallel faq_q[] / faq_a[] arrays and are folded into JSON.
     */
    private function validated(Request $request, ?LocalSeo $existing = null): array
    {
        $pageSlug = $existing?->page_slug ?? $request->input('page_slug');

        $citySlug = Str::slug($request->input('city_slug') ?: $request->input('city', ''));

        // Inject the normalized slug so the unique rule sees it.
        $request->merge(['city_slug' => $citySlug]);

        $rules = [
            'city'               => 'required|string|max:120',
            'city_slug'          => [
                'required', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('local_seos', 'city_slug')
                    ->where(fn ($q) => $q->where('page_slug', $pageSlug))
                    ->ignore($existing?->id),
            ],
            'meta_title'         => 'nullable|string|max:255',
            'meta_description'   => 'nullable|string|max:500',
            'meta_keywords'      => 'nullable|string|max:500',
            'canonical_override' => 'nullable|url|max:500',
            'robots'             => 'nullable|string|max:120',
            'og_image'           => 'nullable|image|max:4096',
            'json_ld'            => 'nullable|string',
            'custom_head'        => 'nullable|string',
            'hero_heading'       => 'nullable|string|max:1000',
            'hero_subheading'    => 'nullable|string|max:2000',
            'faq_q'              => 'nullable|array',
            'faq_q.*'            => 'nullable|string|max:300',
            'faq_a'              => 'nullable|array',
            'faq_a.*'            => 'nullable|string|max:2000',
            'is_active'          => 'nullable|boolean',
        ];

        if (!$existing) {
            $rules['page_slug'] = ['required', Rule::in(array_keys(LocalSeo::PAGES))];
        }

        $validated = $request->validate($rules);

        $validated['page_slug'] = $pageSlug;
        $validated['is_active'] = $request->boolean('is_active');
        $validated['faqs']      = $this->foldFaqs($request);

        // Strip the parallel FAQ arrays — they're not table columns.
        unset($validated['faq_q'], $validated['faq_a']);

        return $validated;
    }

    /** Fold faq_q[] / faq_a[] into a JSON array of {q,a}, dropping empty questions. */
    private function foldFaqs(Request $request): ?string
    {
        $qs = $request->input('faq_q', []);
        $as = $request->input('faq_a', []);
        $out = [];
        foreach ($qs as $i => $q) {
            $q = trim((string) $q);
            if ($q === '') continue;
            $out[] = ['q' => $q, 'a' => trim((string) ($as[$i] ?? ''))];
        }
        return empty($out) ? null : json_encode($out, JSON_UNESCAPED_UNICODE);
    }

    /** Distinct non-empty city tags already used on testimonials (for a datalist). */
    private function knownTestimonialCities(): array
    {
        return Testimonial::query()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->all();
    }
}
