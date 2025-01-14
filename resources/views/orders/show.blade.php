@extends('adminlte::page')

@section('title', 'Order Details')

@section('content_header')
<div class="card">
    <div class="card-body">
<h1><strong> {{ $order->customer->name }}, {{ $order->id }} </strong>számú megrendelése</h1>
    </div>
</div>

@endsection

@section('content')
<div class="card card-success">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p><strong>Rendelés dátuma:</strong> {{ $order->ordering_date }}</p>
            </div>
            <div class="col-md-4">
                <p><strong>Szállítási dátum:</strong> {{ $order->delivery_date }}</p>
            </div>
            <div class="col-md-4">
                <p><strong>Gyártásbaadás dátuma:</strong> {{ $order->production_date }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Szálltási cím:</strong> {{ $order->deliveryAddress->street }}, {{ $order->deliveryAddress->city }} {{ $order->deliveryAddress->zip }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Megjegyzés:</strong> {{ $order->notes }}</p>
            </div>
        </div>

        <!-- Billed and Delivered Row -->
        <div class="row">
            <div class="col-md-6">
                <p><strong>Feladva számlázóba:</strong> {{ $order->isbilled ? 'Feladva' : 'Nem' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Kiszállítva:</strong> {{ $order->isdelivered ? 'Kiszállítva' : 'Nem' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card mt-3">
    <div class="card-header">
        <a href="/orders/{{ $order->id }}/products/create" class="btn btn-success btn-sm">Termék hozzáadása</a>
        <a href="/orders/{{ $order->id }}/print" class="btn btn-secondary btn-sm">Megrendelés nyomtatása</a>
        <a href="{{ route('orders.etiketts', $order->id) }}" class="btn btn-primary btn-sm" target="_blank">Etikett nyomtatása</a>
    </div>
</div>
<div class="card mt-3">
    <div class="card-body p-0">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Magasság</th>
                    <th>Szélesség</th>
                    <th>Termék neve</th>
                    <th>Partner kód</th>
                    <th>Mennyiség</th>
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
                            <a href="{{ route('order.products.edit', [$order, $firstItem]) }}" class="btn btn-warning btn-sm">Módosítás</a>
                            <form action="{{ route('order.products.destroy', [$order, $firstItem]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Törlés</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
