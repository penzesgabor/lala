@extends('adminlte::page')

@section('title', 'Create New Order for ' . $customer->name)

@section('content_header')
    <h1>Új megrendelés felvitele:  {{ $customer->name }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('customers.orders.store', $customer->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="ordering_date">Megrendelés dátuma</label>
                <input type="date" name="ordering_date" id="ordering_date" class="form-control" value="{{ old('ordering_date') }}" required>
            </div>
            <div class="form-group">
                <label for="delivery_date">Szállítás dátuma</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date') }}">
            </div>
            <div class="form-group">
                <label for="production_date">Gyártásbaadás dátuma</label>
                <input type="date" name="production_date" id="production_date" class="form-control" value="{{ old('production_date') }}">
            </div>
            <div class="form-group">
                <label for="delivery_address_id">Szállítási cím</label>
                <select name="delivery_address_id" id="delivery_address_id" class="form-control" required>
                    @foreach ($customer->deliveryAddresses as $address)
                        <option value="{{ $address->id }}">{{ $address->city }}, {{ $address->street }}, {{ $address->zip }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="notes">Megjegyzés</label>
                <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Rendelés rögzítése</button>
        </form>
    </div>
</div>
@endsection
