@extends('adminlte::page')

@section('title', 'Edit Trolley')

@section('content_header')
    <h1>Edit Trolley</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('trolleys.update', $trolley->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $trolley->name }}" required>
            </div>
            <div class="form-group">
                <label for="space">Space</label>
                <input type="number" name="space" id="space" class="form-control" value="{{ $trolley->space }}" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</div>
@endsection
