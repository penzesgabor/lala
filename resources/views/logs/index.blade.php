@extends('layouts.app')

@section('content')
<div class="container">
    <h1>User Action Logs</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Action</th>
                <th>Details</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->user ? $log->user->name : 'Guest' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->details }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection
