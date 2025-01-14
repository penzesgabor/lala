@extends('layouts.app')

@section('content')
    <h1>Etikett for Order #{{ $order->id }}</h1>
    <p>Customer: {{ $order->customer->name }}</p>
    <!-- Add etikett details here -->
@endsection
