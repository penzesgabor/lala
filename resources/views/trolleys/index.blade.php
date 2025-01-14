@extends('adminlte::page')

@section('title', 'Trolleys')

@section('content_header')
    <h1>Trolleys</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <a href="{{ route('trolleys.create') }}" class="btn btn-primary mb-3">Create Trolley</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Space</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trolleys as $trolley)
                    <tr>
                        <td>{{ $trolley->id }}</td>
                        <td>{{ $trolley->name }}</td>
                        <td>{{ $trolley->space }}</td>
                        <td>
                            <a href="{{ route('trolleys.edit', $trolley->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('trolleys.destroy', $trolley->id) }}" method="POST" style="display:inline;">
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
@endsection
