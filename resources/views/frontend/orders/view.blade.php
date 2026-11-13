@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="{{ url('orders') }}">My Orders</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Order #{{ $order->id }}</span>
            </div>
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                <div>
                    <span class="by-eyebrow">Order details</span>
                    <h1>Order #{{ $order->id }}</h1>
                    <p>Placed on {{ $order->created_at->timezone('Asia/Dhaka')->format('d-m-Y h:i A') }}</p>
                </div>
                <a href="{{ url('orders') }}" class="btn btn-light">
                    <i class="fa-solid fa-arrow-left"></i> Back to orders
                </a>
            </div>
        </div>
    </div>

    <section class="by-section--tight">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="by-panel h-100">
                        <div class="by-panel-head">
                            <h3><i class="fa-solid fa-receipt me-2" style="color:var(--by-rose)"></i>Order information</h3>
                        </div>
                        <div class="by-order-info"><span>Order ID</span><strong>#{{ $order->id }}</strong></div>
                        <div class="by-order-info"><span>Tracking no</span><strong>{{ $order->tracking_no }}</strong></div>
                        <div class="by-order-info"><span>Order date</span><strong>{{ $order->created_at->timezone('Asia/Dhaka')->format('d-m-Y') }}</strong></div>
                        <div class="by-order-info"><span>Payment mode</span><strong>{{ $order->payment_mode }}</strong></div>
                        <div class="by-order-info">
                            <span>Status</span>
                            <strong>
                                <span class="badge
                                    @if ($order->status_message == 'completed') bg-success
                                    @elseif($order->status_message == 'cancelled') bg-danger
                                    @else bg-warning text-dark @endif">
                                    {{ ucfirst($order->status_message) }}
                                </span>
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="by-panel h-100">
                        <div class="by-panel-head">
                            <h3><i class="fa-solid fa-user me-2" style="color:var(--by-rose)"></i>Customer information</h3>
                        </div>
                        <div class="by-order-info"><span>Full name</span><strong>{{ $order->fullname }}</strong></div>
                        <div class="by-order-info"><span>Email</span><strong>{{ $order->email }}</strong></div>
                        <div class="by-order-info"><span>Phone</span><strong>{{ $order->phone }}</strong></div>
                        <div class="by-order-info"><span>Pincode</span><strong>{{ $order->pincode }}</strong></div>
                        <div class="by-order-info"><span>Address</span><strong>{{ $order->address }}</strong></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="by-panel h-100">
                        <div class="by-panel-head">
                            <h3><i class="fa-solid fa-bag-shopping me-2" style="color:var(--by-rose)"></i>Billing information</h3>
                        </div>
                        @if ($order->orderItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderItems as $item)
                                            <tr>
                                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td class="text-end">Tk {{ number_format($item->price * $item->quantity, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @php
                                $calculatedTotal = $order->orderItems->sum(function ($item) {
                                    return $item->price * $item->quantity;
                                });
                            @endphp
                            <div class="by-summary-total mt-2">
                                <span>Total</span>
                                <strong>Tk {{ number_format($calculatedTotal, 2) }}</strong>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">No items found in this order.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
