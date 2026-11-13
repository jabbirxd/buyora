@extends('layouts.app')

@section('title', 'Thank You For Your Purchase')

@section('content')
    <div class="thank-you-wrapper">
        <div class="container">
            <div class="thank-you-card mx-auto">
                <div class="checkmark-circle">
                    <i class="fa-solid fa-check"></i>
                </div>
                <span class="by-eyebrow">Order confirmed</span>
                <h1 class="thank-you-title">Thank you for your order!</h1>
                <p class="thank-you-subtitle">
                    We have received your order and will begin processing it shortly.
                    We will keep you updated every step of the way.
                </p>

                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                    <span class="by-chip by-chip--success"><i class="fa-solid fa-circle-check"></i> Payment confirmed</span>
                    <span class="by-chip by-chip--muted"><i class="fa-solid fa-truck-fast"></i> Delivery in 3–5 days</span>
                </div>

                <div class="thank-you-actions mt-2">
                    <a href="{{ url('collections') }}" class="btn btn-primary btn-lg">
                        Continue shopping <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="{{ url('orders') }}" class="btn by-btn-soft btn-lg ms-1 mt-2 mt-sm-0">
                        View my orders
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
