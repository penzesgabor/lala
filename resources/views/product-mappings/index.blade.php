@extends('adminlte::page')

@section('title', 'Product Mappings')

@section('content_header')
    <h1>Product Mappings</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <a href="{{ route('product-mappings.create') }}" class="btn btn-primary mb-3">Add Mapping</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Customer Product Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mappings as $mapping)
                    <tr>
                        <td>{{ $mapping->id }}</td>
                        <td>{{ $mapping->customer->name }}</td>
                        <td>{{ $mapping->product->name }}</td>
                        <td>{{ $mapping->customer_product_name }}</td>
                        <td>
                            <a href="{{ route('product-mappings.edit', $mapping->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('product-mappings.destroy', $mapping->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $mappings->links() }}
    </div>
</div>
@endsection
