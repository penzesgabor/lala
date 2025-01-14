@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter user name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter user email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter password" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" class="form-control select2" required>
                    <option value="" disabled selected>Select a role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    // Initialize Select2 for the role dropdown
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select a role',
            allowClear: true
        });
    });
</script>
@endsection
