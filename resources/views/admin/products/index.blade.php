@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12"></div>
    @if(session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <div class="card">
        <div class="card-header">
            <h3>Products
                <a href="{{ url('admin/products/create') }}" class="btn btn-primary btn-sm text-white float-end">Add Products</a>
            </h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Catagory</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if( $product->catagory)
                        {{ $product->catagory->name }}
                        @else
                        No Catagory
                        @endif
                       </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->selling_price }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>
                            {{ $product->status == '1' ? 'Hidden' : 'Visible' }}
                            <form action="{{ url('admin/products/' . $product->id . '/toggle-status') }}" method="POST" class="d-inline">
                                @csrf
                                @if ($product->status == '1')
                                    <button type="submit" class="btn btn-outline-primary btn-sm ms-2"><i class="mdi mdi-eye"></i> Show</button>
                                @else
                                    <button type="submit" class="btn btn-light btn-sm ms-2"><i class="mdi mdi-eye-off"></i> Hide</button>
                                @endif
                            </form>
                        </td>
                        <td>
                            <a href="{{ url('admin/products/'.$product->id. '/edit') }}" class="btn btn-sm btn-success">Edit</a>
                            <form action="{{ url('admin/products/'.$product->id. '/delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are You Sure,You Want To Delete This Data?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">No Produts Available</td>
                    </tr>
                    @endforelse
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>


@endsection