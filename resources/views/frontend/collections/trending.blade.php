@extends('layouts.app')

@section('title', 'Trending Now — Buyora')

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Trending Now</span>
            </div>
            <span class="by-eyebrow">Most popular picks</span>
            <h1>Trending now</h1>
            <p>Discover what shoppers are loving right now — standout finds and popular items across Buyora.</p>
        </div>
    </div>

    <section class="by-section">
        <div class="container">
            <div class="row g-3 g-lg-4">
                @forelse ($trendingProducts as $product)
                    <div class="col-6 col-md-4 col-xl-3">
                        <article class="by-product-card">
                            <a href="{{ url('/collections/' . $product->catagory->slug . '/' . $product->slug) }}"
                               class="by-product-media" aria-label="{{ $product->name }}">
                                @php $img = $product->productImages->first()->image ?? null; @endphp
                                @if($img)
                                    <img src="{{ asset($img) }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <img src="{{ asset('assets/images/placeholder.png') }}" alt="{{ $product->name }}" loading="lazy">
                                @endif

                                <span class="by-badge-featured"><i class="fa-solid fa-fire"></i> Trending</span>

                                @if($product->selling_price > 0 && $product->original_price > $product->selling_price)
                                    <span class="by-badge-sale">
                                        -{{ round((1 - $product->selling_price / $product->original_price) * 100) }}%
                                    </span>
                                @endif
                            </a>

                            <div class="by-product-body">
                                <div class="by-product-brand">{{ $product->catagory->name ?? 'Buyora' }}</div>
                                <a href="{{ url('/collections/' . $product->catagory->slug . '/' . $product->slug) }}"
                                   class="by-product-name" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </a>

                                <div class="by-price-row">
                                    <span class="by-price">Tk {{ number_format($product->selling_price > 0 ? $product->selling_price : $product->original_price) }}</span>
                                    @if ($product->selling_price > 0 && $product->original_price > $product->selling_price)
                                        <span class="by-price-old">Tk {{ number_format($product->original_price) }}</span>
                                    @endif
                                </div>

                                <a href="{{ url('/collections/' . $product->catagory->slug . '/' . $product->slug) }}"
                                   class="btn btn-primary by-product-btn">
                                    View product <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="by-empty">
                            <div class="by-empty-icon"><i class="fa-solid fa-fire"></i></div>
                            <h3>No trending products yet</h3>
                            <p>Trending products will show up here as soon as they are available.</p>
                            <a href="{{ url('/collections') }}" class="btn btn-primary">Browse all categories</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
