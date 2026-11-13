@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="py-4 py-md-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Order #{{ $order->id }} Details</h4>
                        <a href="{{ url('admin/orders') }}" class="btn btn-light btn-sm">Back to Orders</a>
                    </div>
                    <div class="card-body">
                        @if (session('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif
                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @php
                            $statusMsg = strtolower($order->status_message ?? $order->status);
                            $paymentStatus = 'Paid';
                            if ($statusMsg == 'cancelled') {
                                $paymentStatus = 'Refunded';
                            } elseif (strtolower($order->payment_mode) == 'cash on delivery' && $statusMsg != 'delivered') {
                                $paymentStatus = 'Unpaid';
                            }
                        @endphp
                        <div class="row mb-4">
                            <div class="col-md-6 border-end">
                                <h5 class="border-bottom pb-2 mb-3">Order Information</h5>
                                <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                                <p><strong>Invoice Number:</strong> INV-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p><strong>Tracking Number:</strong> {{ $order->tracking_no ?? 'N/A' }}</p>
                                <p><strong>Order Date & Time:</strong> {{ $order->created_at->format('d-m-Y h:i A') }}</p>
                                <p><strong>Order Status:</strong> {{ ucfirst($order->status_message ?? $order->status) }}</p>
                                <p><strong>Payment Status:</strong> {{ $paymentStatus }}</p>
                                <p><strong>Payment Method:</strong> {{ $order->payment_mode ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2 mb-3">Customer Information</h5>
                                <p><strong>Name:</strong> {{ $order->fullname ?? ($order->user->name ?? 'Guest') }}</p>
                                <p><strong>Email:</strong> {{ $order->email ?? ($order->user->email ?? 'N/A') }}</p>
                                <p><strong>Phone:</strong> {{ $order->phone ?? ($order->user->phone ?? 'N/A') }}</p>
                                <p><strong>Pincode:</strong> {{ $order->pincode ?? 'N/A' }}</p>
                                <p><strong>Address:</strong> {{ $order->address ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="card border mb-4">
                            <div class="card-body bg-white">
                                <h5 class="border-bottom pb-2 mb-3">Update Order Status</h5>
                                <form action="{{ url('admin/orders/' . $order->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row align-items-center">
                                        <div class="col-md-4 mb-2">
                                            <label for="order_status" class="form-label fw-bold">Order Status</label>
                                            <select name="order_status" id="order_status" class="form-select">
                                                <option value="in progress" {{ $statusMsg == 'in progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="pending" {{ $statusMsg == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ $statusMsg == 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="completed" {{ $statusMsg == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="shipped" {{ $statusMsg == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ $statusMsg == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="cancelled" {{ $statusMsg == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                <option value="returned" {{ $statusMsg == 'returned' ? 'selected' : '' }}>Returned</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2 mt-md-4">
                                            <button type="submit" class="btn btn-primary text-white">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <h5 class="mb-3 border-bottom pb-2">Ordered Products</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @foreach($order->orderItems as $index => $item)
                                        @php
                                            $itemTotal = $item->quantity * $item->price;
                                            $grandTotal += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product->name ?? 'Unknown Product' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>TK {{ number_format($item->price, 2) }}</td>
                                            <td>TK {{ number_format($itemTotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Grand Total</td>
                                        <td class="fw-bold">TK {{ number_format($grandTotal, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
