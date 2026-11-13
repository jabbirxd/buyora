@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3>Users List
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm text-white float-end">
                            Add User</a>
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->roll_as == 1)
                                            <span class="badge bg-success text-white">Admin</span>
                                        @else
                                            <span class="badge bg-primary text-white">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 8px;">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-primary btn-sm text-white d-inline-flex align-items-center justify-content-center" style="height: 32px; padding: 0 12px;">
                                                <i class="mdi mdi-eye me-1"></i> View
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center" style="height: 32px; padding: 0 12px;">
                                                <i class="mdi mdi-pencil me-1"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center justify-content-center" style="height: 32px; padding: 0 12px;">
                                                    <i class="mdi mdi-delete me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
