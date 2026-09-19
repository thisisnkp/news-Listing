<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Package::withCount(['plans', 'columns']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('remark', 'like', "%{$search}%");
            });
        }

        $packages = $query->orderBy('order')->paginate(15)->withQueryString();

        return view('admin.packages.index', compact('packages', 'search'));
    }

    public function create()
    {
        return view('admin.packages.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug',
            'type' => 'required|in:media,package',
            'visibility' => 'required|in:public,private',
            'remark' => 'nullable|string',
            'order_button_link' => 'nullable|url|max:500',
            'enabled_filters' => 'nullable|array',
            'enabled_filters.*' => 'string|in:da,dr,disclaimer,backlinks,indexing,sort_az,sort_za',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['enabled_filters'] = $request->input('enabled_filters', []);

        Package::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.form', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug,' . $package->id,
            'type' => 'required|in:media,package',
            'visibility' => 'required|in:public,private',
            'remark' => 'nullable|string',
            'order_button_link' => 'nullable|url|max:500',
            'enabled_filters' => 'nullable|array',
            'enabled_filters.*' => 'string|in:da,dr,disclaimer,backlinks,indexing,sort_az,sort_za',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['enabled_filters'] = $request->input('enabled_filters', []);

        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    public function toggle(Package $package)
    {
        $package->update(['is_active' => !$package->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $package->is_active,
            'message' => $package->is_active ? 'Package activated' : 'Package deactivated'
        ]);
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $id) {
            Package::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
