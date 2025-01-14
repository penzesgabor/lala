@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Base Material Types</h1>
    <a href="{{ route('base_material_types.create') }}" class="btn btn-primary mb-3">Create Base Material Type</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($baseMaterialTypes as $baseMaterialType)
                <tr>
                    <td>{{ $baseMaterialType->id }}</td>
                    <td>{{ $baseMaterialType->name }}</td>
                    <td>
                        <a href="{{ route('base_material_types.edit', $baseMaterialType) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('base_material_types.destroy', $baseMaterialType) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $baseMaterialTypes->links() }}
</div>
@endsection
