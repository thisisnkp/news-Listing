<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>{{ $package->name }} | RV Rising Media</title>
    <meta name="description" content="{{ $package->remark ?? 'View plans for ' . $package->name }}">

    @include('frontend.partials.head')

    <style>
        body { background: #f7f8fa; }

        .package-hero {
            background: var(--gradient-dark);
            color: var(--white);
            padding: 60px 0 80px;
            position: relative;
            overflow: hidden;
        }
        .package-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 30%, rgba(230,57,70,0.18), transparent 55%),
                        radial-gradient(circle at 80% 70%, rgba(255,183,3,0.12), transparent 55%);
            pointer-events: none;
        }
        .package-hero .container { position: relative; z-index: 1; text-align: center; }
        .package-hero .crumb {
            font-size: 13px;
            opacity: 0.75;
            margin-bottom: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .package-hero .crumb a {
            color: var(--accent);
        }
        .package-hero h1 {
            color: var(--white);
            font-size: clamp(1.75rem, 4vw, 2.6rem);
            margin-bottom: 12px;
        }
        .package-hero p {
            color: rgba(255,255,255,0.8);
            max-width: 640px;
            margin: 0 auto;
        }

        .plans-section {
            padding: 50px 0 80px;
            margin-top: -40px;
            position: relative;
            z-index: 2;
        }

        .plan-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border);
        }
        .plan-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(230,57,70,0.25);
        }
        .plan-card-header {
            background: var(--gradient);
            color: var(--white);
            padding: 16px 14px;
            text-align: center;
        }
        .plan-card-header h3 {
            color: var(--white);
            font-family: 'Fraunces', serif;
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
        }
        .plan-card-body {
            padding: 22px 18px 24px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: var(--white);
        }
        .plan-price {
            font-family: 'Fraunces', serif;
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
            line-height: 1;
        }
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 0 0 18px;
            text-align: center;
            width: 100%;
        }
        .plan-features li {
            padding: 5px 0;
            font-size: 0.88rem;
            color: var(--gray);
        }
        .plan-features li.more {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.82rem;
        }
        .plan-btn {
            background: var(--gradient);
            color: var(--white);
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            margin-top: auto;
            border: none;
            font-size: 0.85rem;
            box-shadow: 0 6px 18px rgba(230,57,70,0.3);
            white-space: nowrap;
        }
        .plan-btn:hover {
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(230,57,70,0.4);
        }

        @media (max-width: 768px) {
            .package-hero { padding: 50px 0 70px; }
            .plans-section { padding: 30px 0 60px; }
        }
    </style>
</head>

<body>
    @include('frontend.partials.header')

    <main class="site-main">
        <section class="package-hero">
            <div class="container">
                <div class="crumb">
                    <a href="{{ url('/') }}">Pricing</a> &nbsp;/&nbsp; {{ $package->name }}
                </div>
                <h1>{{ $package->name }}</h1>
                @if($package->remark)
                    <p>{{ $package->remark }}</p>
                @endif
            </div>
        </section>

        <section class="plans-section">
            <div class="container">
                @if($package->plans->count() > 0)
                    <div class="row g-4 justify-content-center">
                        @foreach($package->plans as $plan)
                            <div class="col-6 col-md-6 col-lg-4 col-xl-3">
                                <div class="plan-card">
                                    <div class="plan-card-header">
                                        <h3>{{ $plan->name }}</h3>
                                    </div>
                                    <div class="plan-card-body">
                                        @if($plan->price)
                                            <div class="plan-price">&#8377;{{ number_format($plan->price, 0) }}</div>
                                        @endif

                                        @if(!empty($plan->services))
                                            <ul class="plan-features">
                                                @foreach(array_slice($plan->services, 0, 3) as $service)
                                                    <li>{{ $service }}</li>
                                                @endforeach
                                                @if(count($plan->services) > 3)
                                                    <li class="more">+{{ count($plan->services) - 3 }} more</li>
                                                @endif
                                            </ul>
                                        @endif

                                        <a href="{{ route('plan.show', $plan->slug) }}" class="plan-btn">
                                            View Plan <i class="fa-solid fa-circle-arrow-right"></i>
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
        </section>
    </main>

    @include('frontend.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
