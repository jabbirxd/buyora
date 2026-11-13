<div>
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="{{ url('/cart') }}">Cart</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Checkout</span>
            </div>
            <span class="by-eyebrow">Last step</span>
            <h1>Checkout</h1>
            <p>Tell us where to deliver and choose how you would like to pay.</p>
        </div>
    </div>

    <div class="by-checkout">
        <div class="container">
            @if ($paymentError)
                <div class="alert alert-danger" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $paymentError }}
                </div>
            @endif

            @if ($this->totalProductAmount != '0')
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="by-checkout-card">
                            <h4><i class="fa-solid fa-location-dot"></i> Delivery details</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full name</label>
                                    <input type="text" wire:model.defer="fullname" id="fullname" autocomplete="off"
                                        class="form-control" placeholder="Enter full name" />
                                    @error('fullname')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone number</label>
                                    <input type="tel" wire:model.defer="phone" id="phone" autocomplete="off" class="form-control"
                                        placeholder="Enter phone number" />
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email address</label>
                                    <input type="email" wire:model.defer="email" id="email" autocomplete="off" class="form-control"
                                        placeholder="Enter email address" />
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pin-code (Zip-code)</label>
                                    <input type="text" wire:model.defer="pincode" id="pincode" autocomplete="off" class="form-control"
                                        placeholder="Enter pin-code" />
                                    @error('pincode')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-0">
                                    <label class="form-label">Full address</label>
                                    <textarea wire:model.defer="address" id="address" autocomplete="off" class="form-control" rows="3"
                                        placeholder="House, road, area, city"></textarea>
                                    @error('address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="by-checkout-card">
                            <h4><i class="fa-solid fa-wallet"></i> Payment method</h4>
                            <div class="d-md-flex align-items-start align-items-md-stretch">
                                <div class="nav col-md-4 flex-column nav-pills me-md-3" id="v-pills-tab"
                                    role="tablist" aria-orientation="vertical">
                                    <button wire:loading.attr="disabled" wire:click="setTab('cod')"
                                        class="nav-link flex-md-fill {{ $activeTab === 'cod' ? 'active' : '' }}" id="cashOnDeliveryTab-tab"
                                        type="button" role="tab" aria-controls="cashOnDeliveryTab"
                                        aria-selected="{{ $activeTab === 'cod' ? 'true' : 'false' }}">
                                        <i class="fa-solid fa-money-bill-wave me-2"></i> Cash on delivery
                                    </button>
                                    <button wire:loading.attr="disabled" wire:click="setTab('online')"
                                        class="nav-link flex-md-fill mb-md-0 {{ $activeTab === 'online' ? 'active' : '' }}" id="onlinePayment-tab"
                                        type="button" role="tab" aria-controls="onlinePayment"
                                        aria-selected="{{ $activeTab === 'online' ? 'true' : 'false' }}">
                                        <i class="fa-solid fa-credit-card me-2"></i> Online payment
                                    </button>
                                </div>
                                <div class="tab-content col-md-8" id="v-pills-tabContent">
                                    <div class="tab-pane fade {{ $activeTab === 'cod' ? 'show active' : '' }}" id="cashOnDeliveryTab" role="tabpanel"
                                        aria-labelledby="cashOnDeliveryTab-tab" tabindex="0">
                                        <h6>Pay when your order arrives</h6>
                                        <p class="by-checkout-hint mb-0">No card needed — pay in cash when your parcel is delivered.</p>
                                        <hr />
                                        @if ($orderError)
                                            <div class="alert alert-danger" role="alert">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $orderError }}
                                            </div>
                                        @endif
                                        @if ($errors->any())
                                            <p class="text-danger small">Please fix the highlighted fields above.</p>
                                        @endif
                                        <button type="button" wire:loading.attr="disabled" wire:click="codOrder"
                                            class="btn btn-primary">
                                            <span wire:loading.remove wire:target="codOrder">
                                                Place order <i class="fa-solid fa-arrow-right"></i>
                                            </span>
                                            <span wire:loading wire:target="codOrder">
                                                Placing order…
                                            </span>
                                        </button>
                                    </div>

                                    <div class="tab-pane fade {{ $activeTab === 'online' ? 'show active' : '' }}" id="onlinePayment" role="tabpanel"
                                        aria-labelledby="onlinePayment-tab" tabindex="0">
                                        <h6>Pay securely online</h6>
                                        <p class="by-checkout-hint mb-0">You will be redirected to a secure checkout page to pay.</p>
                                        <hr />
                                        @if ($errors->any())
                                            <p class="text-danger small">Please fix the highlighted fields above.</p>
                                        @endif
                                        <button type="button" wire:loading.attr="disabled" wire:click="startOnlinePayment"
                                            class="btn btn-primary">
                                            <span wire:loading.remove wire:target="startOnlinePayment">
                                                Pay Online (bKash / Nagad / Card) <i class="fa-solid fa-arrow-right"></i>
                                            </span>
                                            <span wire:loading wire:target="startOnlinePayment">
                                                Redirecting to payment…
                                            </span>
                                        </button>
                                        <div class="mt-2">
                                            <button type="button" wire:loading.attr="disabled" wire:click="startAamarpayPayment"
                                                class="btn btn-primary">
                                                <span wire:loading.remove wire:target="startAamarpayPayment">
                                                    Pay with aamarPay <i class="fa-solid fa-arrow-right"></i>
                                                </span>
                                                <span wire:loading wire:target="startAamarpayPayment">
                                                    Redirecting to payment…
                                                </span>
                                            </button>
                                        </div>
                                        <p class="by-checkout-hint mt-3 mb-0">
                                            <i class="fa-solid fa-flask"></i> Sandbox mode — test payments only, no real money.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="by-summary-card">
                            <h3>Order summary</h3>
                            <div class="by-summary-row">
                                <span>Items total</span>
                                <strong>TK {{ number_format($this->totalProductAmount) }}</strong>
                            </div>
                            <div class="by-summary-row">
                                <span>Delivery</span>
                                <strong>Included</strong>
                            </div>
                            <div class="by-summary-total">
                                <span>Total to pay</span>
                                <strong>TK {{ number_format($this->totalProductAmount) }}</strong>
                            </div>
                            <div class="by-summary-note">
                                <i class="fa-solid fa-truck-fast"></i>
                                <span>Items will be delivered in 3–5 days. Tax and other charges are included.</span>
                            </div>
                            <div class="by-summary-note">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Your details are only used to fulfil this order.</span>
                            </div>
                            <a href="{{ url('/cart') }}" class="btn by-btn-soft w-100 mt-3">
                                <i class="fa-solid fa-arrow-left"></i> Back to cart
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="by-empty">
                    <div class="by-empty-icon"><i class="fa-solid fa-basket-shopping"></i></div>
                    <h3>Nothing to checkout yet</h3>
                    <p>Your cart is empty — add something lovely first.</p>
                    <a href="{{ url('/collections') }}" class="btn btn-primary">Shop now</a>
                </div>
            @endif
        </div>
    </div>
</div>
