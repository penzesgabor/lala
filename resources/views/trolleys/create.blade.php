@extends('adminlte::page')

@section('title', 'Create Trolley')

@section('content_header')
    <h1>Create Trolley</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('trolleys.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="space">Space</label>
                <input type="number" name="space" id="space" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
</div>
@endsection
