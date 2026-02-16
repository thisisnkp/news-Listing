<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        // Show only PUBLIC packages on homepage (not private ones)
        $packages = Package::active()
            ->public()
            ->ordered()
            ->withCount('plans')
            ->get();

        $languages = Language::active()->get();
        $defaultLanguage = Language::getDefault();
        $buttonName = SiteSetting::where('key', 'order_button_text')->first()?->value ?? 'View Details';

        return view('frontend.home', compact('packages', 'languages', 'defaultLanguage', 'buttonName'));
    }

    public function showPackage(string $slug)
    {
        $package = Package::where('slug', $slug)
            ->active()
            ->with([
                'plans' => function ($q) {
                    $q->active()->ordered();
                }
            ])
            ->firstOrFail();

        // Check if private package requires token
        if ($package->isPrivate()) {
            $token = request('token');
            if (!$token || $token !== $package->private_token) {
                abort(404, 'Package not found');
            }
        }

        $languages = Language::active()->get();
        $defaultLanguage = Language::getDefault();
        $currentLang = request('lang', $defaultLanguage?->code ?? 'en');
        $buttonName = SiteSetting::where('key', 'order_button_text')->first()?->value ?? 'Order Now';

        // For media type packages, show table view directly
        if ($package->isMedia()) {
            $columns = $package->columns()->ordered()->get();

            // Get columns marked as filterable for dynamic filter display
            $filterableColumns = $columns->where('is_filterable', true);

            $rows = $this->getFilteredRowsForPackage($package, $columns, $currentLang);

            return view('frontend.media-table', compact('package', 'languages', 'defaultLanguage', 'currentLang', 'buttonName', 'columns', 'rows', 'filterableColumns'));
        }

        // For package type, show plans grid
        return view('frontend.package', compact('package', 'languages', 'defaultLanguage', 'currentLang', 'buttonName'));
    }

    public function showPlan(string $slug)
    {
        $plan = Plan::where('slug', $slug)
            ->active()
            ->with([
                'columns' => function ($q) {
                    $q->ordered();
                }
            ])
            ->firstOrFail();

        // Get all available PUBLIC packages for navigation
        $allPackages = Package::active()->public()->ordered()->get();

        $languages = Language::active()->get();
        $defaultLanguage = Language::getDefault();
        $currentLang = request('lang', $defaultLanguage?->code ?? 'en');

        // Get rows with pagination
        $rows = $this->getFilteredRows($plan, $currentLang);

        // For backwards compatibility with table.blade.php
        $table = $plan;
        $allTables = $allPackages;

        return view('frontend.table', compact('plan', 'table', 'rows', 'languages', 'defaultLanguage', 'currentLang', 'allPackages', 'allTables'));
    }

    public function filter(Request $request, string $slug)
    {
        $plan = Plan::where('slug', $slug)
            ->active()
            ->with([
                'columns' => function ($q) {
                    $q->ordered();
                }
            ])
            ->firstOrFail();

        $currentLang = $request->input('lang', Language::getDefault()?->code ?? 'en');
        $rows = $this->getFilteredRows($plan, $currentLang);

        // For backwards compatibility
        $table = $plan;

        // Return HTML for AJAX
        return view('frontend.partials.table-rows', compact('plan', 'table', 'rows', 'currentLang'))->render();
    }

    public function export(string $slug)
    {
        $plan = Plan::where('slug', $slug)
            ->active()
            ->with([
                'columns' => function ($q) {
                    $q->ordered();
                }
            ])
            ->firstOrFail();

        $defaultLanguage = Language::getDefault();
        $currentLang = request('lang', $defaultLanguage?->code ?? 'en');

        $rows = $plan->rows()->with('translations.language')->get();

        // Build CSV content
        $headers = $plan->columns->pluck('name')->toArray();

        $csvContent = implode(',', array_map(function ($h) {
            return '"' . str_replace('"', '""', (string) $h) . '"';
        }, $headers)) . "\n";

        foreach ($rows as $row) {
            $rowData = $row->getTranslatedData($currentLang);
            $values = [];
            foreach ($plan->columns as $column) {
                $value = $rowData[$column->slug] ?? '';

                // Convert to string if not already
                if (is_array($value)) {
                    $value = implode(', ', $value);
                } elseif (!is_string($value)) {
                    $value = (string) $value;
                }

                // Handle button type or any text|url format - export only the URL link
                if (strpos($value, '|') !== false && preg_match('/^[^|]+\s*\|\s*https?:\/\//i', $value)) {
                    $parts = preg_split('/\s*\|\s*/', $value, 2);
                    $btnLink = trim($parts[1] ?? '');
                    // Export only the clean URL
                    $value = $btnLink ?: $value;
                }
                $values[] = '"' . str_replace('"', '""', $value) . '"';
            }
            $csvContent .= implode(',', $values) . "\n";
        }

        $filename = $plan->slug . '_export_' . date('Y-m-d') . '.csv';

        // Add BOM for Excel compatibility
        $csvContent = "\xEF\xBB\xBF" . $csvContent;

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', strlen($csvContent))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Export package data as CSV (for media-type packages)
     */
    public function exportPackage(string $slug)
    {
        $package = Package::where('slug', $slug)
            ->active()
            ->with([
                'columns' => function ($q) {
                    $q->ordered();
                }
            ])
            ->firstOrFail();

        // Check if private package requires token
        if ($package->isPrivate()) {
            $token = request('token');
            if (!$token || $token !== $package->private_token) {
                abort(403, 'Invalid token');
            }
        }

        $defaultLanguage = Language::getDefault();
        $currentLang = request('lang', $defaultLanguage?->code ?? 'en');

        // Use slug for consistent mapping
        $columns = $package->columns;

        // Build CSV headers
        $headers = $columns->pluck('name')->toArray();

        $csvContent = implode(',', array_map(function ($h) {
            return '"' . str_replace('"', '""', (string) $h) . '"';
        }, $headers)) . "\n";

        // Stream rows safely (prevents memory issues)
        $package->rows()
            ->with('translations.language')
            ->chunk(500, function ($rows) use (&$csvContent, $columns, $currentLang) {

                foreach ($rows as $row) {
                    $rowData = $row->getTranslatedData($currentLang);
                    $values = [];

                    foreach ($columns as $column) {
                        $value = $rowData[$column->slug] ?? '';

                        // Normalize value
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        } else {
                            $value = (string) $value;
                        }

                        // Extract URL from "text | url" format
                        if (strpos($value, '|') !== false) {
                            [$label, $link] = array_map('trim', explode('|', $value, 2));
                            if (filter_var($link, FILTER_VALIDATE_URL)) {
                                $value = $link;
                            }
                        }

                        // Prevent CSV / Excel formula injection
                        if (preg_match('/^[=\-+@]/', $value)) {
                            $value = "'" . $value;
                        }

                        // Escape CSV
                        $values[] = '"' . str_replace('"', '""', $value) . '"';
                    }

                    $csvContent .= implode(',', $values) . "\n";
                }
            });

        // Add UTF-8 BOM for Excel compatibility
        $csvContent = "\xEF\xBB\xBF" . $csvContent;

        $filename = $package->slug . '_export_' . date('Y-m-d') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }


    protected function getFilteredRows(Plan $plan, string $langCode)
    {
        $query = $plan->rows()->with('translations.language');

        $search = request('search');
        $sortBy = request('sort_by');
        // $sortDir = request('sort_dir', 'asc'); // Removed default here to handle specific cases
        $priceMin = request('price_min');
        $priceMax = request('price_max');

        // New dropdown filters
        $filterDA = request('filter_da');
        $filterDR = request('filter_dr');
        $filterDisclaimer = request('filter_disclaimer');
        $filterBacklink = request('filter_backlink');
        $filterIndexing = request('filter_indexing');

        // Get all rows first (for filtering by translated content)
        $rows = $query->get();

        // Apply search filter
        if ($search) {
            $rows = $rows->filter(function ($row) use ($search, $langCode) {
                $data = $row->getTranslatedData($langCode);
                foreach ($data as $value) {
                    if (is_string($value) && stripos($value, $search) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Apply price filter
        if ($priceMin !== null || $priceMax !== null) {
            $rows = $rows->filter(function ($row) use ($priceMin, $priceMax, $langCode) {
                $data = $row->getTranslatedData($langCode);
                $price = $data['price'] ?? 0;

                if ($priceMin !== null && $price < $priceMin) {
                    return false;
                }
                if ($priceMax !== null && $price > $priceMax) {
                    return false;
                }
                return true;
            });
        }

        // Apply DA filter (numeric range)
        if ($filterDA) {
            $rows = $rows->filter(function ($row) use ($filterDA, $langCode) {
                $data = $row->getTranslatedData($langCode);
                $da = (int) ($data['da'] ?? 0);
                [$min, $max] = explode('-', $filterDA);
                return $da >= (int) $min && $da <= (int) $max;
            });
        }

        // Apply DR filter (numeric range)
        if ($filterDR) {
            $rows = $rows->filter(function ($row) use ($filterDR, $langCode) {
                $data = $row->getTranslatedData($langCode);
                $dr = (int) ($data['dr'] ?? 0);
                [$min, $max] = explode('-', $filterDR);
                return $dr >= (int) $min && $dr <= (int) $max;
            });
        }

        // Apply Disclaimer filter (exact match)
        if ($filterDisclaimer) {
            $rows = $rows->filter(function ($row) use ($filterDisclaimer, $langCode) {
                $data = $row->getTranslatedData($langCode);
                $disclaimer = $data['disclaimer'] ?? '';
                return strcasecmp($disclaimer, $filterDisclaimer) === 0;
            });
        }

        // Apply Backlink filter (Yes = all except No, No = only No)
        if ($filterBacklink) {
            $rows = $rows->filter(function ($row) use ($filterBacklink, $langCode) {
                $data = $row->getTranslatedData($langCode);
                $backlink = strtolower(trim($data['backlink'] ?? ''));

                if (strtolower($filterBacklink) === 'yes') {
                    // Show all rows that have backlink (anything except "no" or empty)
                    return $backlink !== 'no' && $backlink !== '';
                } else {
                    // Show only rows with "No"
                    return $backlink === 'no';
                }
            });
        }

        // Apply Indexing filter (exact match)
        if ($filterIndexing) {
            $rows = $rows->filter(function ($row) use ($filterIndexing, $langCode) {
                $data = $row->getTranslatedData($langCode);
                $indexing = $data['indexing'] ?? '';
                return strcasecmp($indexing, $filterIndexing) === 0;
            });
        }

        // Apply sorting
        if ($sortBy) {
            if ($sortBy === 'price_high_low') {
                $rows = $rows->sortByDesc(function ($row) use ($langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return (float) ($data['price'] ?? 0);
                });
            } elseif ($sortBy === 'price_low_high') {
                $rows = $rows->sortBy(function ($row) use ($langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return (float) ($data['price'] ?? 0);
                });
            } elseif ($sortBy === 'recently_added') {
                $rows = $rows->sortByDesc('created_at');
            } elseif ($sortBy === 'z_a') {
                // Determine the first sorting column (usually Name)
                $firstColumn = $plan->columns()->ordered()->first();
                $sortColumn = $firstColumn ? $firstColumn->slug : 'id';
                $rows = $rows->sortByDesc(function ($row) use ($sortColumn, $langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return $data[$sortColumn] ?? '';
                }, SORT_NATURAL | SORT_FLAG_CASE);
            } elseif ($sortBy === 'a_z') {
                // Determine the first sorting column (usually Name)
                $firstColumn = $plan->columns()->ordered()->first();
                $sortColumn = $firstColumn ? $firstColumn->slug : 'id';
                $rows = $rows->sortBy(function ($row) use ($sortColumn, $langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return $data[$sortColumn] ?? '';
                }, SORT_NATURAL | SORT_FLAG_CASE);
            } else {
                // Fallback to generic sort if passed directly (e.g. from column click)
                $sortDir = request('sort_dir', 'asc');
                $rows = $rows->sortBy(function ($row) use ($sortBy, $langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return $data[$sortBy] ?? '';
                }, SORT_NATURAL | SORT_FLAG_CASE, $sortDir === 'desc');
            }
        } else {
            // Default sort (by order) is applied by default in query, but if we need a safe fallback:
            $rows = $rows->sortBy('order');
        }

        // Paginate manually
        $perPage = 50;
        $page = request('page', 1);
        $total = $rows->count();

        $paginatedRows = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedRows;
    }

    /**
     * Get filtered rows for a media-type package
     */
    protected function getFilteredRowsForPackage(Package $package, $columns, string $langCode)
    {
        $query = $package->rows()->with('translations.language');

        $search = request('search');
        $sortBy = request('sort_by');
        // $sortDir = request('sort_dir', 'asc');

        // Get all rows first (for filtering by translated content)
        $rows = $query->get();

        // Apply search filter
        if ($search) {
            $rows = $rows->filter(function ($row) use ($search, $langCode) {
                $data = $row->getTranslatedData($langCode);
                foreach ($data as $value) {
                    if (is_string($value) && stripos($value, $search) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Apply dynamic filters based on filterable columns
        $filterableColumns = $columns->where('is_filterable', true);

        foreach ($filterableColumns as $column) {
            $filterKey = 'filter_' . $column->slug;
            $filterValue = request($filterKey);

            if (!$filterValue) {
                continue;
            }

            $rows = $rows->filter(function ($row) use ($column, $filterValue, $langCode) {
                $data = $row->getTranslatedData($langCode);
                $cellValue = $data[$column->slug] ?? '';

                // Handle based on column type
                if ($column->type === 'number') {
                    // Numeric range filter (e.g., "0-20", "21-40")
                    $numValue = (int) $cellValue;
                    if (strpos($filterValue, '-') !== false) {
                        [$min, $max] = explode('-', $filterValue);
                        return $numValue >= (int) $min && $numValue <= (int) $max;
                    }
                    return $numValue == (int) $filterValue;
                } elseif ($column->type === 'dropdown') {
                    // Exact match for dropdown values
                    return strcasecmp($cellValue, $filterValue) === 0;
                } else {
                    // Text contains match
                    return stripos($cellValue, $filterValue) !== false;
                }
            });
        }

        // Apply sorting
        if ($sortBy) {
            if ($sortBy === 'price_high_low') {
                $rows = $rows->sortByDesc(function ($row) use ($langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return (float) ($data['price'] ?? 0);
                });
            } elseif ($sortBy === 'price_low_high') {
                $rows = $rows->sortBy(function ($row) use ($langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return (float) ($data['price'] ?? 0);
                });
            } elseif ($sortBy === 'recently_added') {
                $rows = $rows->sortByDesc('created_at');
            } elseif ($sortBy === 'z_a') {
                // Determine the first sorting column (usually Name)
                $firstColumn = $columns->first();
                $sortColumn = $firstColumn ? $firstColumn->slug : 'id';
                $rows = $rows->sortByDesc(function ($row) use ($sortColumn, $langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return $data[$sortColumn] ?? '';
                }, SORT_NATURAL | SORT_FLAG_CASE);
            } elseif ($sortBy === 'a_z') {
                // Determine the first sorting column (usually Name)
                $firstColumn = $columns->first();
                $sortColumn = $firstColumn ? $firstColumn->slug : 'id';
                $rows = $rows->sortBy(function ($row) use ($sortColumn, $langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return $data[$sortColumn] ?? '';
                }, SORT_NATURAL | SORT_FLAG_CASE);
            } else {
                // Fallback to generic sort
                $sortDir = request('sort_dir', 'asc');
                $rows = $rows->sortBy(function ($row) use ($sortBy, $langCode) {
                    $data = $row->getTranslatedData($langCode);
                    return $data[$sortBy] ?? '';
                }, SORT_REGULAR, $sortDir === 'desc');
            }
        } else {
            $rows = $rows->sortBy('order');
        }

        // Paginate manually
        $perPage = 50;
        $page = request('page', 1);
        $total = $rows->count();

        $paginatedRows = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedRows;
    }
}
