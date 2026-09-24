<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PageImageController extends Controller
{
    public function index(Request $request)
    {
        $page = $this->resolvePage($request->get('page'));
        $this->ensureSlots($page);

        $rows = PageImage::where('page_slug', $page)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('section_key');

        return view('admin.page_images.index', [
            'page'     => $page,
            'sections' => PageImage::sectionsFor($page),
            'rows'     => $rows,
        ]);
    }

    /** Save one single-image or video slot. */
    public function update(Request $request, PageImage $page_image)
    {
        $type = $page_image->sectionMeta()['type'];

        $data = $request->validate([
            'alt'       => 'nullable|string|max:500',
            'image'     => 'nullable|image|max:6144',
            'video_url' => [$type === 'video' ? 'required' : 'nullable', 'nullable', 'url', 'max:512'],
        ]);

        $update = ['alt' => $data['alt'] ?? null];

        if ($type === 'video') {
            $update['video_url'] = $data['video_url'] ?? null;
        } elseif ($request->hasFile('image')) {
            $this->deleteUpload($page_image);
            $update['image'] = $request->file('image')->store('page_images', 'public');
        }

        $page_image->update($update);

        return redirect()
            ->route('admin.page_images.index', ['page' => $page_image->page_slug])
            ->with('success', "Updated — {$page_image->sectionLabel()}.");
    }

    /** Upload one or more images into a gallery section. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'page_slug'   => ['required', Rule::in(array_keys(PageImage::PAGES))],
            'section_key' => 'required|string|max:48',
            'images'      => 'required|array|max:30',
            'images.*'    => 'image|max:6144',
        ]);

        $meta = PageImage::SECTIONS[$data['page_slug']][$data['section_key']] ?? null;
        if (!$meta || $meta['type'] !== 'multi') {
            return back()->withErrors(['images' => 'That section does not accept multiple images.']);
        }

        $files = $request->file('images');
        $count = count($files);

        // Newest first: push the existing images down so the uploads take the top slots.
        // Their relative order is preserved, and the admin can still renumber by hand.
        PageImage::where('page_slug', $data['page_slug'])
            ->where('section_key', $data['section_key'])
            ->increment('sort_order', $count);

        $slot = 0;
        foreach ($files as $file) {
            PageImage::create([
                'page_slug'   => $data['page_slug'],
                'section_key' => $data['section_key'],
                'image'       => $file->store('page_images', 'public'),
                'sort_order'  => $slot++,
                'is_active'   => true,
            ]);
        }

        return redirect()
            ->route('admin.page_images.index', ['page' => $data['page_slug']])
            ->with('success', $count . ' image' . ($count === 1 ? '' : 's') . ' added.');
    }

    /** Save alt text, order and visibility for every row of a gallery in one submit. */
    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'page_slug'    => ['required', Rule::in(array_keys(PageImage::PAGES))],
            'alt'          => 'nullable|array',
            'alt.*'        => 'nullable|string|max:500',
            'sort_order'   => 'nullable|array',
            'sort_order.*' => 'nullable|integer|min:0|max:9999',
            'active'       => 'nullable|array',
        ]);

        $ids     = array_keys($data['sort_order'] ?? $data['alt'] ?? []);
        $active  = $data['active'] ?? [];
        $updated = 0;

        foreach (PageImage::where('page_slug', $data['page_slug'])->whereIn('id', $ids)->get() as $row) {
            $row->update([
                'alt'        => $data['alt'][$row->id] ?? null,
                'sort_order' => (int) ($data['sort_order'][$row->id] ?? $row->sort_order),
                'is_active'  => array_key_exists($row->id, $active),
            ]);
            $updated++;
        }

        return redirect()
            ->route('admin.page_images.index', ['page' => $data['page_slug']])
            ->with('success', "Gallery saved — {$updated} image(s).");
    }

    /** Drop an uploaded file and fall back to the image the page originally shipped with. */
    public function removeImage(PageImage $page_image)
    {
        $this->deleteUpload($page_image);
        $page_image->update(['image' => null]);

        $note = $page_image->legacy_path
            ? 'Upload removed — the original image is showing again.'
            : 'Upload removed.';

        return redirect()
            ->route('admin.page_images.index', ['page' => $page_image->page_slug])
            ->with('success', $note);
    }

    public function destroy(PageImage $page_image)
    {
        if ($page_image->sectionMeta()['type'] !== 'multi') {
            return back()->withErrors(['image' => 'This slot cannot be deleted — replace the image instead.']);
        }

        $page = $page_image->page_slug;
        $this->deleteUpload($page_image);
        $page_image->delete();

        return redirect()
            ->route('admin.page_images.index', ['page' => $page])
            ->with('success', 'Image removed from the gallery.');
    }

    private function resolvePage(?string $page): string
    {
        return array_key_exists((string) $page, PageImage::PAGES) ? (string) $page : 'studio';
    }

    /** Single-image and video slots are fixed — make sure each one has a row to edit. */
    private function ensureSlots(string $page): void
    {
        foreach (PageImage::sectionsFor($page) as $key => $meta) {
            if ($meta['type'] === 'multi') {
                continue;
            }
            PageImage::firstOrCreate(
                ['page_slug' => $page, 'section_key' => $key],
                ['is_active' => true]
            );
        }
    }

    private function deleteUpload(PageImage $row): void
    {
        if ($row->image) {
            Storage::disk('public')->delete($row->image);
        }
    }
}
