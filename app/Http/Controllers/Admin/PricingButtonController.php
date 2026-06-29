<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingButton;
use App\Models\Package;
use Illuminate\Http\Request;

class PricingButtonController extends Controller
{
    public function index()
    {
        $buttons = PricingButton::ordered()->paginate(40);
        return view('admin.pricing_buttons.index', compact('buttons'));
    }

    public function create()
    {
        $button = new PricingButton(['is_active' => true, 'sort_order' => (int) PricingButton::max('sort_order') + 1]);
        return view('admin.pricing_buttons.create', compact('button'));
    }

    public function store(Request $request)
    {
        PricingButton::create($this->validated($request));
        return redirect()->route('admin.pricing_buttons.index')->with('success', 'Button created.');
    }

    public function edit(PricingButton $pricing_button)
    {
        $button = $pricing_button;
        return view('admin.pricing_buttons.edit', compact('button'));
    }

    public function update(Request $request, PricingButton $pricing_button)
    {
        $pricing_button->update($this->validated($request));
        return redirect()->route('admin.pricing_buttons.index')->with('success', 'Button updated.');
    }

    public function destroy(PricingButton $pricing_button)
    {
        $pricing_button->delete();
        return back()->with('success', 'Button deleted.');
    }

    public function toggle(PricingButton $pricing_button)
    {
        $pricing_button->update(['is_active' => !$pricing_button->is_active]);
        return back()->with('success', 'Status updated.');
    }

    /**
     * One-click import: turn each public package into an editable button.
     * Idempotent — keyed on the generated URL, so re-running won't duplicate.
     */
    public function import()
    {
        $packages = Package::active()->public()->ordered()->get();
        $order = (int) PricingButton::max('sort_order');
        $created = 0;

        foreach ($packages as $p) {
            // Root-relative path incl. the /pricing prefix (e.g.
            // /rvrising-php/pricing/package/xxx locally, /pricing/package/xxx in
            // production) — portable + works as a plain href.
            $url = parse_url(route('package.show', $p->slug), PHP_URL_PATH);

            $button = PricingButton::firstOrCreate(
                ['url' => $url],
                [
                    'label'      => $p->name,
                    'icon'       => $p->isMedia() ? 'fas fa-table' : 'fas fa-layer-group',
                    'sort_order' => ++$order,
                    'is_active'  => true,
                ]
            );
            if ($button->wasRecentlyCreated) $created++;
        }

        return back()->with('success', $created > 0
            ? "Imported {$created} package(s) as buttons — edit or reorder them below."
            : 'All packages are already imported as buttons.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label'      => 'required|string|max:160',
            'icon'       => 'nullable|string|max:80',
            'url'        => 'required|string|max:500',
            'new_tab'    => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['new_tab']    = $request->boolean('new_tab');
        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int) $request->input('sort_order', 0);

        return $data;
    }
}
