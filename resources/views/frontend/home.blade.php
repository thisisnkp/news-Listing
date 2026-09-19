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

        /* Pill cloud section (mirrors the pr-services "Capability Index") */
        .pricing-cloud {
            margin-top: -44px;
            position: relative;
            z-index: 2;
            padding-top: 56px;
            border-radius: 28px 28px 0 0;
        }
        /* ===== Explore — category selector grid ===== */
        .pricing-cats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            max-width: 940px;
            margin: 0 auto;
        }
        .pricing-cat {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: 16px;
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            font-size: 15px;
            position: relative;
            overflow: hidden;
            transition: transform .25s ease, box-shadow .3s ease, border-color .3s ease;
        }
        /* Accent bar slides up on hover */
        .pricing-cat::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--gradient);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform .3s ease;
        }
        .pricing-cat:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(230, 57, 70, 0.14);
            border-color: rgba(230, 57, 70, 0.35);
            color: var(--dark);
        }
        .pricing-cat:hover::before { transform: scaleY(1); }
        .pricing-cat-icon {
            flex-shrink: 0;
            width: 46px; height: 46px;
            display: grid; place-items: center;
            border-radius: 13px;
            background: rgba(230, 57, 70, 0.10);
            color: var(--primary);
            font-size: 1.05rem;
            transition: background .3s ease, color .3s ease, transform .35s ease;
        }
        .pricing-cat:hover .pricing-cat-icon {
            background: var(--gradient);
            color: var(--white);
            transform: rotate(-6deg) scale(1.08);
        }
        .pricing-cat-label { flex-grow: 1; line-height: 1.3; }
        .pricing-cat-arrow {
            flex-shrink: 0;
            color: var(--primary);
            font-size: .85rem;
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity .3s ease, transform .3s ease;
        }
        .pricing-cat:hover .pricing-cat-arrow { opacity: 1; transform: translateX(0); }

        @media (max-width: 768px) {
            .pricing-hero { padding: 50px 0 70px; }
            .pricing-cloud { padding-top: 40px; padding-bottom: 56px; }
            .pricing-cats { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .pricing-cat { padding: 13px 14px; font-size: 13.5px; gap: 10px; }
            .pricing-cat-icon { width: 40px; height: 40px; font-size: .95rem; }
            .pricing-cat-arrow { display: none; }
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

        <section class="pr-cap pricing-cloud">
            <div class="container">
                <div class="section-head">
                    <span class="section-tag">Explore</span>
                    <h2>Our <span class="pr-cap-accent">PR &amp; Media Pricing</span></h2>
                    <p>Tap a category to view its detailed pricing, packages and media lists.</p>
                </div>

                <div class="pricing-cats">
                    @forelse($pricingButtons as $btn)
                        <a href="{{ $btn->url }}" class="pricing-cat"@if($btn->new_tab) target="_blank" rel="noopener"@endif>
                            <span class="pricing-cat-icon"><i class="{{ $btn->icon ?: 'fas fa-layer-group' }}"></i></span>
                            <span class="pricing-cat-label">{{ $btn->label }}</span>
                            <i class="fas fa-arrow-right pricing-cat-arrow"></i>
                        </a>
                    @empty
                        {{-- No admin buttons yet → fall back to the public packages --}}
                        @forelse($packages as $package)
                            <a href="{{ route('package.show', $package->slug) }}" class="pricing-cat">
                                <span class="pricing-cat-icon"><i class="fas {{ $package->isMedia() ? 'fa-table' : 'fa-layer-group' }}"></i></span>
                                <span class="pricing-cat-label">{{ $package->name }}</span>
                                <i class="fas fa-arrow-right pricing-cat-arrow"></i>
                            </a>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h3>Nothing here yet</h3>
                                <p>Add buttons from the admin panel, or check back later.</p>
                            </div>
                        @endforelse
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    @include('frontend.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
