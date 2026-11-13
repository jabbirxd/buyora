<div>
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>My Cart</span>
            </div>
            <span class="by-eyebrow">Almost yours</span>
            <h1>My cart</h1>
            <p>Review your picks, adjust quantities and checkout when you are ready.</p>
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

            @if (count($cart) > 0)
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="by-cart-list">
                            @foreach ($cart as $cartItem)
                                @if ($cartItem->product)
                                    <div class="by-cart-item">
                                        <div class="by-cart-product">
                                            @php
                                                $product = $cartItem->product;
                                                $firstImage =
                                                    $product && $product->productImages && $product->productImages->count() > 0
                                                        ? $product->productImages[0]->image
                                                        : 'images/default.png';
                                            @endphp
                                            <a href="{{ url('collections/' . $cartItem->product->catagory->slug . '/' . $cartItem->product->slug) }}">
                                                <img src="{{ asset($firstImage) }}" class="by-cart-thumb"
                                                    alt="{{ $cartItem->product->name }}">
                                            </a>
                                            <div>
                                                <a class="by-cart-name"
                                                   href="{{ url('collections/' . $cartItem->product->catagory->slug . '/' . $cartItem->product->slug) }}">
                                                    {{ $cartItem->product->name }}
                                                </a>
                                                @if ($cartItem->productColor && $cartItem->productColor->color)
                                                    <span class="by-cart-meta">Colour: {{ $cartItem->productColor->color->name }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="by-cart-price">
                                            <span class="by-cart-label">Price</span>
                                            Tk {{ number_format($cartItem->product->selling_price) }}
                                        </div>

                                        <div>
                                            <span class="by-cart-label">Quantity</span>
                                            <div class="by-qty">
                                                <button type="button" wire:loading.attr="disabled"
                                                    wire:click="decrementQuantity({{ $cartItem->id }})" aria-label="Decrease quantity">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                                <input type="text" value="{{ $cartItem->quantity }}" class="input-quantity"
                                                    aria-label="Quantity" readonly />
                                                <button type="button" wire:loading.attr="disabled"
                                                    wire:click="incrementQuantity({{ $cartItem->id }})" aria-label="Increase quantity">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="by-cart-total">
                                            <span class="by-cart-label">Total</span>
                                            Tk {{ number_format($cartItem->product->selling_price * $cartItem->quantity) }}
                                        </div>

                                        <button type="button" wire:loading.attr="disabled"
                                            wire:click="removeCartItem({{ $cartItem->id }})"
                                            class="by-remove-btn" aria-label="Remove {{ $cartItem->product->name }}">
                                            <span wire:loading.remove wire:target="removeCartItem({{ $cartItem->id }})">
                                                <i class="fa fa-trash"></i>
                                            </span>
                                            <span wire:loading wire:target="removeCartItem({{ $cartItem->id }})">
                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                    </div>

                                    @php $totalPrice += $cartItem->product->selling_price * $cartItem->quantity @endphp
                                @endif
                            @endforeach
                        </div>

                        <div class="by-promo-banner mt-4">
                            <div>
                                <h3>Get the best deals &amp; offers</h3>
                                <p>Check out our latest collections and discover something new.</p>
                            </div>
                            <a href="{{ url('/collections') }}" class="btn btn-light">
                                Shop now <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="by-summary-card">
                            <h3>Order summary</h3>
                            <div class="by-summary-row">
                                <span>Items</span>
                                <strong>{{ count($cart) }}</strong>
                            </div>
                            <div class="by-summary-row">
                                <span>Delivery</span>
                                <strong>Included</strong>
                            </div>
                            <div class="by-summary-total">
                                <span>Total</span>
                                <strong>TK {{ number_format($totalPrice) }}</strong>
                            </div>
                            <a href="{{ url('/checkout') }}" class="btn btn-primary w-100 mt-3">
                                Proceed to checkout <i class="fa-solid fa-arrow-right"></i>
                            </a>
                            <a href="{{ url('/collections') }}" class="btn by-btn-soft w-100 mt-2">
                                Continue shopping
                            </a>
                            <div class="by-summary-note">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Taxes and delivery charges are included. Your checkout is secure.</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="by-empty">
                    <div class="by-empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    <h3>Your cart is feeling light</h3>
                    <p>Add a few favourites and they will show up here, ready for checkout.</p>
                    <a href="{{ url('/collections') }}" class="btn btn-primary">Start shopping</a>
                </div>
            @endif
        </div>
    </section>
</div>
