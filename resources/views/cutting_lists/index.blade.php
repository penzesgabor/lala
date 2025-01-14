@extends('adminlte::page')

@section('title', 'Cutting Lists')

@section('content_header')
    <h1>Cutting Lists</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <a href="{{ route('cutting-lists.create') }}" class="btn btn-primary mb-3">Create Cutting List</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Daily Number</th>
                    <th>List Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cuttingLists as $cuttingList)
                    <tr>
                        <td>{{ $cuttingList->id }}</td>
                        <td>{{ $cuttingList->daily_number }}</td>
                        <td>{{ $cuttingList->list_date }}</td>
                        <td>
                            <a href="{{ route('cutting-lists.show', $cuttingList->id) }}" class="btn btn-info btn-sm">View</a>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
