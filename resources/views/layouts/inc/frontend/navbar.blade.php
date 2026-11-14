<header class="by-navbar">
    <div class="by-topbar">
        <div class="container">
            <div class="by-topbar-inner">
                <a class="by-brand" href="{{ url('/') }}" aria-label="{{ $setting->website_name ?? 'Buyora' }} home">
                    <span class="by-brand-mark"><i class="fa-solid fa-bag-shopping"></i></span>
                    <span class="by-brand-text">{{ $setting->website_name ?? 'Buyora' }}<span class="by-brand-dot">.</span></span>
                </a>

                <form class="by-search" role="search" action="{{ url('/search') }}" method="GET">
                    <i class="fa fa-search by-search-icon" aria-hidden="true"></i>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products, brands and more…" aria-label="Search your product" />
                    <button class="by-search-btn" type="submit">
                        <i class="fa fa-search d-sm-none" aria-hidden="true"></i>
                        <span>Search</span>
                    </button>
                </form>

                <div class="by-actions">
                    @auth
                        <a class="by-action" href="{{ url('cart') }}" aria-label="Cart">
                            <span class="by-action-icon">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                <span class="by-count"><livewire:frontend.cart.cart-count /></span>
                            </span>
                            <span class="by-action-text">Cart</span>
                        </a>
                        <a class="by-action" href="{{ url('wishlist') }}" aria-label="Wishlist">
                            <span class="by-action-icon">
                                <i class="fa fa-heart" aria-hidden="true"></i>
                                <span class="by-count"><livewire:frontend.wishlist-count /></span>
                            </span>
                            <span class="by-action-text">Wishlist</span>
                        </a>
                    @endauth
                    @if(Auth::check() && Auth::user()->roll_as == 1)
                        <a class="by-action d-none d-xl-inline-flex" href="/admin/dashboard">
                            <span class="by-action-icon"><i class="fa fa-columns" aria-hidden="true"></i></span>
                            <span class="by-action-text">Dashboard</span>
                        </a>
                    @endif

                    <div class="by-auth-actions">
                        @guest
                            @if (Route::has('login'))
                                <a class="btn btn-light" href="{{ route('login') }}">{{ __('Login') }}</a>
                            @endif
                            @if (Route::has('register'))
                                <a class="btn btn-primary" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                        @else
                            <div class="dropdown">
                                <a class="by-user-toggle dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if(Auth::user()->profile_image)
                                        <img src="{{ asset('uploads/profile/' . Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}" class="by-user-avatar-img">
                                    @else
                                        <span class="by-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    @endif
                                    <span>{{ Auth::user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fa fa-user"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="/orders"><i class="fa fa-list"></i> My Orders</a></li>
                                    <li><a class="dropdown-item" href="{{ route('wishlist') }}"><i class="fa fa-heart"></i> My Wishlist</a></li>
                                    <li><a class="dropdown-item" href="{{ route('cart') }}"><i class="fa fa-shopping-cart"></i> My Cart</a></li>
                                    @if(Auth::user()->roll_as == 1)
                                        <li><a class="dropdown-item" href="/admin/dashboard"><i class="fa fa-columns"></i> Dashboard</a></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="by-mainnav">
        <div class="container">
            <div class="by-mainnav-inner">
                <button class="by-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    <span class="d-lg-none" style="font-size:.82rem;font-weight:600;">Browse</span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav by-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('collections') ? 'active' : '' }}" href="{{ url('/collections') }}">All Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('new-arrivals') ? 'active' : '' }}" href="{{ url('/new-arrivals') }}">New Arrivals</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('collections/featured') ? 'active' : '' }}" href="{{ url('/collections/featured') }}">Featured Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('collections/trending*') ? 'active' : '' }}" href="{{ url('/collections/trending') }}">Trending Now</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}" href="{{ url('/orders') }}">My Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('about-us') ? 'active' : '' }}" href="{{ url('/about-us') }}">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="/contact">Contact</a>
                        </li>
                    </ul>
                    <div class="by-nav-note">
                        <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                        Nationwide delivery across Bangladesh
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
