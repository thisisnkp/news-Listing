<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageSeoController extends Controller
{
    public function index()
    {
        $pages = PageSeo::orderBy('id')->get();
        return view('admin.page_seos.index', compact('pages'));
    }

    public function edit(PageSeo $page_seo)
    {
        return view('admin.page_seos.edit', ['page' => $page_seo]);
    }

    public function update(Request $request, PageSeo $page_seo)
    {
        $data = $request->validate([
            'meta_title'         => 'nullable|string|max:255',
            'meta_description'   => 'nullable|string|max:500',
            'meta_keywords'      => 'nullable|string|max:500',
            'canonical_override' => 'nullable|url|max:500',
            'robots'             => 'nullable|string|max:120',
            'json_ld'            => 'nullable|string',
            'custom_head'        => 'nullable|string',
            'og_image'           => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('og_image')) {
            if ($page_seo->og_image) {
                Storage::disk('public')->delete($page_seo->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('page_seo', 'public');
        }

        $page_seo->update($data);
        return redirect()->route('admin.page_seos.index')->with('success', "SEO updated for {$page_seo->page_label}.");
    }

    public function removeOgImage(PageSeo $page_seo)
    {
        if ($page_seo->og_image) {
            Storage::disk('public')->delete($page_seo->og_image);
            $page_seo->update(['og_image' => null]);
        }
        return back()->with('success', 'OG image removed.');
    }
}
