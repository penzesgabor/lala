@extends('adminlte::page')

@section('title', 'Import Customers')

@section('content_header')
    <h1>Import Customers</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customers.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="csv_file">Upload CSV File</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
</div>
@endsection
