@extends('adminlte::page')

@section('title', 'Customer Prices')

@section('content_header')
    <h1>Manage Prices for {{ $customer->name }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('customers.prices.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <table class="table table-bordered table-striped" id="customer-prices-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Base Price</th>
                        <th>Custom Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ number_format($product->base_price, 2) }}</td>
                            <td>
                                <input type="number" step="0.01" name="custom_prices[{{ $product->id }}]" 
                                       class="form-control" 
                                       value="{{ old('custom_prices.' . $product->id, $product->customerPrice($customer->id)) }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-success mt-3">Save Prices</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#customer-prices-table').DataTable({
            responsive: true,
            autoWidth: false,
            paging: false,
            searching: true,
            ordering: false,
        });
    });
</script>
@endsection
