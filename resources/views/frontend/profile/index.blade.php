@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>My Profile</span>
            </div>
            <span class="by-eyebrow">Your account</span>
            <h1>My profile</h1>
            <p>Keep your details up to date for a smoother checkout.</p>
        </div>
    </div>

    <section class="by-section--tight">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="by-panel">
                        <div class="by-panel-head">
                            <div>
                                <h3><i class="fa-solid fa-user me-2" style="color:var(--by-rose)"></i>Personal details</h3>
                                <p>This information helps us deliver your orders smoothly.</p>
                            </div>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Profile picture</label>
                                    <input type="file" name="profile_image" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address) }}</textarea>
                                </div>
                                @if($user->profile_image)
                                    <div class="col-12">
                                        <img src="{{ asset('uploads/profile/' . $user->profile_image) }}" alt="Profile"
                                             class="img-thumbnail mt-1" style="max-width:120px;border-radius:16px;">
                                    </div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">
                                Save changes <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="by-panel">
                        <div class="by-panel-head">
                            <div>
                                <h3><i class="fa-solid fa-lock me-2" style="color:var(--by-rose)"></i>Change password</h3>
                                <p>Choose a strong password you have not used before.</p>
                            </div>
                        </div>

                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Current password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm new password</label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary mt-1">
                                Update password
                            </button>
                        </form>
                    </div>

                    <div class="by-promo-banner mt-4">
                        <div>
                            <h3>Need a hand?</h3>
                            <p>Our support team is available 24/7.</p>
                        </div>
                        <a href="{{ url('/contact') }}" class="btn btn-light">Contact us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
