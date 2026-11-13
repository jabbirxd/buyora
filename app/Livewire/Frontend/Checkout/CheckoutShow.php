<?php

namespace App\Livewire\Frontend\Checkout;

use App\Http\Controllers\Frontend\PaymentController;
use App\Models\Cart;
use App\Services\OrderPlacementService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Throwable;

class CheckoutShow extends Component
{
    public $carts, $totalProductAmount = 0;

    // The component is the single source of truth for these five fields.
    public $fullname, $email, $phone, $pincode, $address;

    /** 'cod' | 'online' */
    public $activeTab = 'cod';

    public $paymentError = null;
    public $orderError = null;

    public function mount()
    {
        // Fill the form ONCE. render() never refills or overwrites any field.
        $pending = session('pending_checkout');

        if (is_array($pending) && (int) ($pending['user_id'] ?? 0) === (int) auth()->id()) {
            // Back on /checkout after an online attempt: restore what the customer entered.
            foreach (['fullname', 'email', 'phone', 'pincode', 'address'] as $field) {
                $this->{$field} = $pending[$field] ?? null;
            }
            $this->activeTab = 'online';
        } else {
            $this->fullname = auth()->user()->name;
            $this->email = auth()->user()->email;
        }

        if (session()->has('error')) {
            $this->paymentError = session('error');
        } elseif (request()->query('payment') === 'cancelled') {
            $this->paymentError = 'Payment was cancelled. No order was placed and you were not charged.';
        } elseif (request()->query('payment') === 'failed') {
            $this->paymentError = 'Payment could not be completed. No order was placed. Please try again.';
        }

        if ($this->paymentError) {
            $this->activeTab = 'online';
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab === 'online' ? 'online' : 'cod';
    }

    public function rules()
    {
        return [
            'fullname' => 'required|string|max:121',
            'email' => 'required|email|max:121',
            // Bangladeshi mobile: 01XXXXXXXXX, optionally with +880 / 880, spaces or dashes.
            'phone' => ['required', 'string', 'max:20', 'regex:/^(?:\+?88[\s-]?)?(?:0[\s-]?)?1[3-9](?:[\s-]?\d){8}$/'],
            // Postcode: 3-10 letters/digits, spaces or dashes (Bangladeshi postcodes are 4 digits).
            'pincode' => ['required', 'string', 'regex:/^[A-Za-z0-9][A-Za-z0-9 \-]{2,9}$/'],
            'address' => 'required|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'phone.regex' => 'Enter a valid mobile number, e.g. 01712345678 or +880 1712-345678.',
            'pincode.regex' => 'Enter a valid postcode (3 to 10 letters or digits), e.g. 1207.',
        ];
    }

    protected function details(): array
    {
        return [
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'pincode' => $this->pincode,
            'address' => $this->address,
        ];
    }

    public function codOrder()
    {
        $this->activeTab = 'cod';
        $this->orderError = null;
        $this->validate();

        try {
            app(OrderPlacementService::class)->place(auth()->id(), $this->details(), 'Cash On Delivery');
        } catch (Throwable $e) {
            Log::error('COD order failed', ['user_id' => auth()->id(), 'exception' => $e]);
            $this->orderError = 'Your order could not be placed: ' . $e->getMessage();
            return null;
        }

        session()->forget('pending_checkout');

        return $this->redirectRoute('thank-you');
    }

    /** SSLCommerz: bKash / Nagad / cards. */
    public function startOnlinePayment()
    {
        return $this->startGatewayPayment('sslcommerz', 'payment.initiate');
    }

    public function startAamarpayPayment()
    {
        return $this->startGatewayPayment('aamarpay', 'payment.aamarpay.initiate');
    }

    protected function startGatewayPayment(string $gateway, string $initiateRoute)
    {
        $this->activeTab = 'online';
        $this->paymentError = null;
        $this->validate();

        $total = app(OrderPlacementService::class)->cartTotal(auth()->id());
        if ($total <= 0) {
            $this->paymentError = 'Your cart is empty.';
            return null;
        }

        $trackingNo = 'buyora-' . Str::random(10);
        $pending = $this->details() + [
            'user_id' => auth()->id(),
            'gateway' => $gateway,
            'tracking_no' => $trackingNo,
            'amount' => PaymentController::money($total),
        ];

        session()->put('pending_checkout', $pending);
        Cache::put(PaymentController::cacheKey($gateway, $trackingNo), $pending, now()->addMinutes(PaymentController::CACHE_MINUTES));

        return $this->redirectRoute($initiateRoute);
    }

    public function totalProductAmount()
    {
        $this->totalProductAmount = 0;
        $this->carts = Cart::where('user_id', auth()->id())->get();
        foreach ($this->carts as $cartItem) {
            $this->totalProductAmount += $cartItem->product->selling_price * $cartItem->quantity;
        }
        return $this->totalProductAmount;
    }

    public function render()
    {
        // Only the cart total is recalculated here; form fields are never touched.
        $this->totalProductAmount = $this->totalProductAmount();

        return view('livewire.frontend.checkout.checkout-show', [
            'totalProductAmount' => $this->totalProductAmount,
        ]);
    }
}
