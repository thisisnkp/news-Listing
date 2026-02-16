<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $entity = $plan ?? $table;
        $package = $entity->package ?? null;
    @endphp
    <title>{{ $entity->name }} - RV Rising Media</title>
    <meta name="description" content="{{ $entity->description ?? 'View ' . $entity->name }}">

    @if($favicon = App\Models\SiteSetting::getFavicon())
        <link rel="icon" href="{{ $favicon }}">
    @endif

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary: #1a1a2e;
            --primary-light: #16213e;
            --accent: #e94560;
            --accent-hover: #d63854;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --text-dark: #1a1a2e;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f5f5;
            min-height: 100vh;
        }

        /* Header */
        .site-header {
            background: var(--primary);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .site-header .logo {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .site-header .logo:hover {
            color: var(--accent);
        }

        .site-header .logo img {
            height: 40px;
        }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-link-custom:hover {
            color: var(--accent);
        }

        /* Page Title */
        .page-title-section {
            background: white;
            padding: .5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title-section h1 {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
        }

        .breadcrumb {
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--accent);
            text-decoration: none;
        }

        .plan-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .plan-meta .badge {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 0.4rem 0.8rem;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .plan-meta .price-badge {
            background: #28a745;
            color: white;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            padding: .5rem 0;
        }

        /* Filters */
        .filters-card {
            background: white;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: .5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
        }

        .search-input {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            width: 100%;
            font-size: 0.9rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.1);
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .filter-btn {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .filter-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        /* Table */
        .table-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 2rem;
        }

        .table-scroll {
            overflow-x: auto;
            max-height: 75vh;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .table-scroll::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }

        .table-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.95rem;
        }

        .data-table thead {
            background: linear-gradient(135deg, var(--primary) 0%, #243b55 100%);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .data-table th {
            padding: 1.1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #ffffff;
            border: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            white-space: nowrap;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .data-table th:last-child {
            border-right: none;
        }

        .data-table th:first-child {
            position: sticky;
            left: 0;
            z-index: 11;
            background: inherit;
            /* Inherit the gradient/color */
            box-shadow: 4px 0 6px -4px rgba(0, 0, 0, 0.1);
            /* Subtle shadow separator */
        }

        /* Ensure gradient continues correctly on sticky header */
        .data-table th:first-child::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--primary) 0%, #1e3246 100%);
            /* Slightly lighter shade match */
            z-index: -1;
        }


        .data-table tbody tr {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f1f5f9;
            /* Slate 100 - Distinct alternate color */
        }

        .data-table tbody tr:hover {
            background: #e2e8f0;
            /* Slate 200 - Clearly darker hover */
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 5;
            position: relative;
        }

        .data-table td {
            padding: 1.1rem 1.5rem;
            color: #334155;
            /* Slate 700 */
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #e2e8f0;
            font-size: 0.925rem;
        }

        .data-table td:last-child {
            border-right: none;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Column Config */
        .col-name {
            padding-left: .7rem !important;
            min-width: 350px;
        }

        .col-remark {
            min-width: 250px;
        }

        .col-price,
        .col-backlink,
        .col-dr {
            text-align: center !important;
        }

        @media (max-width: 768px) {
            .col-name {
                min-width: 45vw !important;
                width: 45vw !important;
                max-width: 45vw !important;
                white-space: normal;
                word-wrap: break-word;
            }
        }

        .data-table td:first-child {
            position: sticky;
            left: 0;
            z-index: 5;
            background: inherit;
            /* Matches row bg */
            font-weight: 600;
            color: var(--primary);
            box-shadow: 4px 0 6px -4px rgba(0, 0, 0, 0.05);
        }

        /* Accent left border on hover */
        .data-table tbody tr:hover td:first-child {
            border-left: 4px solid var(--accent);
            padding-left: calc(1.5rem - 4px);
            color: var(--accent);
        }

        /* Buttons & Actions */
        .btn-action {
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: background 0.2s;
        }

        .btn-action:hover {
            background: var(--accent-hover);
            color: white;
        }

        .btn-order {
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-order:hover {
            background: var(--accent-hover);
            color: white;
            transform: translateY(-2px);
        }

        /* Pagination */
        .pagination-wrapper {
            padding: 1rem;
            display: flex;
            justify-content: center;
            border-top: 1px solid var(--border-color);
        }

        .pagination .page-link {
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            border-radius: 4px;
            margin: 0 2px;
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }

        .pagination .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Footer */
        .site-footer {
            background: var(--primary);
            color: rgba(255, 255, 255, 0.7);
            padding: 1.5rem 0;
            margin-top: 2rem;
        }

        .site-footer a {
            color: var(--accent);
            text-decoration: none;
        }

        /* Loading */
        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border-color);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .lang-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 4px;
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }

        .lang-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .price-cell {
            color: #28a745;
            font-weight: 600;
        }

        /* Ensure headers are also centered if needed, though th usually has specific styles */
        .data-table th.col-price,
        .data-table th.col-backlink,
        .data-table th.col-dr {
            text-align: center !important;
        }

        @media (max-width: 768px) {
            .page-title-section h1 {
                font-size: 1.4rem;
            }

            .data-table th,
            .data-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body x-data="tableFilter()">
    <!-- Header -->
    <!-- Header -->
    @include('frontend.partials.header')

    <!-- Page Title -->
    <section class="page-title-section">
        <div class="container">
            <!-- <nav class="breadcrumb">
                <a href="https://rvrising.com/">Home</a>
                <span class="mx-2">/</span>
                @if($package)
                <a href="{{ route('package.show', $package->slug) }}">{{ $package->name }}</a>
                <span class="mx-2">/</span>
                @endif
                <span>{{ $entity->name }}</span>
            </nav> -->
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1>{{ $entity->name }}</h1>
                    @if($entity->description)
                        <p class="text-muted mb-2">{{ $entity->description }}</p>
                    @endif
                    <div class="plan-meta">
                        @if($entity->price)
                            <span class="badge price-badge">₹{{ number_format($entity->price, 0) }}</span>
                        @endif
                        @if(!empty($entity->services))
                            <span class="badge"><i class="fas fa-check me-1"></i>{{ count($entity->services) }}
                                Services</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="container main-content">
        <!-- Filters -->
        @php
            $enabledFilters = $entity->enabled_filters ?? ['da', 'dr', 'disclaimer', 'backlinks', 'indexing', 'sort_az', 'sort_za'];
        @endphp
        <div class="filters-card">
            <div class="row g-3 align-items-center">
                <div class="col-lg-4">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-input" placeholder="Search..." x-model="search"
                            @input.debounce.300ms="filterTable()">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div
                        class="d-flex flex-nowrap flex-md-wrap gap-2 justify-content-between justify-content-lg-end align-items-center">
                        <select class="form-select w-auto flex-grow-1 flex-md-grow-0" x-model="sortBy"
                            @change="handleSortChange()">
                            <option value="">Sort By</option>
                            <option value="a_z">A-Z</option>
                            <option value="z_a">Z-A</option>
                            <option value="price_high_low">Price High-Low</option>
                            <option value="price_low_high">Price Low-High</option>
                            <option value="recently_added">Recently Added</option>
                        </select>

                        <div class="d-flex gap-2">
                            <button class="filter-btn" @click="resetFilters()" title="Reset">
                                <i class="fas fa-undo"></i><span class="d-none d-md-inline ms-1">Reset</span>
                            </button>
                            <a href="{{ route('plan.export', $entity->slug) }}?lang={{ $currentLang }}"
                                class="filter-btn" title="Export">
                                <i class="fas fa-download"></i><span class="d-none d-md-inline ms-1">Export</span>
                            </a>
                        </div>

                        @if($entity->order_button_link)
                            <a href="{{ $entity->order_button_link }}" class="btn-order d-none d-md-inline-flex"
                                target="_blank">
                                Order Now
                            </a>
                            <a href="{{ $entity->order_button_link }}" class="btn-action d-inline-flex d-md-none"
                                target="_blank">
                                <i class="fas fa-shopping-cart"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dropdown Filters -->
            @if(in_array('da', $enabledFilters) || in_array('dr', $enabledFilters) || in_array('disclaimer', $enabledFilters) || in_array('backlinks', $enabledFilters) || in_array('indexing', $enabledFilters))
                <div class="row g-2 mt-2">
                    @if(in_array('da', $enabledFilters))
                        <div class="col-4 col-md-4 col-lg-2">
                            <select class="form-select form-select-sm" x-model="filterDA" @change="filterTable()">
                                <option value="">All DA</option>
                                <option value="0-20">DA 0-20</option>
                                <option value="21-40">DA 21-40</option>
                                <option value="41-60">DA 41-60</option>
                                <option value="61-80">DA 61-80</option>
                                <option value="81-100">DA 81+</option>
                            </select>
                        </div>
                    @endif
                    @if(in_array('dr', $enabledFilters))
                        <div class="col-4 col-md-4 col-lg-2">
                            <select class="form-select form-select-sm" x-model="filterDR" @change="filterTable()">
                                <option value="">All DR</option>
                                <option value="0-20">DR 0-20</option>
                                <option value="21-40">DR 21-40</option>
                                <option value="41-60">DR 41-60</option>
                                <option value="61-80">DR 61-80</option>
                                <option value="81-100">DR 81+</option>
                            </select>
                        </div>
                    @endif
                    @if(in_array('disclaimer', $enabledFilters))
                        <div class="col-4 col-md-4 col-lg-2">
                            <select class="form-select form-select-sm" x-model="filterDisclaimer" @change="filterTable()">
                                <option value="">Disclaimer</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    @endif
                    @if(in_array('backlinks', $enabledFilters))
                        <div class="col-4 col-md-4 col-lg-2">
                            <select class="form-select form-select-sm" x-model="filterBacklink" @change="filterTable()">
                                <option value="">Backlinks</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    @endif
                    @if(in_array('indexing', $enabledFilters))
                        <div class="col-4 col-md-4 col-lg-2">
                            <select class="form-select form-select-sm" x-model="filterIndexing" @change="filterTable()">
                                <option value="">Indexing</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Table -->
        <div class="table-card" style="position: relative;">
            <template x-if="loading">
                <div class="loading-overlay">
                    <div class="spinner"></div>
                </div>
            </template>

            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            @foreach($entity->columns as $column)
                                <th class="col-{{ $column->slug }}">
                                    {{ $column->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @include('frontend.partials.table-rows', ['table' => $entity])
                    </tbody>
                </table>
            </div>

            @if($rows->count() > 0)
                <div class="pagination-wrapper">
                    {{ $rows->appends(['lang' => $currentLang])->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <p class="mb-0">© {{ date('Y') }} RV Rising Media. All rights reserved.</p>
            <p class="mb-0">
                <a href="https://rvrising.com/about-us/">About</a> ·
                <a href="https://rvrising.com/pr-services/">Services</a> ·
                <a href="https://rvrising.com/payment-details-for-rv-rising-entertainment/">Payment</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function tableFilter() {
            return {
                search: '{{ request("search") }}',
                sortBy: '{{ request("sort_by") }}',
                sortDir: '{{ request("sort_dir", "asc") }}',
                filterDA: '{{ request("filter_da") }}',
                filterDR: '{{ request("filter_dr") }}',
                filterDisclaimer: '{{ request("filter_disclaimer") }}',
                filterBacklink: '{{ request("filter_backlink") }}',
                filterIndexing: '{{ request("filter_indexing") }}',
                loading: false,

                handleSortChange() {
                    this.filterTable();
                },

                setSort(column, direction = null) {
                    // Deprecated: Sorting now handled via dropdown
                    /*
                    if (direction) {
                        this.sortBy = column;
                        this.sortDir = direction;
                    } else {
                        if (this.sortBy === column) {
                            this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                        } else {
                            this.sortBy = column;
                            this.sortDir = 'asc';
                        }
                    }
                    this.filterTable();
                    */
                },

                resetFilters() {
                    this.search = '';
                    this.sortBy = '';
                    this.sortDir = 'asc';
                    this.filterDA = '';
                    this.filterDR = '';
                    this.filterDisclaimer = '';
                    this.filterBacklink = '';
                    this.filterIndexing = '';
                    this.filterTable();
                },

                filterTable() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    params.set('lang', '{{ $currentLang }}');
                    if (this.search) params.set('search', this.search);
                    if (this.sortBy) params.set('sort_by', this.sortBy);
                    if (this.sortDir) params.set('sort_dir', this.sortDir);
                    if (this.filterDA) params.set('filter_da', this.filterDA);
                    if (this.filterDR) params.set('filter_dr', this.filterDR);
                    if (this.filterDisclaimer) params.set('filter_disclaimer', this.filterDisclaimer);
                    if (this.filterBacklink) params.set('filter_backlink', this.filterBacklink);
                    if (this.filterIndexing) params.set('filter_indexing', this.filterIndexing);

                    window.location.href = '{{ route("plan.show", $entity->slug) }}?' + params.toString();
                }
            }
        }
    </script>
</body>

</html>