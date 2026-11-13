@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12"></div>
        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        <div class="card">
            <div class="card-header">
                <h3>Colors List
                    <a href="{{ url('admin/colors/create') }}" class="btn btn-primary btn-sm text-white float-end">
                        Add Colors</a>
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Color Name</th>
                            <th>Color Code</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($colors as $item )
                        
                        
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->code}}</td>
                            <td>
                                {{$item->status ? 'Hidden':'Visible'}}
                                <form action="{{ url('admin/colors/' . $item->id . '/toggle-status') }}" method="POST" class="d-inline">
                                    @csrf
                                    @if ($item->status)
                                        <button type="submit" class="btn btn-outline-primary btn-sm ms-2"><i class="mdi mdi-eye"></i> Show</button>
                                    @else
                                        <button type="submit" class="btn btn-light btn-sm ms-2"><i class="mdi mdi-eye-off"></i> Hide</button>
                                    @endif
                                </form>
                            </td>
                            <td>
                                <a href="{{url('admin/colors/' .$item->id.'/edit')}}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ url('admin/colors/' . $item->id . '/delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are You Sure. You Want To Delete This Data?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
@endsection
