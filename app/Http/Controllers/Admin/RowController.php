<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Language;
use App\Models\TableRow;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RowsImport;

class RowController extends Controller
{
    public function index(Request $request, Plan $plan)
    {
        $search = $request->input('search');

        $columns = $plan->columns()->ordered()->get();
        $query = $plan->rows()
            ->with('translations.language');

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Search in the JSON data field
                $q->where('data', 'like', "%{$search}%")
                    // Also search in translations
                    ->orWhereHas('translations', function ($tq) use ($search) {
                        $tq->where('translated_data', 'like', "%{$search}%");
                    });
            });
        }

        $rows = $query->ordered()->paginate(20)->withQueryString();
        $languages = Language::active()->get();
        $defaultLanguage = Language::getDefault();

        return view('admin.rows.index', compact('plan', 'columns', 'rows', 'languages', 'defaultLanguage', 'search'));
    }

    /**
     * Display rows for a media-type package
     */
    public function indexForPackage(Request $request, Package $package)
    {
        $search = $request->input('search');

        $columns = $package->columns()->ordered()->get();
        $query = $package->rows()
            ->with('translations.language');

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Search in the JSON data field
                $q->where('data', 'like', "%{$search}%")
                    // Also search in translations
                    ->orWhereHas('translations', function ($tq) use ($search) {
                        $tq->where('translated_data', 'like', "%{$search}%");
                    });
            });
        }

        $rows = $query->ordered()->paginate(20)->withQueryString();
        $languages = Language::active()->get();
        $defaultLanguage = Language::getDefault();

        return view('admin.rows.index_package', compact('package', 'columns', 'rows', 'languages', 'defaultLanguage', 'search'));
    }

    public function store(Request $request, Plan $plan)
    {
        $columns = $plan->columns;
        $languages = Language::active()->get();

        // Build validation rules dynamically
        $rules = [];
        foreach ($columns as $column) {
            if (!$column->is_translatable) {
                $rules["data.{$column->slug}"] = $column->type === 'number' || $column->type === 'currency'
                    ? 'nullable|numeric'
                    : 'nullable|string';
            }
        }

        $validated = $request->validate($rules);

        // Create the row
        $row = $plan->rows()->create([
            'data' => $request->input('data', []),
            'order' => $plan->rows()->max('order') + 1,
        ]);

        // Save translations
        foreach ($languages as $language) {
            $translatedData = $request->input("translations.{$language->id}", []);
            if (!empty(array_filter($translatedData))) {
                $row->setTranslation($language->id, $translatedData);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Row added successfully', 'row_id' => $row->id]);
        }

        return redirect()->back()->with('success', 'Row added successfully.');
    }

    /**
     * Store row for a media-type package
     */
    public function storeForPackage(Request $request, Package $package)
    {
        $columns = $package->columns;
        $languages = Language::active()->get();

        // Build validation rules dynamically
        $rules = [];
        foreach ($columns as $column) {
            if (!$column->is_translatable) {
                $rules["data.{$column->slug}"] = $column->type === 'number' || $column->type === 'currency'
                    ? 'nullable|numeric'
                    : 'nullable|string';
            }
        }

        $validated = $request->validate($rules);

        // Create the row
        $row = $package->rows()->create([
            'data' => $request->input('data', []),
            'order' => $package->rows()->max('order') + 1,
        ]);

        // Save translations
        foreach ($languages as $language) {
            $translatedData = $request->input("translations.{$language->id}", []);
            if (!empty(array_filter($translatedData))) {
                $row->setTranslation($language->id, $translatedData);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Row added successfully', 'row_id' => $row->id]);
        }

        return redirect()->back()->with('success', 'Row added successfully.');
    }

    public function update(Request $request, TableRow $row)
    {
        $plan = $row->plan ?? $row->package ?? $row->dynamicTable;
        $columns = $plan->columns;

        // Update non-translatable data
        $row->update([
            'data' => $request->input('data', []),
        ]);

        // Update translations
        $languages = Language::active()->get();
        foreach ($languages as $language) {
            $translatedData = $request->input("translations.{$language->id}", []);
            if (!empty(array_filter($translatedData))) {
                $row->setTranslation($language->id, $translatedData);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Row updated successfully']);
        }

        return redirect()->back()->with('success', 'Row updated successfully.');
    }

    public function destroy(TableRow $row)
    {
        $row->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Row deleted successfully']);
        }

        return redirect()->back()->with('success', 'Row deleted successfully.');
    }

    public function import(Request $request, Plan $plan)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new RowsImport($plan), $request->file('file'));

            return redirect()->back()->with('success', 'Data imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Import rows for a media-type package
     */
    public function importForPackage(Request $request, Package $package)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new RowsImport($package, true), $request->file('file'));

            return redirect()->back()->with('success', 'Data imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $id) {
            TableRow::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
