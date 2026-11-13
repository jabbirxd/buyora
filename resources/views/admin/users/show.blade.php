@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">User Details</h4>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">Back to Users</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">User Information</h5>
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                        <p><strong>Role:</strong> 
                            @if ($user->roll_as == 1)
                                <span class="badge bg-success text-white">Admin</span>
                            @else
                                <span class="badge bg-primary text-white">User</span>
                            @endif
                        </p>
                        <p><strong>Registration Date:</strong> {{ $user->created_at->format('d-m-Y H:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2 mb-3">Statistics</h5>
                        <p><strong>Total Orders:</strong> {{ $totalOrders }}</p>
                        <p><strong>Total Amount Spent:</strong> ${{ number_format($totalAmountSpent, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Orders Placed by {{ $user->name }}</h5>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Invoice Number</th>
                                    <th>Order Date & Time</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    @php
                                        // Calculate total amount for this order
                                        $orderTotal = 0;
                                        foreach($order->orderItems as $item) {
                                            $orderTotal += ($item->quantity * $item->price);
                                        }

                                        // Status badge mapping
                                        $statusClass = 'bg-secondary'; // default
                                        $statusMsg = strtolower($order->status_message ?? $order->status);
                                        if($statusMsg == 'pending') $statusClass = 'bg-warning text-dark';
                                        elseif($statusMsg == 'processing') $statusClass = 'bg-primary';
                                        elseif($statusMsg == 'confirmed') $statusClass = 'bg-info text-dark';
                                        elseif($statusMsg == 'shipped') $statusClass = 'bg-primary'; // purple is not standard bs bg, using primary or we could inline style
                                        elseif($statusMsg == 'delivered') $statusClass = 'bg-success';
                                        elseif($statusMsg == 'cancelled') $statusClass = 'bg-danger';
                                        elseif($statusMsg == 'returned') $statusClass = 'bg-dark';

                                        // Payment status derivation
                                        $paymentStatus = 'Paid';
                                        $paymentClass = 'bg-success';
                                        if ($statusMsg == 'cancelled') {
                                            $paymentStatus = 'Refunded';
                                            $paymentClass = 'bg-danger';
                                        } elseif (strtolower($order->payment_mode) == 'cash on delivery' && $statusMsg != 'delivered') {
                                            $paymentStatus = 'Unpaid';
                                            $paymentClass = 'bg-warning text-dark';
                                        }
                                    @endphp
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>INV-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $order->created_at->format('d-m-Y h:i A') }}</td>
                                        <td><span class="badge {{ $statusClass }}" @if($statusMsg == 'shipped') style="background-color: purple;" @endif>{{ ucfirst($order->status_message ?? $order->status) }}</span></td>
                                        <td><span class="badge {{ $paymentClass }}">{{ $paymentStatus }}</span></td>
                                        <td>${{ number_format($orderTotal, 2) }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary text-white">View Order</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">This user has not placed any orders yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
