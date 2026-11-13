@extends('layouts.app')

@section('title', 'Confirm Password — Buyora')

@section('content')
    <div class="by-auth-page">
        <div class="by-auth-simple">
            <span class="by-eyebrow">Security check</span>
            <h1>{{ __('Confirm Password') }}</h1>
            <p>Please confirm your password before continuing.</p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-4">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" autofocus
                        placeholder="Enter your password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ __('Confirm Password') }} <i class="fa-solid fa-check"></i>
                </button>
            </form>

            @if (Route::has('password.request'))
                <p class="by-auth-alt mb-0">
                    <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                </p>
            @endif
        </div>
    </div>
@endsection
