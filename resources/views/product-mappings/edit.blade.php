@extends('adminlte::page')

@section('title', 'Edit Product Mapping')

@section('content_header')
    <h1>Edit Product Mapping</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('product-mappings.update', $productMapping->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="customer_id">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control" required>
                    <option value="" disabled>Select a Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $customer->id == $productMapping->customer_id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="product_id">Product</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    <option value="" disabled>Select a Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ $product->id == $productMapping->product_id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="customer_product_name">Customer Product Name</label>
                <input type="text" name="customer_product_name" id="customer_product_name" 
                       class="form-control" value="{{ $productMapping->customer_product_name }}" required>
            </div>

            <button type="submit" class="btn btn-success">Update Mapping</button>
            <a href="{{ route('product-mappings.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
