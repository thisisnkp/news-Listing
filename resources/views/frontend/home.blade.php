<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PR Pricing - RV Rising Media</title>
    <meta name="description" content="{{ App\Models\SiteSetting::get('meta_description', 'Explore our PR and media packages') }}">

    @if($favicon = App\Models\SiteSetting::getFavicon())
    <link rel="icon" href="{{ $favicon }}">
    @endif

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --header-bg: #1A1A2E;
            --button-bg: #E94560;
            --button-hover: #d63854;
            --body-bg: #e6e6e6;
            --card-bg: #ffffff;
            --text-main: #333333;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        html,
        body {
            overflow-x: hidden;
            max-width: 100%;
        }

        body {
            background: var(--body-bg);
            min-height: 100vh;
        }

        /* Header */
        .site-header {
            background: var(--header-bg);
            padding: 1rem 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            margin-left: 1rem;
            font-weight: 500;
        }

        .nav-link-custom:hover {
            color: white;
            text-decoration: underline;
        }

        /* Hero */
        .hero-section {
            background: transparent;
            padding: 3rem 0;
            text-align: center;
        }

        .hero-section h1 {
            font-weight: 700;
            font-size: 2.25rem;
            color: var(--header-bg);
            margin-bottom: 0.75rem;
        }

        .hero-section p {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Main Content */
        .main-content {
            padding: 2rem 0;
        }

        /* Package Cards */
        .package-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: none;
        }

        .package-card:hover {
            transform: translateY(-5px);
        }

        .package-card-header {
            background-color: var(--header-bg);
            color: white;
            padding: 0.5rem 0.5rem;
            text-align: center;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .package-card-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .package-card-body {
            padding: .5rem .5rem 1.5rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
        }

        .package-card .remark {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            flex-grow: 1;
        }

        .package-meta {
            margin-bottom: 1.5rem;
        }

        .package-meta .badge {
            background: #f8f9fa;
            color: var(--text-main);
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border: 1px solid #eee;
        }

        .package-btn {
            background: var(--button-bg);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background-color 0.3s;
            font-size: 0.8rem;
            width: auto;
            min-width: 140px;
        }

        .package-btn:hover {
            background: var(--button-hover);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            border: 1px solid #eee;
        }

        /* Footer */
        .site-footer {
            margin-top: 3rem;
            text-align: center;
            color: #6c757d;
            padding: 2rem 0;
            background: transparent;
        }

        .site-footer a {
            color: var(--header-bg);
            text-decoration: none;
        }

        /* Language Dropdown */
        .lang-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 4px;
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }

        .lang-btn:hover {
            border-color: white;
            color: white;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <!-- Header -->
    @include('frontend.partials.header')

    <!-- Hero -->
    <!-- <section class="hero-section">
        <div class="container">
            <h1>PR & Media Pricing</h1>
            <p>Explore our packages and find the perfect solution for your PR and media needs</p>
        </div>
    </section> -->

    <!-- Main Content -->
    <section class="container main-content p-1 mt-2">
        @if($packages->count() > 0)
        <div class="row g-4 justify-content-center">
            @foreach($packages as $package)
            <div class="col-6 col-lg-4 col-xl-3">
                <div class="package-card">
                    <div class="package-card-header">
                        <h3>{{ $package->name }}</h3>
                    </div>

                    <div class="package-card-body">
                        @if($package->remark)
                        <p class="remark">{{ $package->remark }}</p>
                        @else
                        <p class="remark">Discover the perfect plan for your needs.</p>
                        @endif

                        <div class="package-meta">
                            @if($package->isMedia())
                            <span class="badge"><i class="fas fa-table me-1"></i>Media List</span>
                            @else
                            <span class="badge"><i class="fas fa-layer-group me-1"></i>{{ $package->plans_count }} {{ Str::plural('Plan', $package->plans_count) }}</span>
                            @endif
                        </div>

                        <a href="{{ route('package.show', $package->slug) }}" class="package-btn">
                            View Details <i class="fa-solid fa-circle-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No Packages Available</h3>
            <p>Check back later for new packages.</p>
        </div>
        @endif
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
</body>

</html>