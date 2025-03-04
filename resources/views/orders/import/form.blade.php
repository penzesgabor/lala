@extends('adminlte::page')

@section('title', 'Import Orders for ' . $customer->name)

@section('content_header')
    <h1>Megrendelés importálása-> {{ $customer->name }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('orders.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">

            <div class="form-group">
                <label for="file">CSV File</label>
                <input type="file" name="csvfile" id="csvfile" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Feltöltés</button>
        </form>
    </div>
</div>
@endsection

