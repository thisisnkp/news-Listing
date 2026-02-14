<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index(Request $request, Package $package)
    {
        $search = $request->input('search');

        $query = $package->plans()
            ->withCount(['columns', 'rows']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $plans = $query->orderBy('order')->paginate(15)->withQueryString();

        return view('admin.plans.index', compact('package', 'plans', 'search'));
    }

    public function create(Package $package)
    {
        return view('admin.plans.form', compact('package'));
    }

    public function store(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:plans,slug',
            'services' => 'nullable|array',
            'services.*' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'order_button_link' => 'nullable|url|max:500',
            'enabled_filters' => 'nullable|array',
            'enabled_filters.*' => 'string|in:da,dr,disclaimer,backlinks,indexing,sort_az,sort_za',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['package_id'] = $package->id;

        // Filter out empty services
        if (isset($validated['services'])) {
            $validated['services'] = array_values(array_filter($validated['services'], fn($s) => !empty(trim($s))));
        }

        Plan::create($validated);

        return redirect()->route('admin.plans.index', $package)
            ->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        $package = $plan->package;
        return view('admin.plans.form', compact('package', 'plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:plans,slug,' . $plan->id,
            'services' => 'nullable|array',
            'services.*' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'order_button_link' => 'nullable|url|max:500',
            'enabled_filters' => 'nullable|array',
            'enabled_filters.*' => 'string|in:da,dr,disclaimer,backlinks,indexing,sort_az,sort_za',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        // Filter out empty services
        if (isset($validated['services'])) {
            $validated['services'] = array_values(array_filter($validated['services'], fn($s) => !empty(trim($s))));
        }

        $plan->update($validated);

        return redirect()->route('admin.plans.index', $plan->package)
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $package = $plan->package;
        $plan->delete();

        return redirect()->route('admin.plans.index', $package)
            ->with('success', 'Plan deleted successfully.');
    }

    public function toggle(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $plan->is_active,
            'message' => $plan->is_active ? 'Plan activated' : 'Plan deactivated'
        ]);
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $id) {
            Plan::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
