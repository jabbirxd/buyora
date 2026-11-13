<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script> --}}

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $setting->page_title ?? 'Buyora')</title>
    <meta name="description" content="@yield('meta_description', $setting->meta_description ?? '')">
    <meta name="keywords" content="@yield('meta_keyword', $setting->meta_keyword ?? '')">
    <meta name="theme-color" content="#FFF8F1">

    <!-- Fonts: Outfit (headings) + Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<!-- style -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/buyora.css') }}" rel="stylesheet">
    <!-- Animate.css for animation effects -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


    @livewireStyles

</head>
<body>
    <div id="app" class="d-flex flex-column min-vh-100">
        @include('layouts.inc.frontend.navbar')
        

        <main class="flex-fill pb-5">
            @yield('content')
        </main>

        @include('layouts.inc.frontend.footer')
    </div>
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
