@extends('adminlte::page')

@section('title', 'Edit Product')

@section('content_header')
    <h1>Edit Product for Order #{{ $order->id }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('order.products.update', [$order, $product]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="product_id">Product</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    @foreach ($products as $prod)
                        <option value="{{ $prod->id }}" {{ $prod->id == $product->product_id ? 'selected' : '' }}>
                            {{ $prod->name }} ({{ $prod->productGroup->name ?? 'No Group' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="height">Height</label>
                <input type="number" step="0.01" name="height" id="height" class="form-control" value="{{ $product->height }}" required>
            </div>
            <div class="form-group">
                <label for="width">Width</label>
                <input type="number" step="0.01" name="width" id="width" class="form-control" value="{{ $product->width }}" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $product->quantity }}" required>
            </div>
            <div class="form-group">
                <label for="customers_order_text">Customer's Order Text</label>
                <textarea name="customers_order_text" id="customers_order_text" class="form-control">{{ $product->customers_order_text }}</textarea>
            </div>
            <div class="form-group">
                <label for="barcode">Barcode</label>
                <input type="text" name="barcode" id="barcode" class="form-control" value="{{ old('barcode', $orderProduct->barcode ?? '') }}">
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control">{{ $product->notes }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Update Product</button>
        </form>
    </div>
</div>
@endsection
