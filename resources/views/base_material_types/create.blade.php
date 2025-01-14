@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Base Material Type</h1>
    <form action="{{ route('base_material_types.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success mt-3">Save</button>
    </form>
</div>
@endsection
