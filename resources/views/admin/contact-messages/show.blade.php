@extends('layouts.admin')

@section('title', 'View Message')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>View Message</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2 fw-bold">Name</div>
                        <div class="col-md-10">{{ $contactMessage->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2 fw-bold">Email</div>
                        <div class="col-md-10"><a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2 fw-bold">Received</div>
                        <div class="col-md-10">{{ $contactMessage->created_at->format('d-m-Y h:i A') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2 fw-bold">Message</div>
                        <div class="col-md-10" style="white-space: pre-wrap; word-break: break-word;">{{ $contactMessage->message }}</div>
                    </div>
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-secondary">Back to Messages</a>
                    <form action="{{ url('admin/contact-messages/'.$contactMessage->id.'/delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are You Sure,You Want To Delete This Message?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
