@extends('layouts.app')

@section('title', 'Choose a New Password — Buyora')

@section('content')
    <div class="by-auth-page">
        <div class="by-auth-simple">
            <span class="by-eyebrow">Account help</span>
            <h1>{{ __('Reset Password') }}</h1>
            <p>Choose a fresh password for your Buyora account.</p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
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
                        name="password" required autocomplete="new-password" placeholder="New password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                        required autocomplete="new-password" placeholder="Confirm new password">
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('Reset Password') }} <i class="fa-solid fa-check"></i>
                </button>
            </form>
        </div>
    </div>
@endsection
