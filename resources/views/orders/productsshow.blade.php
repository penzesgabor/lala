@extends('adminlte::page')

@section('title', 'Order Details')

@section('content_header')
    <h1>Order Details</h1>
@endsection

@section('content')

<h3>Products</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Height</th>
            <th>Width</th>
            <th>Product</th>
            <th>Customer Order Text</th>
            <th>Quantity</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groupedItems as $key => $items)
            @php
                $firstItem = $items->first();
            @endphp
            <tr>
                <td>{{ $firstItem->height }}</td>
                <td>{{ $firstItem->width }}</td>
                <td>{{ $firstItem->product->name ?? 'N/A' }}</td>
                <td>{{ $firstItem->customers_order_text }}</td>
                <td>{{ $items->count() }}</td>
                <td>
                <a href="{{ route('order.products.edit', [$order, $firstItem]) }}" class="btn btn-warning btn-sm">Edit</a>
                <a href="{{ route('order.products.edit', [$order, $firstItem]) }}" class="btn btn-warning btn-sm">Termékek</a>
                <form action="{{ route('order.products.destroy', [$order, $firstItem]) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
