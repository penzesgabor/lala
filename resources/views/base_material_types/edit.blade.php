@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Base Material Type</h1>
    <form action="{{ route('base_material_types.update', $baseMaterialType) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $baseMaterialType->name }}" required>
        </div>
        <button type="submit" class="btn btn-success mt-3">Update</button>
    </form>
</div>
@endsection
