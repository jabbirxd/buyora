@extends('layouts.app')

@section('title', 'Create an Account — ' . ($setting->website_name ?? 'Buyora'))

@section('content')
    <div class="by-auth-page">
        <div class="by-auth-card">
            <aside class="by-auth-aside">
                <a class="by-brand" href="{{ url('/') }}" aria-label="{{ $setting->website_name ?? 'Buyora' }} home">
                    <span class="by-brand-mark"><i class="fa-solid fa-bag-shopping"></i></span>
                    <span class="by-brand-text">{{ $setting->website_name ?? 'Buyora' }}<span class="by-brand-dot">.</span></span>
                </a>
                <div>
                    <h2>Join the cheerful side of shopping.</h2>
                    <p>Create your free {{ $setting->website_name ?? 'Buyora' }} account and make every order a little easier.</p>
                    <ul class="by-auth-points">
                        <li><i class="fa-solid fa-circle-check"></i> Save your delivery details</li>
                        <li><i class="fa-solid fa-circle-check"></i> Build a wishlist you love</li>
                        <li><i class="fa-solid fa-circle-check"></i> Follow every order in one place</li>
                    </ul>
                </div>
                <p style="font-size:.8rem;color:#5D4A63;">Everyday finds, a little more joyful.</p>
            </aside>

            <div class="by-auth-form">
                <span class="by-eyebrow">Get started</span>
                <h1>Create an account</h1>
                <p>It only takes a minute — then the fun begins.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Name') }}</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            placeholder="Enter your name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email"
                            placeholder="you@example.com">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="new-password" placeholder="Create a password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            required autocomplete="new-password" placeholder="Confirm your password">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('Register') }} <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <p class="by-auth-alt">
                    Already have an account? <a href="{{ route('login') }}">Sign in</a>
                </p>
            </div>
        </div>
    </div>
@endsection
