@extends('layouts.app')

@section('title')
{{$catagory->meta_title}}
@endsection

@section('meta_keyword')
{{$catagory->meta_keyword}}
@endsection

@section('meta_description')
{{$catagory->meta_description}}
@endsection

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="{{ url('/collections') }}">All Categories</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>{{ $catagory->name }}</span>
            </div>
            <span class="by-eyebrow">Shop the collection</span>
            <h1>{{ $catagory->name }}</h1>
            <p>Handpicked products from the {{ $catagory->name }} collection, ready to brighten your day.</p>
        </div>
    </div>

    <section class="by-section">
        <div class="container">
            <livewire:frontend.product.index :catagory="$catagory" />
        </div>
    </section>

    {{-- About This Category Section --}}
    <section class="by-section--tight">
        <div class="container">
            <div class="by-section-head by-section-head--center">
                <span class="by-eyebrow">Good to know</span>
                <h2>Why choose our {{ $catagory->name }} products?</h2>
                <p>At Buyora, we make sure every product in the <strong>{{ $catagory->name }}</strong> category meets your expectations and style.</p>
            </div>

            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="by-feature-card">
                        <div class="by-feature-icon by-icon-butter mx-auto"><i class="fa-solid fa-star"></i></div>
                        <h3>Top quality</h3>
                        <p>We only feature reliable and highly-rated items — guaranteed to last and satisfy.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="by-feature-card">
                        <div class="by-feature-icon by-icon-pink mx-auto"><i class="fa-solid fa-truck-fast"></i></div>
                        <h3>Fast delivery</h3>
                        <p>Get your order at your doorstep in record time, with no delays.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="by-feature-card">
                        <div class="by-feature-icon by-icon-mint mx-auto"><i class="fa-solid fa-thumbs-up"></i></div>
                        <h3>Customer satisfaction</h3>
                        <p>Our customers love the products — and we are sure you will too.</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ url('/contact') }}" class="btn btn-outline-primary btn-lg">
                    Need help? Contact us <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
@endsection
