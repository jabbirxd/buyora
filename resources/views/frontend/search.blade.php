@extends('layouts.app')

@section('title', 'Search: ' . $searchTerm . ' - Buyora')

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Search</span>
            </div>
            <span class="by-eyebrow">Search</span>
            <h1>Search results for "{{ $searchTerm }}"</h1>
            <p>{{ $products->total() }} {{ \Illuminate\Support\Str::plural('product', $products->total()) }} found</p>
        </div>
    </div>

    <section class="by-section">
        <div class="container">
            @if ($products->isEmpty())
                <div class="by-empty">
                    <div class="by-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <h3>No products found for '{{ $searchTerm }}'</h3>
                    <p>Try a different or more general search term.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">Back to home</a>
                </div>
            @else
                <div class="row g-3 g-lg-4">
                    @foreach ($products as $productItem)
                        <div class="col-6 col-md-6 col-xl-4">
                            <article class="by-product-card">
                                <a href="{{ url('/collections/' . $productItem->catagory->slug . '/' . $productItem->slug) }}"
                                   class="by-product-media" aria-label="{{ $productItem->name }}">
                                    @if ($productItem->productImages->count() > 0)
                                        <img src="{{ asset($productItem->productImages[0]->image) }}"
                                             alt="{{ $productItem->name }}" loading="lazy">
                                    @else
                                        <img src="{{ asset('assets/images/placeholder.png') }}"
                                             alt="{{ $productItem->name }}" loading="lazy">
                                    @endif

                                    @if ($productItem->quantity > 0)
                                        <span class="by-badge-stock"><i class="fa-solid fa-circle-check"></i> In stock</span>
                                    @else
                                        <span class="by-badge-stock is-out"><i class="fa-solid fa-circle-xmark"></i> Out of stock</span>
                                    @endif

                                    @if ($productItem->selling_price > 0 && $productItem->original_price > $productItem->selling_price)
                                        <span class="by-badge-sale">
                                            -{{ round((1 - $productItem->selling_price / $productItem->original_price) * 100) }}%
                                        </span>
                                    @endif
                                </a>

                                <div class="by-product-body">
                                    <div class="by-product-brand">{{ $productItem->catagory->name ?? 'Buyora' }}</div>
                                    <a href="{{ url('/collections/' . $productItem->catagory->slug . '/' . $productItem->slug) }}"
                                       class="by-product-name" title="{{ $productItem->name }}">
                                        {{ $productItem->name }}
                                    </a>

                                    <div class="by-price-row">
                                        <span class="by-price">Tk {{ number_format($productItem->selling_price > 0 ? $productItem->selling_price : $productItem->original_price) }}</span>
                                        @if ($productItem->selling_price > 0 && $productItem->original_price > $productItem->selling_price)
                                            <span class="by-price-old">Tk {{ number_format($productItem->original_price) }}</span>
                                        @endif
                                    </div>

                                    <a href="{{ url('/collections/' . $productItem->catagory->slug . '/' . $productItem->slug) }}"
                                       class="btn btn-primary by-product-btn">
                                        View product <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
