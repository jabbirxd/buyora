@extends('layouts.app')

@section('title', 'Login — Buyora')

@section('content')
    <div class="by-auth-page">
        <div class="by-auth-card">
            <aside class="by-auth-aside">
                <a class="by-brand" href="{{ url('/') }}" aria-label="Buyora home">
                    <span class="by-brand-mark"><i class="fa-solid fa-bag-shopping"></i></span>
                    <span class="by-brand-text">Buyora<span class="by-brand-dot">.</span></span>
                </a>
                <div>
                    <h2>Welcome back to your happy place.</h2>
                    <p>Sign in to pick up where you left off — your cart, wishlist and orders are waiting.</p>
                    <ul class="by-auth-points">
                        <li><i class="fa-solid fa-circle-check"></i> Track your orders easily</li>
                        <li><i class="fa-solid fa-circle-check"></i> Save favourites to your wishlist</li>
                        <li><i class="fa-solid fa-circle-check"></i> Faster checkout every time</li>
                    </ul>
                </div>
                <p style="font-size:.8rem;color:#5D4A63;">Everyday finds, a little more joyful.</p>
            </aside>

            <div class="by-auth-form">
                <span class="by-eyebrow">Welcome back</span>
                <h1>Sign in to Buyora</h1>
                <p>Enter your details to access your account.</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
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
                            name="password" required autocomplete="current-password" placeholder="Enter your password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="by-auth-row mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="forgot-password" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('Login') }} <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                @if (Route::has('register'))
                    <p class="by-auth-alt">
                        New to Buyora? <a href="{{ route('register') }}">Create an account</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
