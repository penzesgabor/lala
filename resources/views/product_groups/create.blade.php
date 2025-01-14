@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Product Group</h1>
    <form action="{{ route('product_groups.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="base_material_types_id">Base Material Type</label>
            <select name="base_material_types_id" id="base_material_types_id" class="form-control" required>
                @foreach ($baseMaterialTypes as $baseMaterialType)
                    <option value="{{ $baseMaterialType->id }}">{{ $baseMaterialType->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success mt-3">Save</button>
    </form>
</div>
@endsection
