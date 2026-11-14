<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Services\AamarpayService;
use App\Services\OrderPlacementService;
use App\Services\SslcommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * SSLCommerz + aamarPay (sandbox) payments.
 *
 * An order is ONLY created after the gateway confirms the payment server-to-server
 * (SSLCommerz validation API / aamarPay trxcheck API). A success redirect or callback
 * POST on its own is never trusted.
 *
 * The gateway callbacks (success/fail/cancel/ipn) arrive as cross-site POSTs, so the
 * browser does not send the SameSite=Lax session cookie with them. They therefore run
 * without the session middleware and read the pending checkout from the cache,
 * keyed by tran_id. The customer's own session cookie is sent again on the GET redirect
 * to thank-you / checkout that follows.
 */
class PaymentController extends Controller
{
    public const CACHE_MINUTES = 30;

    public function __construct(
        protected OrderPlacementService $orders,
        protected SslcommerzService $sslcommerz,
        protected AamarpayService $aamarpay,
    ) {
    }

    public static function cacheKey(string $gateway, string $tranId): string
    {
        return $gateway . '_pending_' . $tranId;
    }

    /* ------------------------------------------------------------------ */
    /* SSLCommerz                                                          */
    /* ------------------------------------------------------------------ */

    public function sslcommerzInitiate(Request $request)
    {
        if (!$this->sslcommerz->configured()) {
            return $this->checkoutError('Online payment is not configured.');
        }

        $pending = $this->pendingForInitiate($request, 'sslcommerz');
        if (!is_array($pending)) {
            return $pending; // redirect response with an error
        }

        $tranId = $pending['tracking_no'];
        $siteName = Setting::first()?->website_name ?? 'Buyora';

        try {
            $response = $this->sslcommerz->initiate([
                'total_amount' => $pending['amount'],
                'currency' => 'BDT',
                'tran_id' => $tranId,
                'success_url' => route('payment.sslcommerz.success'),
                'fail_url' => route('payment.sslcommerz.fail'),
                'cancel_url' => route('payment.sslcommerz.cancel'),
                'ipn_url' => route('payment.sslcommerz.ipn'),
                'cus_name' => $pending['fullname'],
                'cus_email' => $pending['email'],
                'cus_phone' => $pending['phone'],
                'cus_add1' => $pending['address'],
                'cus_city' => self::cityFromAddress($pending['address']),
                'cus_postcode' => $pending['pincode'],
                'cus_country' => 'Bangladesh',
                'shipping_method' => 'Courier',
                'ship_name' => $pending['fullname'],
                'ship_add1' => $pending['address'],
                'ship_city' => self::cityFromAddress($pending['address']),
                'ship_postcode' => $pending['pincode'],
                'ship_country' => 'Bangladesh',
                'num_of_item' => 1,
                'product_name' => $siteName . ' order ' . $tranId,
                'product_category' => 'General',
                'product_profile' => 'general',
            ]);
        } catch (Throwable $e) {
            Log::error('SSLCommerz initiate request failed', ['tran_id' => $tranId, 'error' => $e->getMessage()]);
            return $this->checkoutError('Could not reach the payment gateway. Please try again.');
        }

        if (($response['status'] ?? null) !== 'SUCCESS' || empty($response['GatewayPageURL'])) {
            Log::warning('SSLCommerz initiate rejected', ['tran_id' => $tranId, 'response' => $response]);
            return $this->checkoutError('The payment gateway could not start the payment. Please try again.');
        }

        return redirect()->away($response['GatewayPageURL']);
    }

    public function sslcommerzSuccess(Request $request)
    {
        $valId = (string) $request->input('val_id', '');
        $tranId = (string) $request->input('tran_id', '');

        if ($valId === '' || $tranId === '') {
            Log::warning('SSLCommerz success callback missing val_id/tran_id', ['post' => $request->all()]);
            return $this->callbackError('failed');
        }

        $result = $this->completeSslcommerz($valId, $tranId);

        return $result === true ? redirect()->route('thank-you') : $this->callbackError('failed');
    }

    public function sslcommerzFail(Request $request)
    {
        Log::info('SSLCommerz payment failed', ['tran_id' => $request->input('tran_id')]);
        return $this->callbackError('failed');
    }

    public function sslcommerzCancel(Request $request)
    {
        Log::info('SSLCommerz payment cancelled', ['tran_id' => $request->input('tran_id')]);
        return $this->callbackError('cancelled');
    }

    /** Server-to-server IPN: no login, no session. */
    public function sslcommerzIpn(Request $request)
    {
        $valId = (string) $request->input('val_id', '');
        $tranId = (string) $request->input('tran_id', '');

        if ($valId === '' || $tranId === '') {
            Log::warning('SSLCommerz IPN missing val_id/tran_id', ['post' => $request->all()]);
            return response('FAILED', 400);
        }

        return $this->completeSslcommerz($valId, $tranId) === true
            ? response('OK', 200)
            : response('FAILED', 400);
    }

    /** Verify with SSLCommerz and place the order. Returns true when an order exists for $tranId. */
    protected function completeSslcommerz(string $valId, string $tranId): bool
    {
        if (Order::where('tracking_no', $tranId)->exists()) {
            return true;
        }

        $pending = Cache::get(self::cacheKey('sslcommerz', $tranId));
        if (!is_array($pending)) {
            Log::warning('SSLCommerz: no pending checkout for tran_id (expired or unknown)', ['tran_id' => $tranId]);
            return false;
        }

        try {
            $v = $this->sslcommerz->validate($valId);
        } catch (Throwable $e) {
            Log::error('SSLCommerz validation request failed', ['tran_id' => $tranId, 'error' => $e->getMessage()]);
            return false;
        }

        $valid = in_array($v['status'] ?? null, ['VALID', 'VALIDATED'], true)
            && (string) ($v['tran_id'] ?? '') === $tranId
            && (string) ($v['currency'] ?? '') === 'BDT'
            && self::money($v['amount'] ?? null) === $pending['amount'];

        if (!$valid) {
            Log::warning('SSLCommerz validation failed', ['tran_id' => $tranId, 'pending_amount' => $pending['amount'], 'response' => $v]);
            return false;
        }

        $paymentId = (string) (($v['bank_tran_id'] ?? '') ?: $valId);

        return $this->placePaidOrder('sslcommerz', 'Paid by SSLCommerz', $pending, $tranId, $paymentId, $v);
    }

    /* ------------------------------------------------------------------ */
    /* aamarPay                                                            */
    /* ------------------------------------------------------------------ */

    public function aamarpayInitiate(Request $request)
    {
        if (!$this->aamarpay->configured()) {
            return $this->checkoutError('Online payment is not configured.');
        }

        $pending = $this->pendingForInitiate($request, 'aamarpay');
        if (!is_array($pending)) {
            return $pending;
        }

        $tranId = $pending['tracking_no'];
        $siteName = Setting::first()?->website_name ?? 'Buyora';

        try {
            $response = $this->aamarpay->initiate([
                'tran_id' => $tranId,
                'amount' => $pending['amount'],
                'currency' => 'BDT',
                'desc' => $siteName . ' order ' . $tranId,
                'cus_name' => $pending['fullname'],
                'cus_email' => $pending['email'],
                'cus_phone' => $pending['phone'],
                'cus_add1' => $pending['address'],
                'cus_city' => self::cityFromAddress($pending['address']),
                'cus_country' => 'Bangladesh',
                'success_url' => route('payment.aamarpay.success'),
                'fail_url' => route('payment.aamarpay.fail'),
                'cancel_url' => route('payment.aamarpay.cancel'),
            ]);
        } catch (Throwable $e) {
            Log::error('aamarPay initiate request failed', ['tran_id' => $tranId, 'error' => $e->getMessage()]);
            return $this->checkoutError('Could not reach the payment gateway. Please try again.');
        }

        if ((string) ($response['result'] ?? '') !== 'true' || empty($response['payment_url'])) {
            Log::warning('aamarPay initiate rejected', ['tran_id' => $tranId, 'response' => $response]);
            return $this->checkoutError('The payment gateway could not start the payment. Please try again.');
        }

        return redirect()->away($response['payment_url']);
    }

    public function aamarpaySuccess(Request $request)
    {
        $tranId = (string) ($request->input('mer_txnid') ?: $request->input('tran_id', ''));

        if ($tranId === '') {
            Log::warning('aamarPay success callback missing mer_txnid/tran_id', ['post' => $request->all()]);
            return $this->callbackError('failed');
        }

        if (Order::where('tracking_no', $tranId)->exists()) {
            return redirect()->route('thank-you');
        }

        $pending = Cache::get(self::cacheKey('aamarpay', $tranId));
        if (!is_array($pending)) {
            Log::warning('aamarPay: no pending checkout for tran_id (expired or unknown)', ['tran_id' => $tranId]);
            return $this->callbackError('failed');
        }

        try {
            $v = $this->aamarpay->verify($tranId);
        } catch (Throwable $e) {
            Log::error('aamarPay verify request failed', ['tran_id' => $tranId, 'error' => $e->getMessage()]);
            return $this->callbackError('failed');
        }

        $valid = (string) ($v['status_code'] ?? '') === '2'
            && (string) ($v['pay_status'] ?? '') === 'Successful'
            && (string) ($v['mer_txnid'] ?? '') === $tranId
            && self::money($v['amount'] ?? null) === $pending['amount']
            && (string) ($v['currency'] ?? '') === 'BDT';

        if (!$valid) {
            Log::warning('aamarPay verification failed', ['tran_id' => $tranId, 'pending_amount' => $pending['amount'], 'response' => $v]);
            return $this->callbackError('failed');
        }

        $paymentId = (string) (($v['pg_txnid'] ?? '') ?: $tranId);

        return $this->placePaidOrder('aamarpay', 'Paid by aamarPay', $pending, $tranId, $paymentId, $v)
            ? redirect()->route('thank-you')
            : $this->callbackError('failed');
    }

    public function aamarpayFail(Request $request)
    {
        Log::info('aamarPay payment failed', ['tran_id' => $request->input('mer_txnid') ?: $request->input('tran_id')]);
        return $this->callbackError('failed');
    }

    public function aamarpayCancel(Request $request)
    {
        Log::info('aamarPay payment cancelled', ['tran_id' => $request->input('mer_txnid') ?: $request->input('tran_id')]);
        return $this->callbackError('cancelled');
    }

    /* ------------------------------------------------------------------ */
    /* Shared                                                              */
    /* ------------------------------------------------------------------ */

    /**
     * Read pending checkout from the session for an initiate request and re-check it.
     * Returns the pending array, or a redirect response on error.
     */
    protected function pendingForInitiate(Request $request, string $gateway)
    {
        $pending = $request->session()->get('pending_checkout');

        if (!is_array($pending) || ($pending['gateway'] ?? null) !== $gateway || empty($pending['tracking_no'])) {
            return $this->checkoutError('Your payment session has expired. Please try again.');
        }

        if ((int) $pending['user_id'] !== (int) auth()->id()) {
            Log::warning("{$gateway}: pending checkout user mismatch", ['pending_user' => $pending['user_id'], 'auth_user' => auth()->id()]);
            $request->session()->forget('pending_checkout');
            return $this->checkoutError('Your payment session is invalid. Please try again.');
        }

        if (Order::where('tracking_no', $pending['tracking_no'])->exists()) {
            $request->session()->forget('pending_checkout');
            return $this->checkoutError('This payment has already been completed.');
        }

        $freshTotal = self::money($this->orders->cartTotal((int) auth()->id()));
        if ((float) $freshTotal <= 0 || $freshTotal !== $pending['amount']) {
            Log::warning("{$gateway}: cart total changed before payment", ['pending_amount' => $pending['amount'], 'cart_total' => $freshTotal]);
            $request->session()->forget('pending_checkout');
            return $this->checkoutError('Your cart changed. Please review your order and try again.');
        }

        Cache::put(self::cacheKey($gateway, $pending['tracking_no']), $pending, now()->addMinutes(self::CACHE_MINUTES));

        return $pending;
    }

    /** Place an order for a payment the gateway has already confirmed server-side. */
    protected function placePaidOrder(string $gateway, string $paymentMode, array $pending, string $tranId, string $paymentId, array $gatewayResponse): bool
    {
        $userId = (int) $pending['user_id'];

        // The order is built from the cart, so the cart must still match what was paid.
        $cartTotal = self::money($this->orders->cartTotal($userId));
        if ($cartTotal !== $pending['amount']) {
            Log::critical("{$gateway}: PAID but cart total no longer matches; order NOT created - refund/manual review needed", [
                'tran_id' => $tranId, 'paid' => $pending['amount'], 'cart_total' => $cartTotal, 'response' => $gatewayResponse,
            ]);
            return Order::where('tracking_no', $tranId)->exists();
        }

        try {
            $this->orders->place($userId, [
                'fullname' => $pending['fullname'],
                'email' => $pending['email'],
                'phone' => $pending['phone'],
                'pincode' => $pending['pincode'],
                'address' => $pending['address'],
            ], $paymentMode, $paymentId, $tranId);
        } catch (Throwable $e) {
            // A concurrent callback (success redirect vs IPN) may have created it first.
            if (Order::where('tracking_no', $tranId)->exists()) {
                Cache::forget(self::cacheKey($gateway, $tranId));
                return true;
            }
            Log::critical("{$gateway}: PAID but order placement failed - refund/manual review needed", [
                'tran_id' => $tranId, 'error' => $e->getMessage(), 'response' => $gatewayResponse,
            ]);
            return false;
        }

        Cache::forget(self::cacheKey($gateway, $tranId));

        return true;
    }

    /** Error for requests that carry the customer's session (initiate). */
    protected function checkoutError(string $message)
    {
        return redirect()->route('checkout')->with('error', $message);
    }

    /** Error for gateway callbacks, which run without a session (no flash available). */
    protected function callbackError(string $reason)
    {
        return redirect()->route('checkout', ['payment' => $reason]);
    }

    /**
     * The customer's delivery city for the gateway's city field.
     *
     * Checkout has no separate city field: the customer types one address
     * ("House, road, area, city"), so the city is read from the end of it,
     * skipping a trailing country or postcode ("..., Dhaka-1207, Bangladesh" -> "Dhaka").
     * If the address has no separators, the address itself is sent (shortened).
     */
    public static function cityFromAddress(?string $address): string
    {
        $parts = preg_split('/[,;\r\n]+/', (string) $address, -1, PREG_SPLIT_NO_EMPTY);
        $parts = array_values(array_filter(array_map('trim', $parts), fn ($p) => $p !== ''));

        for ($i = count($parts) - 1; $i >= 0; $i--) {
            // Drop a postcode stuck to the city: "Dhaka-1207", "Dhaka 1207", "1207 Dhaka".
            $part = trim(preg_replace('/^\d{3,}[\s\-]*|[\s\-]*\d{3,}$/', '', $parts[$i]), " \t-.");

            if ($part === '' || !preg_match('/\pL/u', $part)) {
                continue; // only a postcode
            }
            if (in_array(mb_strtolower($part), ['bangladesh', 'bd'], true)) {
                continue; // country, not city
            }

            return mb_substr($part, 0, 50);
        }

        return 'N/A';
    }

    public static function money($value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
