<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    @php
        $entity = $plan ?? $table;
        $package = $entity->package ?? null;
    @endphp
    <title>{{ $entity->name }} | RV Rising Media</title>
    <meta name="description" content="{{ $entity->description ?? 'View ' . $entity->name }}">

    @include('frontend.partials.head')

    {{-- Alpine.js for inline filter/sort reactivity --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { background: #f7f8fa; }

        /* Unified toolbar — title + filters live in ONE compact card */
        .main-content { padding: 16px 0 36px; }

        .page-toolbar {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            margin-bottom: 12px;
            position: relative;
            overflow: hidden;
        }
        .page-toolbar::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--gradient);
            border-radius: var(--radius-lg) 0 0 var(--radius-lg);
        }

        .page-toolbar-head {
            padding: 11px 18px 10px 22px;
        }
        .page-toolbar-head .crumb {
            font-size: 11.5px;
            color: var(--gray);
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }
        .page-toolbar-head .crumb a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .page-toolbar-head .crumb a:hover { text-decoration: underline; }
        .page-toolbar-head h1 {
            color: var(--dark);
            font-size: clamp(1.15rem, 2.4vw, 1.45rem);
            line-height: 1.2;
            margin: 0;
        }
        .page-toolbar-head .text-muted {
            font-size: 0.82rem;
            margin: 3px 0 0 !important;
            color: var(--gray);
        }

        .plan-meta {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 6px;
        }
        .plan-meta .meta-badge {
            background: #fef3f4;
            color: var(--primary-dark);
            border: 1px solid rgba(230,57,70,0.18);
            padding: 3px 10px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.74rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            line-height: 1.4;
        }
        .plan-meta .price-badge {
            background: var(--gradient-accent);
            color: var(--dark);
            border-color: transparent;
        }

        .page-toolbar-divider {
            height: 1px;
            background: var(--border);
            margin-left: 18px;
        }

        .page-toolbar-filters {
            padding: 9px 18px 11px 22px;
        }
        .page-toolbar-filters .row > [class*="col-"] { padding-top: 4px; padding-bottom: 4px; }
        .filters-card .search-box { position: relative; }
        .filters-card .search-box i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); color: var(--gray);
        }
        .filters-card .search-input {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px 10px 38px;
            width: 100%;
            font-size: 0.92rem;
            font-family: 'Poppins', sans-serif;
        }
        .filters-card .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(230,57,70,0.12);
        }
        .filters-card .form-select {
            font-family: 'Poppins', sans-serif;
            font-size: 0.88rem;
            border-color: var(--border);
        }
        .filters-card .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(230,57,70,0.12);
        }
        .filter-btn {
            background: var(--light);
            border: 1px solid var(--border);
            color: var(--dark);
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .filter-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .btn-order {
            background: var(--gradient);
            color: var(--white);
            border: none;
            border-radius: 50px;
            padding: 9px 22px;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            box-shadow: 0 6px 18px rgba(230,57,70,0.3);
        }
        .btn-order:hover {
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(230,57,70,0.4);
        }
        .btn-action {
            background: var(--gradient);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }
        .btn-action:hover { color: var(--white); transform: translateY(-2px); }

        /* Data Table */
        .table-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }
        .table-scroll {
            overflow-x: auto;
            max-height: 75vh;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .table-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
        .table-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .table-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1; border-radius: 4px;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.93rem;
            font-family: 'Poppins', sans-serif;
        }
        .data-table thead {
            background: var(--gradient-dark);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .data-table th {
            padding: 14px 18px;
            text-align: left;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--white);
            border: none;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            border-right: 1px solid rgba(255,255,255,0.15);
            white-space: nowrap;
        }
        .data-table th:last-child { border-right: none; }
        .data-table th:first-child {
            position: sticky;
            left: 0;
            z-index: 11;
            background: inherit;
            box-shadow: 4px 0 6px -4px rgba(0,0,0,0.1);
        }
        .data-table th:first-child::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gradient-dark);
            z-index: -1;
        }

        .data-table tbody tr {
            transition: var(--transition);
            background: var(--white);
        }
        .data-table tbody tr:nth-child(even) { background: #f8fafc; }
        .data-table tbody tr:hover {
            background: #fef3f4;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(230,57,70,0.08);
            z-index: 5;
            position: relative;
        }
        .data-table td {
            padding: 14px 18px;
            color: #334155;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }
        .data-table td:last-child { border-right: none; }
        .data-table tbody tr:last-child td { border-bottom: none; }

        .col-name { padding-left: 16px !important; min-width: 320px; font-weight: 600; }
        .col-remark { min-width: 240px; }
        .col-price, .col-backlink, .col-dr { text-align: center !important; }
        .data-table th.col-price,
        .data-table th.col-backlink,
        .data-table th.col-dr { text-align: center !important; }

        .data-table td:first-child {
            position: sticky;
            left: 0;
            z-index: 5;
            background: inherit;
            font-weight: 600;
            color: var(--dark);
            box-shadow: 4px 0 6px -4px rgba(0,0,0,0.05);
        }
        .data-table tbody tr:hover td:first-child {
            border-left: 4px solid var(--primary);
            padding-left: calc(16px - 4px);
            color: var(--primary);
        }

        .price-cell { color: var(--primary-dark); font-weight: 700; }

        /* Pagination */
        .pagination-wrapper {
            padding: 16px;
            display: flex;
            justify-content: center;
            border-top: 1px solid var(--border);
        }
        .pagination .page-link {
            border: 1px solid var(--border);
            color: var(--dark);
            border-radius: 6px;
            margin: 0 2px;
            padding: 6px 12px;
            font-size: 0.88rem;
        }
        .pagination .page-link:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--gray);
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 14px;
            opacity: 0.5;
            color: var(--primary);
        }

        /* Loading */
        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }
        .spinner {
            width: 42px;
            height: 42px;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 768px) {
            .col-name {
                min-width: 45vw !important;
                width: 45vw !important;
                max-width: 45vw !important;
                white-space: normal;
                word-wrap: break-word;
            }
            .data-table th, .data-table td {
                padding: 10px 8px;
                font-size: 0.82rem;
            }
        }
    </style>
</head>

<body x-data="tableFilter()">
    @include('frontend.partials.header')

    <main class="site-main">
        <section class="container main-content">
            @php
                $enabledFilters = $entity->enabled_filters ?? ['da', 'dr', 'disclaimer', 'backlinks', 'indexing', 'sort_az', 'sort_za'];
            @endphp

            {{-- Unified toolbar: title + meta + filters in one card --}}
            <div class="page-toolbar">
                <div class="page-toolbar-head">
                    <div class="crumb">
                        <a href="{{ url('/') }}">Pricing</a>
                        @if($package)
                            &nbsp;/&nbsp; <a href="{{ route('package.show', $package->slug) }}">{{ $package->name }}</a>
                        @endif
                        @if(!$package || trim($entity->name) !== trim($package->name))
                            &nbsp;/&nbsp; {{ $entity->name }}
                        @endif
                    </div>
                    <h1>{{ $entity->name }}</h1>
                    @if($entity->description)
                        <p class="text-muted mb-1">{{ $entity->description }}</p>
                    @endif
                    @if($entity->price || !empty($entity->services))
                        <div class="plan-meta">
                            @if($entity->price)
                                <span class="meta-badge price-badge"><i class="fas fa-tag"></i> &#8377;{{ number_format($entity->price, 0) }}</span>
                            @endif
                            @if(!empty($entity->services))
                                <span class="meta-badge"><i class="fas fa-check"></i> {{ count($entity->services) }} Services</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="page-toolbar-divider"></div>

                <div class="page-toolbar-filters">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-4">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search..." x-model="search"
                                @input.debounce.300ms="filterTable()">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="d-flex flex-nowrap flex-md-wrap gap-2 justify-content-between justify-content-lg-end align-items-center">
                            <select class="form-select w-auto flex-grow-1 flex-md-grow-0" x-model="sortBy" @change="handleSortChange()">
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
                                <a href="{{ route('plan.export', $entity->slug) }}?lang={{ $currentLang }}" class="filter-btn" title="Export">
                                    <i class="fas fa-download"></i><span class="d-none d-md-inline ms-1">Export</span>
                                </a>
                            </div>

                            @if($entity->order_button_link)
                                <a href="{{ $entity->order_button_link }}" class="btn-order d-none d-md-inline-flex" target="_blank">
                                    <i class="fas fa-shopping-cart"></i> Order Now
                                </a>
                                <a href="{{ $entity->order_button_link }}" class="btn-action d-inline-flex d-md-none" target="_blank">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Dropdown Filters --}}
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
                </div>{{-- /.page-toolbar-filters --}}
            </div>{{-- /.page-toolbar --}}

            {{-- Table --}}
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
                                    <th class="col-{{ $column->slug }}">{{ $column->name }}</th>
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
    </main>

    @include('frontend.partials.footer')

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

                handleSortChange() { this.filterTable(); },
                setSort(column, direction = null) { /* sorting handled via dropdown */ },

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
