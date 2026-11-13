@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
    <div class="by-page-head">
        <div class="container">
            <div class="by-breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>My Orders</span>
            </div>
            <span class="by-eyebrow">Order history</span>
            <h1>My orders</h1>
            <p>Keep track of everything you have ordered from Buyora.</p>
        </div>
    </div>

    <section class="by-section--tight">
        <div class="container">
            <div class="by-panel">
                @if ($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Tracking No</th>
                                    <th>Username</th>
                                    <th>Payment Mode</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $item)
                                    <tr>
                                        <td><strong>#{{ $item->id }}</strong></td>
                                        <td>{{ $item->tracking_no }}</td>
                                        <td>{{ $item->fullname }}</td>
                                        <td>{{ $item->payment_mode }}</td>
                                        <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <span class="badge
                                                @if ($item->status_message == 'completed') bg-success
                                                @elseif($item->status_message == 'cancelled') bg-danger
                                                @else bg-warning text-dark @endif">
                                                {{ ucfirst($item->status_message ?: 'Pending') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ url('orders/' . $item->id) }}" class="btn btn-primary btn-sm">
                                                View <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="by-empty" style="border:none;box-shadow:none;">
                        <div class="by-empty-icon"><i class="fa-solid fa-receipt"></i></div>
                        <h3>No orders yet</h3>
                        <p>When you place an order, it will appear here with all the details.</p>
                        <a href="{{ url('/collections') }}" class="btn btn-primary">Start shopping</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
