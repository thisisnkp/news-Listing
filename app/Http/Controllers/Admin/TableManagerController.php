<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableManagerController extends Controller
{
    public function index()
    {
        $tables = DynamicTable::withCount(['columns', 'rows'])
            ->orderBy('order')
            ->paginate(15);

        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:dynamic_tables,slug',
            'services' => 'nullable|array',
            'services.*' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'order_button_link' => 'required|url|max:500',
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

        DynamicTable::create($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Table created successfully.');
    }

    public function edit(DynamicTable $table)
    {
        return view('admin.tables.form', compact('table'));
    }

    public function update(Request $request, DynamicTable $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:dynamic_tables,slug,' . $table->id,
            'services' => 'nullable|array',
            'services.*' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'order_button_link' => 'required|url|max:500',
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

        $table->update($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Table updated successfully.');
    }

    public function destroy(DynamicTable $table)
    {
        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Table deleted successfully.');
    }

    public function toggle(DynamicTable $table)
    {
        $table->update(['is_active' => !$table->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $table->is_active,
            'message' => $table->is_active ? 'Table activated' : 'Table deactivated'
        ]);
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $id) {
            DynamicTable::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
