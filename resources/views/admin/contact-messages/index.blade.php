@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3>Contact Messages</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Received</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($messages as $contactMessage)
                                <tr>
                                    <td>{{ $contactMessage->id }}</td>
                                    <td>{{ $contactMessage->name }}</td>
                                    <td><a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></td>
                                    <td style="white-space: pre-wrap; word-break: break-word;">{{ \Illuminate\Support\Str::limit($contactMessage->message, 80) }}</td>
                                    <td>{{ $contactMessage->created_at->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.contact-messages.show', $contactMessage->id) }}" class="btn btn-sm btn-primary">View</a>
                                        <form action="{{ url('admin/contact-messages/'.$contactMessage->id.'/delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are You Sure,You Want To Delete This Message?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No messages yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
