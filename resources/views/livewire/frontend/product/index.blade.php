<div>
    <div class="row g-4">
        {{-- Filters --}}
        <div class="col-lg-3 by-filters">
            <div class="by-filter-card">
                <div class="by-filter-title"><i class="fa-solid fa-tags"></i> Brands</div>
                @forelse ($catagory->brands as $brandItem)
                    <label class="by-filter-option">
                        <input type="checkbox" class="form-check-input" wire:model="brandInputs"
                            value="{{ $brandItem->name }}" wire:change="logBrandInputs" />
                        <span>{{ $brandItem->name }}</span>
                    </label>
                @empty
                    <p class="text-muted mb-0" style="font-size:.84rem;">No brands listed for this category yet.</p>
                @endforelse
            </div>

            <div class="by-filter-card">
                <div class="by-filter-title"><i class="fa-solid fa-arrow-down-wide-short"></i> Sort by price</div>
                <label class="by-filter-option">
                    <input type="radio" class="form-check-input" name="priceSort" wire:model="priceInput"
                        value="high-to-low" wire:change="logpriceInput" />
                    <span>High to low</span>
                </label>
                <label class="by-filter-option">
                    <input type="radio" class="form-check-input" name="priceSort" wire:model="priceInput"
                        value="low-to-high" wire:change="logpriceInput" />
                    <span>Low to high</span>
                </label>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div class="row g-3 g-lg-4">
                @forelse($products as $productItem)
                    <div class="col-6 col-md-6 col-xl-4">
                        <article class="by-product-card">
                            <a href="{{ url('/collections/' . $productItem->catagory->slug . '/' . $productItem->slug) }}"
                               class="by-product-media" aria-label="{{ $productItem->name }}">
                                @if ($productItem->productImages->count() > 0)
                                    <img src="{{ asset($productItem->productImages[0]->image) }}"
                                         alt="{{ $productItem->name }}" loading="lazy">
                                @else
                                    <img src="{{ asset('assets/images/placeholder.png') }}"
                                         alt="{{ $productItem->name }}" loading="lazy">
                                @endif

                                @if ($productItem->quantity > 0)
                                    <span class="by-badge-stock"><i class="fa-solid fa-circle-check"></i> In stock</span>
                                @else
                                    <span class="by-badge-stock is-out"><i class="fa-solid fa-circle-xmark"></i> Out of stock</span>
                                @endif

                                @if ($productItem->selling_price > 0 && $productItem->original_price > $productItem->selling_price)
                                    <span class="by-badge-sale">
                                        -{{ round((1 - $productItem->selling_price / $productItem->original_price) * 100) }}%
                                    </span>
                                @endif
                            </a>

                            <div class="by-product-body">
                                <div class="by-product-brand">{{ $productItem->catagory->name ?? ($setting->website_name ?? 'Buyora') }}</div>
                                <a href="{{ url('/collections/' . $productItem->catagory->slug . '/' . $productItem->slug) }}"
                                   class="by-product-name" title="{{ $productItem->name }}">
                                    {{ $productItem->name }}
                                </a>

                                <div class="by-price-row">
                                    <span class="by-price">Tk {{ number_format($productItem->selling_price > 0 ? $productItem->selling_price : $productItem->original_price) }}</span>
                                    @if ($productItem->selling_price > 0 && $productItem->original_price > $productItem->selling_price)
                                        <span class="by-price-old">Tk {{ number_format($productItem->original_price) }}</span>
                                    @endif
                                </div>

                                <a href="{{ url('/collections/' . $productItem->catagory->slug . '/' . $productItem->slug) }}"
                                   class="btn btn-primary by-product-btn">
                                    View product <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="by-empty">
                            <div class="by-empty-icon"><i class="fa-solid fa-box-open"></i></div>
                            <h3>No products available</h3>
                            <p>We could not find any products for {{ $catagory->name }} right now. Try another category or check back soon.</p>
                            <a href="{{ url('/collections') }}" class="btn btn-primary">Browse all categories</a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
