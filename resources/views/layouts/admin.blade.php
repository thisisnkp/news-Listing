<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ App\Models\SiteSetting::get('site_name', 'SmartTable CMS') }}</title>

    @if($favicon = App\Models\SiteSetting::getFavicon())
    <link rel="icon" href="{{ $favicon }}">
    @endif

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #9899ac;
            --sidebar-active: #ffffff;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand img {
            max-height: 35px;
            margin-right: 10px;
        }

        .sidebar-brand span {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - var(--header-height));
            overflow-y: auto;
        }

        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link:hover {
            color: var(--sidebar-active);
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-nav .nav-link.active {
            color: var(--sidebar-active);
            background: rgba(255, 255, 255, 0.08);
            border-left-color: var(--primary-color);
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1rem;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1.5rem 1.5rem 0.5rem;
            font-weight: 600;
        }

        /* Main Content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Header */
        .top-header {
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-search {
            max-width: 300px;
        }

        .header-search input {
            border-radius: 20px;
            padding-left: 40px;
            background: #f5f7fb;
            border: none;
        }

        .header-search i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9899ac;
        }

        /* Content Area */
        .content-area {
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* Stats Cards */
        .stats-card {
            border-radius: 12px;
            padding: 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .stats-card.bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stats-card.bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stats-card.bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stats-card.bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stats-card .icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.3;
        }

        .stats-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stats-card p {
            margin-bottom: 0;
            opacity: 0.9;
        }

        /* Table Styles */
        .table th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-top: none;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        /* Toggle Switch */
        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }
        }

        /* Page Title */
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e1e2d;
            margin-bottom: 0;
        }

        .breadcrumb {
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
        }

        /* Pagination Styles */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            color: var(--primary-color);
            border-color: #dee2e6;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            color: var(--primary-hover);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
        }

        /* Fix SVG icons in pagination */
        .pagination svg {
            width: 1rem;
            height: 1rem;
            display: inline-block;
            vertical-align: middle;
        }

        .pagination .page-link svg {
            fill: currentColor;
        }

        /* Tailwind pagination fixes for admin panel */
        nav[role="navigation"] {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        nav[role="navigation"]>div {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        nav[role="navigation"] span.inline-flex,
        nav[role="navigation"] a.inline-flex {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            color: #495057;
            background-color: #fff;
            text-decoration: none;
        }

        nav[role="navigation"] a.inline-flex:hover {
            background-color: #e9ecef;
        }

        nav[role="navigation"] span[aria-current="page"] span {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }

        nav[role="navigation"] span[aria-disabled="true"] span {
            color: #6c757d;
            cursor: not-allowed;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            @if($logo = App\Models\SiteSetting::getLogo())
            <img src="{{ $logo }}" alt="Logo">
            @else
            <i class="fas fa-table fa-lg text-primary me-2"></i>
            @endif
            <span>{{ App\Models\SiteSetting::get('site_name', 'SmartTable') }}</span>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>

                <div class="nav-section-title">Package Management</div>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.packages.*') || request()->routeIs('admin.plans.*') ? 'active' : '' }}" href="{{ route('admin.packages.index') }}">
                        <i class="fas fa-box"></i>
                        Packages
                    </a>
                </li>

                <div class="nav-section-title">Settings</div>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.languages.*') ? 'active' : '' }}" href="{{ route('admin.languages.index') }}">
                        <i class="fas fa-globe"></i>
                        Languages
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog"></i>
                        Site Settings
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <button class="btn btn-link d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="d-flex align-items-center ms-auto">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-dark text-decoration-none" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        {{ auth()->user()->name ?? 'Admin' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-area">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // CSRF Token for AJAX
        window.csrfToken = '{{ csrf_token() }}';
    </script>

    @stack('scripts')
</body>

</html>