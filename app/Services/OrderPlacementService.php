<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class OrderPlacementService
{
    /**
     * Turn the user's cart into an order, atomically.
     *
     * $details: fullname, email, phone, pincode, address
     * $paymentMode: 'Cash On Delivery' | 'Paid by SSLCommerz' | 'Paid by aamarPay'
     */
    public function place(int $userId, array $details, string $paymentMode, ?string $paymentId = null, ?string $trackingNo = null): Order
    {
        return DB::transaction(function () use ($userId, $details, $paymentMode, $paymentId, $trackingNo) {
            // Lock the cart rows so two concurrent placements (e.g. browser success
            // redirect + IPN for the same payment) cannot both turn the same cart into an order.
            $carts = Cart::where('user_id', $userId)->lockForUpdate()->get();

            if ($carts->isEmpty()) {
                throw new RuntimeException('Cart is empty.');
            }

            if ($trackingNo !== null && Order::where('tracking_no', $trackingNo)->exists()) {
                throw new RuntimeException("Order {$trackingNo} already exists.");
            }

            $lines = [];
            foreach ($carts as $cartItem) {
                $product = Product::whereKey($cartItem->product_id)->lockForUpdate()->first();
                if (!$product) {
                    throw new RuntimeException("Product {$cartItem->product_id} no longer exists.");
                }
                if ((int) $product->quantity < (int) $cartItem->quantity) {
                    throw new RuntimeException("Not enough stock for product {$product->id}.");
                }

                $variant = null;
                if ($cartItem->product_color_id) {
                    $variant = ProductColor::whereKey($cartItem->product_color_id)->lockForUpdate()->first();
                    if (!$variant) {
                        throw new RuntimeException("Product variant {$cartItem->product_color_id} no longer exists.");
                    }
                    if ((int) $variant->quantity < (int) $cartItem->quantity) {
                        throw new RuntimeException("Not enough stock for product variant {$variant->id}.");
                    }
                }

                $lines[] = [$cartItem, $product, $variant];
            }

            $order = Order::create([
                'user_id' => $userId,
                'tracking_no' => $trackingNo ?? 'buyora-' . Str::random(10),
                'fullname' => $details['fullname'],
                'email' => $details['email'],
                'phone' => $details['phone'],
                'pincode' => $details['pincode'],
                'address' => $details['address'],
                'status_message' => 'in progress',
                'payment_mode' => $paymentMode,
                'payment_id' => $paymentId,
            ]);

            foreach ($lines as [$cartItem, $product, $variant]) {
                Orderitem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_color_id' => $cartItem->product_color_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->selling_price,
                ]);

                $product->decrement('quantity', $cartItem->quantity);
                if ($variant) {
                    $variant->decrement('quantity', $cartItem->quantity);
                }
            }

            Cart::where('user_id', $userId)->delete();

            return $order;
        });
    }

    /**
     * Server-side cart total. Gateways are always charged this, never a browser-supplied amount.
     */
    public function cartTotal(int $userId): float
    {
        $total = 0.0;
        foreach (Cart::with('product')->where('user_id', $userId)->get() as $cartItem) {
            if ($cartItem->product) {
                $total += (float) $cartItem->product->selling_price * (int) $cartItem->quantity;
            }
        }

        return $total;
    }
}
