@extends('adminlte::page')

@section('title', 'Orders for ' . $customer->name)

@section('content_header')
<div class="card card-success">
    <div class="card-header">
        <h1>{{ $customer->name }} megrendelései</h1>
    </div>
</div>

@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-header">
                <a href="{{ route('customers.orders.create', $customer->id) }}"
                    class="btn btn-success">Új megrendelés</a> <br>
                    </div>
        </div>
        
        <table id="customer-orders-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Megrendelés szám</th>
                    <th>Megrendelés dátuma</th>
                    <th>Szallítási határidö</th>
                    <th>Gyártásbaadás dátuma</th>
                    <th>Szállítási cím</th>
                    <th>Számlázva</th>
                    <th>Kiszállítva</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->ordering_date }}</td>
                        <td>{{ $order->delivery_date }}</td>
                        <td>{{ $order->production_date }}</td>
                        <td>{{ $order->deliveryAddress->street ?? 'N/A' }}</td>
                        <td>{{ $order->isbilled ? 'Feladva' : 'Nem' }}</td>
                        <td>{{ $order->isdelivered ? 'Igen' : 'Nem' }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">Mutasd</a>
                            <a href="/orders/{{ $order->id }}/products/create" class="btn btn-info btn-sm">Termékek</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#customer-orders-table').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                },
                search: "Keresés:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            },
            columnDefs: [
                { orderable: false, targets: 7 } // Disable ordering for the Actions column
            ]
        });
    });
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

@endsection
