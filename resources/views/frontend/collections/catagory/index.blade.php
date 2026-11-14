@extends('layouts.app')

@section('title', 'All Categories — ' . ($setting->website_name ?? 'Buyora'))

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>All Categories</span>
            </div>
            <span class="by-eyebrow">Browse the marketplace</span>
            <h1>Our categories</h1>
            <p>Find your next little joy — explore everything {{ $setting->website_name ?? 'Buyora' }} has to offer.</p>
        </div>
    </div>

    <section class="by-section">
        <div class="container">
            @php
                $tints = ['by-tint-pink', 'by-tint-peach', 'by-tint-butter', 'by-tint-lavender', 'by-tint-mint'];
            @endphp

            <div class="row g-3 g-lg-4">
                @forelse ($catagories as $catagoryItem)
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ url('/collections/' . $catagoryItem->slug) }}"
                           class="by-category-tile {{ $tints[$loop->index % count($tints)] }}">
                            <div class="by-category-media">
                                @if ($catagoryItem->image)
                                    <img src="{{ asset('uploads/catagory/' . $catagoryItem->image) }}"
                                         alt="{{ $catagoryItem->name }}">
                                @else
                                    <img src="{{ asset('assets/images/placeholder.png') }}"
                                         alt="{{ $catagoryItem->name }}">
                                @endif
                            </div>
                            <div class="by-category-body">
                                <h3>{{ $catagoryItem->name }}</h3>
                                <span><i class="fa-solid fa-arrow-right"></i></span>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="by-empty">
                            <div class="by-empty-icon"><i class="fa-solid fa-folder-open"></i></div>
                            <h3>No categories yet</h3>
                            <p>New categories will appear here as the store grows.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary">Back to home</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
