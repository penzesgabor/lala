@extends('adminlte::page')

@section('title', 'Import Orders for ' . $customer->name)

@section('content_header')
    <h1>Import Orders for {{ $customer->name }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('orders.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">

            <div class="form-group">
                <label for="file">Upload CSV File</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>
@endsection

