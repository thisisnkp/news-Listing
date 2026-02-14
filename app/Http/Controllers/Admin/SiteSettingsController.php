<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\Language;
use App\Models\DynamicTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::getAllSettings();
        $languages = Language::all();
        $tables = DynamicTable::orderBy('name')->get();

        return view('admin.settings.index', compact('settings', 'languages', 'tables'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:png,ico|max:1024',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'currency_symbol' => 'nullable|string|max:10',
            'currency_position' => 'nullable|in:before,after',
            'homepage_table_id' => 'nullable|exists:dynamic_tables,id',
            'order_button_text' => 'nullable|string|max:50',
        ]);

        // Handle text settings
        foreach (['site_name', 'meta_title', 'meta_description', 'currency_symbol', 'currency_position', 'homepage_table_id', 'order_button_text'] as $key) {
            if (isset($validated[$key])) {
                SiteSetting::set($key, $validated[$key]);
            } elseif ($key === 'homepage_table_id') {
                // Allow clearing the homepage table
                SiteSetting::set($key, null);
            }
        }

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $oldLogo = SiteSetting::get('site_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('site_logo')->store('branding', 'public');
            SiteSetting::set('site_logo', $path, 'image');
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            $oldFavicon = SiteSetting::get('site_favicon');
            if ($oldFavicon) {
                Storage::disk('public')->delete($oldFavicon);
            }

            $path = $request->file('site_favicon')->store('branding', 'public');
            SiteSetting::set('site_favicon', $path, 'image');
        }

        // Handle language toggles
        if ($request->has('active_languages')) {
            $activeLanguages = $request->input('active_languages', []);
            Language::query()->update(['is_active' => false]);
            Language::whereIn('id', $activeLanguages)->update(['is_active' => true]);

            // Ensure default language is always active
            Language::where('is_default', true)->update(['is_active' => true]);
        }

        SiteSetting::clearCache();

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function removeLogo()
    {
        $logo = SiteSetting::get('site_logo');
        if ($logo) {
            Storage::disk('public')->delete($logo);
            SiteSetting::where('key', 'site_logo')->delete();
        }

        SiteSetting::clearCache();

        return response()->json(['success' => true]);
    }

    public function removeFavicon()
    {
        $favicon = SiteSetting::get('site_favicon');
        if ($favicon) {
            Storage::disk('public')->delete($favicon);
            SiteSetting::where('key', 'site_favicon')->delete();
        }

        SiteSetting::clearCache();

        return response()->json(['success' => true]);
    }
}
