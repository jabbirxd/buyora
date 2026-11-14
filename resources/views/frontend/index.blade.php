@extends('layouts.app')

@section('title', 'Buyora — Everyday finds, a little more joyful')

@section('content')

    {{-- ============================== HERO ============================== --}}
    <section class="by-hero-wrap">
        <div class="by-hero by-hero--gradient">
            <div class="by-hero-inner">
                <div class="by-hero-copy">
                    <span class="by-eyebrow by-eyebrow--ink">Your everyday marketplace</span>
                    <h1>Everyday finds,<br>a little more <span>joyful.</span></h1>
                    <p>
                        Discover trendsetting products, honest prices and effortless delivery — from high-tech gadgets
                        to the latest fashions, all in one cheerful place.
                    </p>
                    <div class="by-hero-cta">
                        <a href="{{ url('/collections') }}" class="btn btn-primary">
                            Start shopping <i class="fa-solid fa-shopping-bag"></i>
                        </a>
                        <a href="{{ url('/new-arrivals') }}" class="btn by-btn-ghost">
                            New arrivals <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="by-hero-trust">
                        <span><i class="fa-solid fa-truck-fast"></i> Nationwide delivery</span>
                        <span><i class="fa-solid fa-shield-halved"></i> Secure checkout</span>
                        <span><i class="fa-solid fa-headset"></i> 24/7 support</span>
                    </div>
                </div>

                <div class="by-hero-art" aria-hidden="true">
                    <div class="by-art-card by-art-card--1">
                        <span class="by-art-icon"><i class="fa-solid fa-headphones"></i></span>
                        <span><strong>Tech picks</strong><small>Fresh gadgets daily</small></span>
                    </div>
                    <div class="by-art-card by-art-card--2">
                        <span class="by-art-icon"><i class="fa-solid fa-shirt"></i></span>
                        <span><strong>Fashion drops</strong><small>Styles you will love</small></span>
                    </div>
                    <div class="by-art-card by-art-card--3">
                        <span class="by-art-icon"><i class="fa-solid fa-gem"></i></span>
                        <span><strong>Accessories</strong><small>Finish every look</small></span>
                    </div>
                    <div class="by-art-rating"><i class="fa-solid fa-star"></i> Loved by happy shoppers</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== TRUST STRIP ============================== --}}
    <section class="by-trust">
        <div class="container">
            <div class="by-trust-grid">
                <div class="by-trust-item">
                    <span class="by-trust-icon"><i class="fa-solid fa-truck-fast"></i></span>
                    <div>
                        <strong>Fast &amp; free delivery</strong>
                        <span>Reliable logistics, no hidden fees</span>
                    </div>
                </div>
                <div class="by-trust-item">
                    <span class="by-trust-icon"><i class="fa-solid fa-shield-halved"></i></span>
                    <div>
                        <strong>Safe payments</strong>
                        <span>Cash on delivery or pay online</span>
                    </div>
                </div>
                <div class="by-trust-item">
                    <span class="by-trust-icon"><i class="fa-solid fa-headset"></i></span>
                    <div>
                        <strong>Easy support</strong>
                        <span>We are here whenever you need us</span>
                    </div>
                </div>
                <div class="by-trust-item">
                    <span class="by-trust-icon"><i class="fa-solid fa-star"></i></span>
                    <div>
                        <strong>Quality checked</strong>
                        <span>Every product reviewed with care</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== CATEGORIES ============================== --}}
    <section class="by-section">
        <div class="container">
            <div class="by-section-head">
                <div>
                    <span class="by-eyebrow">Browse the marketplace</span>
                    <h2>Shop by category</h2>
                    <p>Little joys for every part of your day — start with a category you love.</p>
                </div>
                <a class="by-link-arrow" href="{{ url('/collections') }}">
                    All categories <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="row g-3 g-lg-4">
                @php
                    $homeSlugs = ['accessories', 'fashion', 'electronics'];
                    $allVisible = $navCategories ?? collect();
                    $priority = $allVisible->whereIn('slug', $homeSlugs)
                        ->sortBy(fn ($c) => array_search($c->slug, $homeSlugs));
                    $fillers = $allVisible->whereNotIn('slug', $homeSlugs);
                    $homeCategories = $priority->concat($fillers)->take(3);
                    $catTints = ['by-tint-pink', 'by-tint-butter', 'by-tint-lavender', 'by-tint-mint'];
                    $catIcons = ['electronics' => 'fa-headphones', 'fashion' => 'fa-shirt', 'accessories' => 'fa-gem'];
                @endphp
                @foreach ($homeCategories as $category)
                    <div class="col-6 col-lg-3">
                        <a class="by-cat-card {{ $catTints[$loop->index % count($catTints)] }}" href="{{ url('/collections/' . $category->slug) }}">
                            <span class="by-cat-icon"><i class="fa-solid {{ $catIcons[$category->slug] ?? 'fa-tag' }}"></i></span>
                            <h3>{{ $category->name }}</h3>
                            <p>{{ $category->description ? \Illuminate\Support\Str::limit(strip_tags($category->description), 60) : 'Explore our ' . $category->name . ' collection.' }}</p>
                            <span class="by-cat-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                        </a>
                    </div>
                @endforeach
                <div class="col-6 col-lg-3">
                    <a class="by-cat-card by-tint-mint" href="{{ url('/new-arrivals') }}">
                        <span class="by-cat-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                        <h3>New Arrivals</h3>
                        <p>Fresh drops, just landed and ready to explore.</p>
                        <span class="by-cat-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== PROMO ============================== --}}
    <section class="by-section--tight">
        <div class="container">
            <div class="by-promo">
                <div>
                    <span class="by-eyebrow by-eyebrow--ink">This week’s happy edit</span>
                    <h2>New season, new favourites.</h2>
                    <p>Discover fresh arrivals, playful accessories and everyday essentials — all picked to brighten your week.</p>
                </div>
                <a href="{{ url('/collections/trending') }}" class="btn btn-dark btn-lg">
                    Trending Now <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================== FEATURES ============================== --}}
    <section class="by-section">
        <div class="container">
            <div class="by-section-head by-section-head--center">
                <span class="by-eyebrow">Why shop with Buyora</span>
                <h2>A marketplace that feels good</h2>
                <p>Friendly service, carefully chosen products and a checkout that stays out of your way.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="by-feature-card">
                        <div class="by-feature-icon by-icon-pink"><i class="fa-solid fa-truck-fast"></i></div>
                        <h3>Fast &amp; free delivery</h3>
                        <p>Nationwide delivery with reliable logistics partners and no hidden fees.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="by-feature-card">
                        <div class="by-feature-icon by-icon-mint"><i class="fa-solid fa-check-circle"></i></div>
                        <h3>Top quality</h3>
                        <p>Every product is selected and reviewed to ensure maximum quality and value.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="by-feature-card">
                        <div class="by-feature-icon by-icon-lavender"><i class="fa-solid fa-headset"></i></div>
                        <h3>24/7 support</h3>
                        <p>Need help? Our support team is always ready to assist you — day or night.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== CTA ============================== --}}
    <section class="by-section--tight">
        <div class="container">
            <div class="by-cta">
                <span class="by-eyebrow by-eyebrow--ink">Join the fun</span>
                <h2>Thousands of happy shoppers, one cheerful place.</h2>
                <p>Discover exclusive deals and experience effortless online shopping with Buyora.</p>
                <a href="{{ url('/collections') }}" class="btn btn-dark btn-lg">
                    Start shopping <i class="fa-solid fa-shopping-bag"></i>
                </a>
            </div>
        </div>
    </section>

@endsection
