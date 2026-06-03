<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>PR Pricing | RV Rising Media</title>
    <meta name="description" content="{{ App\Models\SiteSetting::get('meta_description', 'Explore our PR and media packages — transparent pricing from RV Rising Media.') }}">

    @include('frontend.partials.head')

    <style>
        /* Page-specific overrides — uses main brand vars from style.css (--primary, --accent, --dark, etc.) */
        body { background: #f7f8fa; }

        .pricing-hero {
            background: var(--gradient-dark);
            color: var(--white);
            text-align: center;
            padding: 70px 0 90px;
            position: relative;
            overflow: hidden;
        }
        .pricing-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 30%, rgba(230,57,70,0.18), transparent 55%),
                        radial-gradient(circle at 80% 70%, rgba(255,183,3,0.12), transparent 55%);
            pointer-events: none;
        }
        .pricing-hero .container { position: relative; z-index: 1; }
        .pricing-hero .eyebrow {
            display: inline-block;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            letter-spacing: 1px;
            margin-bottom: 18px;
            color: var(--accent);
            font-weight: 600;
            text-transform: uppercase;
        }
        .pricing-hero h1 {
            color: var(--white);
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 14px;
        }
        .pricing-hero p {
            color: rgba(255,255,255,0.8);
            max-width: 640px;
            margin: 0 auto;
            font-size: 1.05rem;
        }

        .packages-section {
            padding: 60px 0 80px;
            margin-top: -40px;
            position: relative;
            z-index: 2;
        }

        .pkg-card {
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
        .pkg-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(230,57,70,0.25);
        }
        .pkg-card-header {
            background: var(--gradient);
            color: var(--white);
            padding: 16px 14px;
            text-align: center;
        }
        .pkg-card-header h3 {
            color: var(--white);
            font-family: 'Fraunces', serif;
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
        }
        .pkg-card-body {
            padding: 22px 18px 24px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: var(--white);
        }
        .pkg-card .remark {
            color: var(--gray);
            font-size: 0.93rem;
            margin-bottom: 18px;
            line-height: 1.55;
            flex-grow: 1;
        }
        .pkg-meta {
            margin-bottom: 18px;
        }
        .pkg-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef3f4;
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 0.82rem;
            padding: 6px 14px;
            border: 1px solid rgba(230,57,70,0.18);
            border-radius: 50px;
        }
        .pkg-btn {
            background: var(--gradient);
            color: var(--white);
            border: none;
            border-radius: 50px;
            padding: 11px 20px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            font-size: 0.85rem;
            box-shadow: 0 6px 18px rgba(230,57,70,0.3);
            white-space: nowrap;
        }
        .pkg-btn:hover {
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(230,57,70,0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 24px;
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .empty-state i { font-size: 3rem; color: var(--primary); opacity: 0.6; margin-bottom: 14px; }
        .empty-state h3 { margin-bottom: 6px; }

        @media (max-width: 768px) {
            .pricing-hero { padding: 50px 0 70px; }
            .packages-section { padding: 40px 0 60px; }
        }
    </style>
</head>

<body>
    @include('frontend.partials.header')

    <main class="site-main">
        <section class="pricing-hero">
            <div class="container">
                <span class="eyebrow"><i class="fas fa-tag"></i> Transparent Pricing</span>
                <h1>PR &amp; Media Packages</h1>
                <p>Pick the package that fits your goals — every plan is backed by RV Rising Media's verified network of publishers, influencers and journalists.</p>
            </div>
        </section>

        <section class="packages-section">
            <div class="container">
                @if($packages->count() > 0)
                    <div class="row g-4 justify-content-center">
                        @foreach($packages as $package)
                            <div class="col-6 col-md-6 col-lg-4 col-xl-3">
                                <div class="pkg-card">
                                    <div class="pkg-card-header">
                                        <h3>{{ $package->name }}</h3>
                                    </div>
                                    <div class="pkg-card-body">
                                        <p class="remark">{{ $package->remark ?: 'Discover the perfect plan for your needs.' }}</p>

                                        <div class="pkg-meta">
                                            @if($package->isMedia())
                                                <span class="pkg-badge"><i class="fas fa-table"></i> Media List</span>
                                            @else
                                                <span class="pkg-badge"><i class="fas fa-layer-group"></i> {{ $package->plans_count }} {{ Str::plural('Plan', $package->plans_count) }}</span>
                                            @endif
                                        </div>

                                        <a href="{{ route('package.show', $package->slug) }}" class="pkg-btn">
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
            </div>
        </section>
    </main>

    @include('frontend.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
