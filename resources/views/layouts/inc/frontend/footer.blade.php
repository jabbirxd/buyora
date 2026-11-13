<footer class="by-footer">
    <div class="by-footer-inner">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4">
                    <a class="by-brand mb-3" href="{{ url('/') }}" aria-label="Buyora home">
                        <span class="by-brand-mark"><i class="fa-solid fa-bag-shopping"></i></span>
                        <span class="by-brand-text">{{ $setting->website_name ?? 'Buyora' }}<span class="by-brand-dot">.</span></span>
                    </a>
                    <p>
                        Your cheerful everyday marketplace for trendy, high-quality products — delivered across Bangladesh with care.
                        We blend style, value and simplicity for a seamless online shopping experience.
                    </p>
                    <div class="by-footer-chips">
                        <span class="by-footer-chip"><i class="fa-solid fa-shield-halved me-1"></i> Secure checkout</span>
                        <span class="by-footer-chip"><i class="fa-solid fa-truck-fast me-1"></i> Nationwide delivery</span>
                        <span class="by-footer-chip"><i class="fa-solid fa-rotate-left me-1"></i> Easy support</span>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h4>Shop</h4>
                    <ul class="by-footer-list">
                        <li><a href="{{ url('/collections') }}">All Categories</a></li>
                        <li><a href="{{ url('/new-arrivals') }}">New Arrivals</a></li>
                        <li><a href="{{ url('/collections/featured') }}">Featured Products</a></li>
                        @php
                            $footerCategories = ($navCategories ?? null) instanceof \Illuminate\Support\Collection
                                ? $navCategories
                                : collect($navCategories ?? []);
                        @endphp
                        @foreach ($footerCategories->take(3) as $category)
                            <li><a href="{{ url('/collections/' . $category->slug) }}">{{ $category->name }}</a></li>
                        @endforeach
                        @if ($footerCategories->count() > 3)
                            <li><a href="{{ url('/collections') }}">See More...</a></li>
                        @endif
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h4>Help</h4>
                    <ul class="by-footer-list">
                        <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                        <li><a href="{{ url('/cart') }}">My Cart</a></li>
                        <li><a href="{{ url('/wishlist') }}">My Wishlist</a></li>
                        <li><a href="{{ url('/orders') }}">My Orders</a></li>
                        <li><a href="{{ url('/profile') }}">My Profile</a></li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h4>Reach Us</h4>
                    <ul class="by-footer-contact">
                        <li>
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                            <span>{!! $setting->address ? nl2br(e($setting->address)) : 'Boro Boyra, Khalishpur,<br>Khulna 9000, Bangladesh' !!}</span>
                        </li>
                        <li>
                            <i class="fa fa-phone" aria-hidden="true"></i>
                            <a href="tel:{{ str_replace(' ', '', $setting->phone1 ?? '+8801410295500') }}">{{ $setting->phone1 ?? '+880 1410 295500' }}</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            <a href="mailto:{{ $setting->email1 ?? 'info@buyora.com' }}">{{ $setting->email1 ?? 'info@buyora.com' }}</a>
                        </li>
                        <li>
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            <span>Support available 24/7</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="by-footer-bottom">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <p>&copy; 2026 {{ $setting->website_name ?? 'Buyora' }}. All rights reserved.</p>
                <p>Made with care for shoppers in Bangladesh <span aria-hidden="true">💛</span></p>
            </div>
        </div>
    </div>
</footer>
