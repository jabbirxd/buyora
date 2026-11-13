<div>
    <div class="by-product-view">
        <div class="container">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="{{ url('/collections') }}">Collections</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="{{ url('/collections/' . $product->catagory->slug) }}">{{ $product->catagory->name }}</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>{{ $product->name }}</span>
            </div>

            <div class="row g-4 g-lg-5">
                <div class="col-lg-5">
                    <div class="by-gallery">
                        @if ($product->productImages && $product->productImages->count() > 0)
                            <img src="{{ asset(($product->productImages[$selectedImageIndex] ?? $product->productImages[0])->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="by-gallery-empty">
                                <span><i class="fa-regular fa-image me-2"></i>No image available</span>
                            </div>
                        @endif
                        @if ($product->productImages && $product->productImages->count() > 1)
                            <div class="by-gallery-thumbs">
                                @foreach ($product->productImages as $image)
                                    <img src="{{ asset($image->image) }}" alt="{{ $product->name }}" wire:click="selectImage({{ $loop->index }})" class="{{ $loop->index == $selectedImageIndex ? 'is-selected' : '' }}">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="by-product-info">
                        <span class="by-eyebrow">Buyora pick</span>
                        <h1 class="by-product-title">{{ $product->name }}</h1>

                        <div class="by-product-price-row">
                            <span class="by-product-price">Tk {{ number_format($product->selling_price > 0 ? $product->selling_price : $product->original_price) }}</span>
                            @if ($product->selling_price > 0 && $product->original_price > $product->selling_price)
                                <span class="by-product-price-old">Tk {{ number_format($product->original_price) }}</span>
                                <span class="by-chip by-chip--success">
                                    Save {{ round((1 - $product->selling_price / $product->original_price) * 100) }}%
                                </span>
                            @endif
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            @if ($product->productColors->count() > 0)
                                @if ($prodColorSelectedQuantiy === 'outofstock')
                                    <span class="by-chip by-chip--danger"><i class="fa-solid fa-circle-xmark"></i> Out of stock</span>
                                @elseif($prodColorSelectedQuantiy > 0)
                                    <span class="by-chip by-chip--success"><i class="fa-solid fa-circle-check"></i> In stock</span>
                                @endif
                            @else
                                @if ($product->quantity > 0)
                                    <span class="by-chip by-chip--success"><i class="fa-solid fa-circle-check"></i> In stock</span>
                                @else
                                    <span class="by-chip by-chip--danger"><i class="fa-solid fa-circle-xmark"></i> Out of stock</span>
                                @endif
                            @endif
                            <span class="by-chip by-chip--muted"><i class="fa-solid fa-truck-fast"></i> Delivery in 3–5 days</span>
                        </div>

                        @if ($product->productColors->count() > 0)
                            <span class="by-option-label">Choose a colour</span>
                            <div class="by-color-grid">
                                @foreach ($product->productColors as $colorItem)
                                    <div class="by-color-option text-center">
                                        <label
                                            class="by-color-swatch {{ $productColorId == $colorItem->id ? 'is-selected' : '' }}"
                                            wire:click="colorSelected({{ $colorItem->id }})"
                                            role="button" tabindex="0">
                                            <span class="by-color-dot" style="background-color: #{{ ltrim($colorItem->color?->code ?? 'cccccc', '#') }};"></span>
                                            {{ $colorItem->color?->name ?? 'Default' }}
                                        </label>
                                        <span class="by-color-qty">{{ $colorItem->quantity }} pcs available</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <span class="by-chip by-chip--muted">
                                    Total available colour quantity: {{ $product->productColors->sum('quantity') }}
                                </span>
                            </div>
                        @endif

                        <span class="by-option-label">Quantity</span>
                        <div class="by-qty" role="group" aria-label="Product quantity">
                            <button class="btn" type="button" wire:click="decrementQuantity" aria-label="Decrease quantity">
                                <i class="fa fa-minus"></i>
                            </button>
                            <input type="text" wire:model="quantityCount" readonly aria-label="Quantity">
                            <button class="btn" type="button" wire:click="incrementQuantity" aria-label="Increase quantity">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>

                        <div class="by-product-actions">
                            <button type="button" wire:click="addToCart({{ $product->id }})" class="btn btn-primary">
                                <i class="fa fa-shopping-cart"></i> Add to cart
                            </button>
                            <button type="button" wire:click="addToWishlist({{ $product->id }})" class="btn by-btn-soft">
                                <i class="fa fa-heart"></i> Add to wishlist
                            </button>
                        </div>

                        @if ($product->small_description)
                            <div class="by-soft-note">
                                <i class="fa-solid fa-circle-info"></i>
                                <div>{!! $product->small_description !!}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($product->description)
                <div class="by-desc-card">
                    <h4><i class="fa-solid fa-align-left me-2" style="color:var(--by-rose)"></i>Description</h4>
                    <p>{!! $product->description !!}</p>
                </div>
            @endif
        </div>
    </div>
</div>
