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

            <form action="{{ route('orders.import.save-third-step', $order->id) }}" method="POST">
                @csrf

                @if ($orderProducts->isNotEmpty())
                    <h3>Nincs termékár rögzítve</h3>

                    <input type="hidden" name="delivery_date" value="1999-10-10">
                    <input type="hidden" name="delivery_address_id" value="{{ old('delivery_address_id', $defaultDeliveryAddressId) }}">
                    <input type="hidden" name="production_date" value="1999-10-10">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Magasság</th>
                                <th>Sélesség</th>
                                <th>m2</th>
                                <th>Ügyfél termék név</th>
                                <th>Kézi ár</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderProducts as $orderProduct)
                                <tr>
                                    <td>{{ $orderProduct->product->name }}</td>
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
                @else
                    <p>Árak rendben.</p>
                @endif

                <h3>Szállítási adatok</h3>

                <div class="form-group">
                    <label for="delivery_date">Szállítás dátuma</label>
                    <input type="date" name="delivery_date" id="delivery_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="delivery_address_id">Szállítási cím</label>
                    <select name="delivery_address_id" id="delivery_address_id" class="form-control" required>
                        <option value="" disabled selected>Szállítási cím</option>
                        @foreach ($deliveryAddresses as $address)
                            <option value="{{ $address->id }}">{{ $address->city }}, {{ $address->street }}, {{ $address->zip }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="production_date">Gyártásbaadás dátuma</label>
                    <input type="date" name="production_date" id="production_date" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">Mentés</button>
            </form> 
        </div>
    </div>
@endsection
