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

        // Fetch all rows and sort alphabetically by the first column
        $allRows = $query->get();
        $firstColumn = $columns->first();
        $defaultLanguage = Language::getDefault();
        $langCode = $defaultLanguage?->code ?? 'en';

        $sortedRows = $allRows->sortBy(function ($row) use ($firstColumn, $langCode) {
            $data = $row->getTranslatedData($langCode);
            return $firstColumn ? strtolower($data[$firstColumn->slug] ?? '') : '';
        }, SORT_NATURAL | SORT_FLAG_CASE);

        // Manual pagination
        $perPage = 20;
        $page = $request->input('page', 1);
        $rows = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedRows->forPage($page, $perPage),
            $sortedRows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $languages = Language::active()->get();

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

        // Fetch all rows and sort alphabetically by the first column
        $allRows = $query->get();
        $firstColumn = $columns->first();
        $defaultLanguage = Language::getDefault();
        $langCode = $defaultLanguage?->code ?? 'en';

        $sortedRows = $allRows->sortBy(function ($row) use ($firstColumn, $langCode) {
            $data = $row->getTranslatedData($langCode);
            return $firstColumn ? strtolower($data[$firstColumn->slug] ?? '') : '';
        }, SORT_NATURAL | SORT_FLAG_CASE);

        // Manual pagination
        $perPage = 20;
        $page = $request->input('page', 1);
        $rows = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedRows->forPage($page, $perPage),
            $sortedRows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $languages = Language::active()->get();

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

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No rows selected.']);
        }

        TableRow::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected rows deleted successfully.']);
    }

    public function sortAlphabetically(Request $request)
    {
        $planId = $request->input('plan_id');
        $packageId = $request->input('package_id');

        if (!$planId && !$packageId) {
            return response()->json(['success' => false, 'message' => 'Invalid request.']);
        }

        $query = TableRow::query();

        if ($planId) {
            $query->where('plan_id', $planId);
            $plan = Plan::find($planId);
            $firstColumn = $plan->columns()->ordered()->first();
        } else {
            $query->where('package_id', $packageId);
            $package = Package::find($packageId);
            $firstColumn = $package->columns()->ordered()->first();
        }

        if (!$firstColumn) {
            return response()->json(['success' => false, 'message' => 'No columns found to sort by.']);
        }

        // Fetch rows and sort them in PHP
        // This is necessary because data is stored in a JSON column
        $rows = $query->get();

        $sortedRows = $rows->sortBy(function ($row) use ($firstColumn) {
            $data = $row->getTranslatedData(); // Default language or fallback
            return $data[$firstColumn->slug] ?? '';
        }, SORT_NATURAL | SORT_FLAG_CASE);

        // Update order in database
        $order = 0;
        foreach ($sortedRows as $row) {
            $row->update(['order' => $order++]);
        }

        return response()->json(['success' => true, 'message' => 'Rows sorted alphabetically.']);
    }
}
