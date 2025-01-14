@extends('layouts.app')

@section('title', 'Products')

@section('content_header')
    <h1>Products</h1>
@endsection

@section('content')
<div class="card card-danger">
    <div class="card-header">
        <a href="{{ route('products.create') }}" class="btn btn-primary">Új termék felvitele </a>
        <!-- Trigger Button for Price Update Modal -->
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#priceUpdateModal">
            Áremelés
        </button>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table id="products-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>Termék neve</th>
                    <th>Típus</th>
                    <th>Termék csoport</th>
                    <th>Alapanyag</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ ucfirst($product->type) }}</td>
                        <td>{{ $product->productGroup->name ?? 'N/A' }}</td>
                        <td>{{ $product->baseMaterialType->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Módosítás</a>
<!--                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Törlés</button>
                            </form>
                        -->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Price Update -->
<div class="modal fade" id="priceUpdateModal" tabindex="-1" role="dialog" aria-labelledby="priceUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('products.prices.updateAll') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="priceUpdateModalLabel">Áremelés</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="percentage">Add meg a százalékot</label>
                        <input type="number" name="percentage" id="percentage" class="form-control" placeholder="Add meg az emelés mértéket %-ban (pl, 10 vagy -10)" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégdem</button>
                    <button type="submit" class="btn btn-warning">Mehet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#products-table').DataTable({
            responsive: true,
            autoWidth: false
        });
    });
</script>
@endsection
