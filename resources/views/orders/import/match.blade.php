@extends('adminlte::page')

@section('title', 'Match Products')

@section('content_header')
    <h1>Találatok</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('orders.import.match') }}" method="POST">
    
            @csrf

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Customer Product Name</th>
                        <th>Customer Product Name (Long)</th>
                        <th>Quantity</th>
                        <th>Width</th>
                        <th>Height</th>
                        <th>Matched Product</th>
                    </tr>
                </thead>
                <tbody> 
                    @foreach ($products as $product)
                        @php
                            $isMatched = isset($matchedProducts[$product['customer_product_name']]);
                        @endphp
                        <tr style="background-color: {{ $isMatched ? 'inherit' : '#f8d7da' }};">
                            <td>{{ $product['customer_product_name'] }}</td>
                            <td>{{ $product['customer_product_name_long'] }}</td>
                            <td>{{ $product['quantity'] }}</td>
                            <td>{{ $product['width'] }}</td>
                            <td>{{ $product['height'] }}</td>
                            <td>
                                <select name="matches[{{ $product['customer_product_name'] }}]" class="form-control">
                                    <option value="" disabled {{ $isMatched ? '' : 'selected' }}>-- Select Product --</option>
                                    @foreach ($internalProducts as $internalProduct)
                                        <option value="{{ $internalProduct->id }}" 
                                            {{ $isMatched && $matchedProducts[$product['customer_product_name']]->product_id == $internalProduct->id ? 'selected' : '' }}>
                                            {{ $internalProduct->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

          
            <input type="hidden" name="customer_id" value="{{ $customer_id }}">
            <input type="hidden" name="products" value="{{ json_encode($products) }}">
            <!-- Other fields and the product matching logic -->
            <button type="submit" class="btn btn-success">Save Matching</button>
        </form>
    </div>
</div>
@endsection
