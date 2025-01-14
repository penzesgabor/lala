@extends('adminlte::page')

@section('title', 'Create Cutting List - Step 1')

@section('content_header')
    <h1>Create Cutting List - Step 1</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('cutting-lists.second-step') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="orders">Select Orders</label>
                <select name="order_ids[]" id="orders" class="form-control" multiple required>
                    @foreach ($orders as $order)
                        <option value="{{ $order->id }}">
                            Order #{{ $order->id }} - {{ $order->customer->name ?? 'Unknown Customer' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Next Step</button>
        </form>
    </div>
</div>
@endsection
