@extends('layouts.app')

@section('title', 'About Us — Buyora')

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>About Us</span>
            </div>
            <span class="by-eyebrow">Who we are</span>
            <h1>About Buyora</h1>
            <p>Your cheerful everyday online marketplace in Bangladesh, making shopping simpler and more delightful.</p>
        </div>
    </div>

    <section class="by-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="p-4 p-md-5 bg-white rounded-4 border">
                        <h2 class="h3 fw-bold mb-3">Welcome to Buyora</h2>
                        <p class="text-secondary mb-4" style="line-height: 1.7; font-size: 1.05rem;">
                            Buyora is an everyday online marketplace in Bangladesh dedicated to bringing you quality products across diverse categories. We focus on delivering value, convenience, and a seamless shopping experience for customers across the country.
                        </p>
                        <p class="text-secondary mb-4" style="line-height: 1.7; font-size: 1.05rem;">
                            Whether you are exploring daily essentials, fashion, or modern electronics, Buyora offers an easy-to-browse selection with prompt nationwide delivery, secure checkout methods, and dedicated customer support whenever you need assistance.
                        </p>

                        <div class="row g-3 g-md-4 pt-3">
                            <div class="col-md-4">
                                <div class="by-feature-card h-100 p-4 rounded-3 border bg-light">
                                    <div class="by-feature-icon by-icon-pink mb-3"><i class="fa-solid fa-truck-fast"></i></div>
                                    <h3 class="h5 fw-bold mb-2">Nationwide Delivery</h3>
                                    <p class="text-muted small mb-0">Reliable shipping to every corner of Bangladesh, bringing your orders directly to your doorstep.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="by-feature-card h-100 p-4 rounded-3 border bg-light">
                                    <div class="by-feature-icon by-icon-mint mb-3"><i class="fa-solid fa-shield-halved"></i></div>
                                    <h3 class="h5 fw-bold mb-2">Secure Checkout</h3>
                                    <p class="text-muted small mb-0">Safe payment options including online gateway payment and convenient cash on delivery.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="by-feature-card h-100 p-4 rounded-3 border bg-light">
                                    <div class="by-feature-icon by-icon-lavender mb-3"><i class="fa-solid fa-headset"></i></div>
                                    <h3 class="h5 fw-bold mb-2">Customer Support</h3>
                                    <p class="text-muted small mb-0">Friendly and responsive customer assistance ready to help with your orders and questions.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 text-center">
                            <a href="{{ url('/collections') }}" class="btn btn-primary me-2">Explore Marketplace <i class="fa-solid fa-arrow-right"></i></a>
                            <a href="{{ url('/contact') }}" class="btn by-btn-soft">Get in Touch</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
