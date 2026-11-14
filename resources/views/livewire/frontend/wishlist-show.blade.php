<div>
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>My Wishlist</span>
            </div>
            <span class="by-eyebrow">Saved for later</span>
            <h1>My wishlist</h1>
            <p>A little collection of things you love. Add them to your cart whenever you are ready.</p>
        </div>
    </div>

    <section class="by-section--tight">
        <div class="container">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (count($wishlist) > 0)
                <div class="by-cart-list">
                    @foreach ($wishlist as $wishlistItem)
                        @if ($wishlistItem->product)
                            @php $img = $wishlistItem->product->productImages->first()->image ?? null; @endphp
                            <div class="by-wishlist-item">
                                <div class="by-cart-product">
                                    <a href="{{ url('collections/' . $wishlistItem->product->catagory->slug . '/' . $wishlistItem->product->slug) }}">
                                        @if ($img)
                                            <img src="{{ asset($img) }}" class="by-cart-thumb"
                                                alt="{{ $wishlistItem->product->name }}">
                                        @else
                                            <img src="{{ asset('assets/images/placeholder.png') }}" class="by-cart-thumb"
                                                alt="{{ $wishlistItem->product->name }}">
                                        @endif
                                    </a>
                                    <div>
                                        <a class="by-cart-name"
                                           href="{{ url('collections/' . $wishlistItem->product->catagory->slug . '/' . $wishlistItem->product->slug) }}">
                                            {{ $wishlistItem->product->name }}
                                        </a>
                                        <span class="by-cart-meta">{{ $wishlistItem->product->catagory->name ?? ($setting->website_name ?? 'Buyora') }}</span>
                                    </div>
                                </div>

                                <div class="by-cart-price">
                                    <span class="by-cart-label">Price</span>
                                    Tk {{ number_format($wishlistItem->product->selling_price) }}
                                </div>

                                <div class="d-flex align-items-center gap-2 justify-content-end">
                                    <a href="{{ url('collections/' . $wishlistItem->product->catagory->slug . '/' . $wishlistItem->product->slug) }}"
                                       class="btn btn-primary btn-sm">
                                        View product
                                    </a>
                                    <button type="button" wire:click="removeWishlistItem({{ $wishlistItem->id }})"
                                        class="by-remove-btn" aria-label="Remove {{ $wishlistItem->product->name }}">
                                        <span wire:loading.remove wire:target="removeWishlistItem({{ $wishlistItem->id }})">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                        <span wire:loading wire:target="removeWishlistItem({{ $wishlistItem->id }})">
                                            <i class="fa-solid fa-spinner fa-spin"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="by-empty">
                    <div class="by-empty-icon"><i class="fa-regular fa-heart"></i></div>
                    <h3>Your wishlist is waiting</h3>
                    <p>Tap the heart on any product to save it here for later.</p>
                    <a href="{{ url('/collections') }}" class="btn btn-primary">Find something lovely</a>
                </div>
            @endif
        </div>
    </section>
</div>
