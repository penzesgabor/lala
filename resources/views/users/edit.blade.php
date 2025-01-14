@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
    <h1>Edit User</h1>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ $userRole == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
</div>
</div>
@endsection
