@extends('layouts.app')

@section('title', 'Reset Password — Buyora')

@section('content')
    <div class="by-auth-page">
        <div class="by-auth-simple">
            <span class="by-eyebrow">Account help</span>
            <h1>{{ __('Reset Password') }}</h1>
            <p>Enter your email address and we will send you a secure reset link.</p>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
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

                <button type="submit" class="btn btn-primary">
                    {{ __('Send Password Reset Link') }} <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

            <p class="by-auth-alt mb-0">Remembered it? <a href="{{ route('login') }}">Back to sign in</a></p>
        </div>
    </div>
@endsection
