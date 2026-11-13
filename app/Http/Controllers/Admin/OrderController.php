<?php

namespace App\Http\Controllers\Admin;
use App\Models\Order;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    
    // Show all orders
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    // Show a single order
    public function show($orderId)
    {
        // Admin can see any order, no user restriction
        $order = Order::with('user', 'orderItems.product')->findOrFail($orderId);

        // Use admin Blade
        return view('admin.orders.show', compact('order'));
    }

    // Update order status
    public function updateStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $status = $request->input('order_status') ?? $request->input('status') ?? $request->input('status_message');
        $request->merge(['order_status' => $status]);

        $request->validate([
            'order_status' => [
                'required',
                'string',
                'in:in progress,pending,processing,completed,shipped,delivered,cancelled,returned',
            ],
        ]);

        $order->update([
            'status_message' => $status,
        ]);

        return redirect()->back()->with('message', 'Order status updated successfully');
    }
}
