@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Contact Us</span>
            </div>
            <span class="by-eyebrow">We are here for you</span>
            <h1>Get in touch</h1>
            <p>Questions, feedback or a custom request? Our team is ready to help, 24/7.</p>
        </div>
    </div>

    <section class="by-section">
        <div class="container">
            @if(session('message'))
                <div class="alert alert-success text-center" role="alert">
                    {{ session('message') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-12">
                            <div class="by-contact-card">
                                <div class="by-feature-icon by-icon-pink"><i class="fa-solid fa-location-dot"></i></div>
                                <h3>Visit us</h3>
                                <p>{!! $setting->address ? nl2br(e($setting->address)) : 'Boro Boyra, Khalishpur,<br>Khulna 9000, Bangladesh' !!}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-12">
                            <div class="by-contact-card">
                                <div class="by-feature-icon by-icon-mint"><i class="fa-solid fa-phone"></i></div>
                                <h3>Call us</h3>
                                <p><a href="tel:{{ str_replace(' ', '', $setting->phone1 ?? '+8801410295500') }}">{{ $setting->phone1 ?? '+880 1410 295500' }}</a></p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-12">
                            <div class="by-contact-card">
                                <div class="by-feature-icon by-icon-lavender"><i class="fa-solid fa-envelope"></i></div>
                                <h3>Email us</h3>
                                <p><a href="mailto:{{ $setting->email1 ?? 'info@buyora.com' }}">{{ $setting->email1 ?? 'info@buyora.com' }}</a></p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-12">
                            <div class="by-contact-card">
                                <div class="by-feature-icon by-icon-butter"><i class="fa-regular fa-clock"></i></div>
                                <h3>Support hours</h3>
                                <p>24 hours a day, 7 days a week</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="by-panel h-100">
                        <div class="by-panel-head">
                            <div>
                                <h3>Send us a message</h3>
                                <p>Fill in the form and we will get back to you as soon as possible.</p>
                            </div>
                        </div>

                        <form action="{{ url('/contact') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-name">Your name</label>
                                    <input id="contact-name" type="text" name="name" class="form-control" required placeholder="e.g. Ayesha Rahman">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-email">Your email</label>
                                    <input id="contact-email" type="email" name="email" class="form-control" required placeholder="you@example.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="contact-message">Your message</label>
                                    <textarea id="contact-message" name="message" rows="6" class="form-control" required placeholder="Write your message here…"></textarea>
                                </div>
                            </div>
                            <button class="btn btn-primary mt-4">
                                Send message <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
