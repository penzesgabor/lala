@extends('adminlte::page')

@section('title', 'Finalize Order')

@section('content_header')
    <h1>Finalize Order</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($orderProducts->isNotEmpty())
                <h3>Products Missing Prices</h3>
                <form action="{{ route('orders.import.save-third-step', $order->id) }}" method="POST">
                    @csrf

                    <input type="hidden" name="delivery_date" value="1999-10-10">
                    <input type="hidden" name="delivery_address_id"
                        value="{{ old('delivery_address_id', $defaultDeliveryAddressId) }}">
                    <input type="hidden" name="production_date"
                        value="1999-10-10">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Height</th>
                                <th>Width</th>
                                <th>Square Meter</th>
                                <th>Customer Product Name</th>
                                <th>Manual Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderProducts as $orderProduct)
                                <tr>
                                    <td>{{ $orderProduct->product_id }}</td>
                                    <td>{{ $orderProduct->height }}</td>
                                    <td>{{ $orderProduct->width }}</td>
                                    <td>{{ $orderProduct->squaremeter }}</td>
                                    <td>{{ $orderProduct->customer_product_name ?? 'N/A' }}</td>
                                    <td>
                                        <input type="number" step="0.01" name="prices[{{ $orderProduct->id }}]"
                                            class="form-control" placeholder="Enter price">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">Save Prices</button>
               @else
                <p>All products have prices.</p>
            @endif

            <h3>Finalize Delivery Details</h3>
            
                <div class="form-group">
                    <label for="delivery_date">Delivery Date</label>
                    <input type="date" name="delivery_date" id="delivery_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="delivery_address_id">Delivery Address</label>
                    <select name="delivery_address_id" id="delivery_address_id" class="form-control" required>
                        <option value="" disabled selected>Select a Delivery Address</option>
                        @foreach ($deliveryAddresses as $address)
                            <option value="{{ $address->id }}">{{ $address->city }}, {{ $address->street }},
                                {{ $address->zip }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="production_date">Production Date</label>
                    <input type="date" name="production_date" id="production_date" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">Finalize Order</button>
            </form>
        </div>
    </div>
@endsection
