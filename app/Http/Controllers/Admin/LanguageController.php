<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderBy('name')->paginate(15);

        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.languages.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');

        // If setting as default, unset other defaults
        if ($validated['is_default']) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        Language::create($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language created successfully.');
    }

    public function edit(Language $language)
    {
        return view('admin.languages.form', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');

        // If setting as default, unset other defaults
        if ($validated['is_default'] && !$language->is_default) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->update($validated);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language updated successfully.');
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return redirect()->back()->with('error', 'Cannot delete default language.');
        }

        $language->delete();

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language deleted successfully.');
    }

    public function setDefault(Language $language)
    {
        Language::where('is_default', true)->update(['is_default' => false]);
        $language->update(['is_default' => true, 'is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => $language->name . ' is now the default language.'
        ]);
    }

    public function toggle(Language $language)
    {
        if ($language->is_default && $language->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot deactivate default language.'
            ], 400);
        }

        $language->update(['is_active' => !$language->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $language->is_active,
            'message' => $language->is_active ? 'Language activated' : 'Language deactivated'
        ]);
    }
}
