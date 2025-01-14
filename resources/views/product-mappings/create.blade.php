@extends('adminlte::page')

@section('title', 'Create Product Mapping')

@section('content_header')
    <h1>Create Product Mapping</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('product-mappings.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="customer_id">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control" required>
                    <option value="" disabled selected>Select a Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="product_id">Product</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    <option value="" disabled selected>Select a Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="customer_product_name">Customer Product Name</label>
                <input type="text" name="customer_product_name" id="customer_product_name" 
                       class="form-control" placeholder="Enter the customer product name" required>
            </div>

            <button type="submit" class="btn btn-success">Create Mapping</button>
            <a href="{{ route('product-mappings.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
