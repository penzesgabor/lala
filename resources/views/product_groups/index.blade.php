@extends('layouts.app')

@section('title', 'Product Groups')

@section('content_header')
    <h1>Product Groups</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('product_groups.create') }}" class="btn btn-primary">Create Product Group</a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table id="product-groups-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Base Material Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productGroups as $productGroup)
                    <tr>
                        <td>{{ $productGroup->id }}</td>
                        <td>{{ $productGroup->name }}</td>
                        <td>{{ $productGroup->baseMaterialType->name }}</td>
                        <td>
                            <a href="{{ route('product_groups.edit', $productGroup) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('product_groups.destroy', $productGroup) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#product-groups-table').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            }
        });
    });
</script>
@endsection

