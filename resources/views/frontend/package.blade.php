<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $package->name }} - RV Rising Media</title>
    <meta name="description" content="{{ $package->remark ?? 'View plans for ' . $package->name }}">

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
            /* Deep Navy from Home Page */
            --button-bg: #E94560;
            /* Reddish-Pink from Home Page */
            --button-hover: #d63854;
            --body-bg: #e6e6e6;
            /* Light gray background */
            --card-bg: #ffffff;
            --text-main: #333333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            min-height: 100vh;
        }

        /* Header */
        .site-header {
            background: var(--header-bg);
            padding: 1rem 0;
            margin-bottom: 2rem;
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

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem 3rem;
        }

        .page-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--header-bg);
            font-weight: 700;
        }

        /* Reference Card Style */
        .ref-card {
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

        .ref-card:hover {
            transform: translateY(-5px);
        }

        .ref-card-header {
            background-color: var(--header-bg);
            color: white;
            padding: 0.5rem 0.5rem;
            text-align: center;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .ref-card-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .ref-card-body {
            padding: .5rem .5rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
        }

        .ref-description {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .ref-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .ref-meta {
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #555;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Pill Button */
        .ref-btn {
            background-color: var(--button-bg);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 50px;
            /* Pill shape */
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.3s;
            margin-top: auto;
            border: none;
            font-size: .8rem;
        }

        .ref-btn:hover {
            background-color: var(--button-hover);
            color: white;
        }

        .ref-btn-icon {
            font-size: 1.2rem;
        }

        /* List for services if needed, kept hidden/minimal per image style or shown cleanly */
        .ref-features {
            list-style: none;
            padding: 0;
            /* margin: 0 0 1.5rem; */
            text-align: left;
            width: 100%;
        }

        .ref-features li {
            padding: 0.25rem 0;
            font-size: 0.85rem;
            color: #555;
            text-align: center;
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
            border-color: white;
            color: white;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 0 1rem 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    @include('frontend.partials.header')

    <div class="main-container">
        <!-- Breadcrumb equivalent -->
        <!-- <div class="text-center mb-4">
            <span class="text-muted small">Pricing / {{ $package->name }}</span>
        </div> -->

        @if($package->plans->count() > 0)
        <div class="row g-4 justify-content-center p-1">
            @foreach($package->plans as $plan)
            <div class="col-6 col-md-6 col-lg-4 col-xl-3">
                <div class="ref-card">
                    <div class="ref-card-header">
                        <h3>{{ $plan->name }}</h3>
                    </div>
                    <div class="ref-card-body">
                        <!-- <p class="ref-description">
                            {{ $plan->description ?? 'Discover the perfect plan for your needs.' }}
                        </p> -->

                        @if($plan->price)
                        <div class="ref-price">₹{{ number_format($plan->price, 0) }}</div>
                        @else
                        <!-- <div class="ref-meta">
                            <i class="fas fa-cube"></i> Flexible Plan
                        </div> -->
                        @endif

                        @if(!empty($plan->services))
                        <ul class="ref-features">
                            @foreach(array_slice($plan->services, 0, 3) as $service)
                            <li>{{ $service }}</li>
                            @endforeach
                            @if(count($plan->services) > 3)
                            <li class="text-muted small">+{{ count($plan->services) - 3 }} more</li>
                            @endif
                        </ul>
                        @endif

                        <a href="{{ route('plan.show', $plan->slug) }}" class="ref-btn">
                            View Plans <i class="ref-btn-icon fa-solid fa-circle-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <h3>No Plans Available</h3>
        </div>
        @endif
    </div>

    <!-- Simple Footer -->
    <footer class="text-center py-4 text-muted">
        <small>© {{ date('Y') }} RV Rising Media</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>