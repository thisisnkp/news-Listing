<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Plan;
use App\Models\TableColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ColumnController extends Controller
{
    public function index(Plan $plan)
    {
        $columns = $plan->columns()->ordered()->get();

        return view('admin.columns.index', compact('plan', 'columns'));
    }

    /**
     * Display columns for a media-type package
     */
    public function indexForPackage(Package $package)
    {
        $columns = $package->columns()->ordered()->get();
        $rows = $package->rows()->ordered()->get();

        return view('admin.columns.index_package', compact('package', 'columns', 'rows'));
    }

    public function store(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,currency,button,dropdown',
            'name_if_button' => 'nullable|string|max:255',
            'dropdown_options' => 'nullable|array',
            'dropdown_options.*' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($validated['name'], '_');

        // Check for duplicate slug within the same plan
        $existingColumn = $plan->columns()->where('slug', $slug)->first();
        if ($existingColumn) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'A column with this name already exists in this plan.']);
        }

        $validated['plan_id'] = $plan->id;
        $validated['slug'] = $slug;
        $validated['is_translatable'] = $request->has('is_translatable');
        $validated['is_filterable'] = $request->has('is_filterable');
        $validated['is_sortable'] = $request->has('is_sortable') ? true : true; // Default to true
        $validated['order'] = ($plan->columns()->max('order') ?? 0) + 1;
        $validated['name_if_button'] = $request->input('name_if_button');

        // Handle dropdown options
        $dropdownOptions = $request->input('dropdown_options', []);
        $validated['dropdown_options'] = array_values(array_filter($dropdownOptions));

        TableColumn::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Column added successfully']);
        }

        return redirect()->back()->with('success', 'Column added successfully.');
    }

    /**
     * Store column for a media-type package
     */
    public function storeForPackage(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,currency,button,dropdown',
            'name_if_button' => 'nullable|string|max:255',
            'dropdown_options' => 'nullable|array',
            'dropdown_options.*' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($validated['name'], '_');

        // Check for duplicate slug within the same package
        $existingColumn = $package->columns()->where('slug', $slug)->first();
        if ($existingColumn) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'A column with this name already exists in this package.']);
        }

        $validated['package_id'] = $package->id;
        $validated['slug'] = $slug;
        $validated['is_translatable'] = $request->has('is_translatable');
        $validated['is_filterable'] = $request->has('is_filterable');
        $validated['is_sortable'] = $request->has('is_sortable') ? true : true;
        $validated['order'] = ($package->columns()->max('order') ?? 0) + 1;
        $validated['name_if_button'] = $request->input('name_if_button');

        // Handle dropdown options
        $dropdownOptions = $request->input('dropdown_options', []);
        $validated['dropdown_options'] = array_values(array_filter($dropdownOptions));

        TableColumn::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Column added successfully']);
        }

        return redirect()->back()->with('success', 'Column added successfully.');
    }

    public function update(Request $request, TableColumn $column)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,currency,button,dropdown',
            'name_if_button' => 'nullable|string|max:255',
            'dropdown_options' => 'nullable|array',
            'dropdown_options.*' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name'], '_');
        $validated['is_translatable'] = $request->has('is_translatable');
        $validated['is_filterable'] = $request->has('is_filterable');
        $validated['is_sortable'] = $request->has('is_sortable');
        $validated['name_if_button'] = $request->input('name_if_button');

        // Handle dropdown options
        $dropdownOptions = $request->input('dropdown_options', []);
        $validated['dropdown_options'] = array_values(array_filter($dropdownOptions));

        $column->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Column updated successfully']);
        }

        return redirect()->back()->with('success', 'Column updated successfully.');
    }

    public function destroy(TableColumn $column)
    {
        $column->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Column deleted successfully']);
        }

        return redirect()->back()->with('success', 'Column deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $id) {
            TableColumn::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
