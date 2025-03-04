@extends('adminlte::page')

@section('title', 'Orders')

@section('content_header')
<div class="card card-success">
    <div class="card-header">
        <h1>Megrendelések</h1>
    </div>
</div>

@endsection

@section('content')
<div class="card ">
    <div class="card-header">
        <a href="{{ route('orders.create') }}" class="btn btn-success">Új megrendelés felvitele</a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table id="orders-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Megrendelés szám</th>
                    <th>Partner</th>
                    <th>Rendelés dátum</th>
                    <th>Szállítás dátum</th>
                    <th>Fizetve</th>
                    <th>Leszállítva</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer->name }}</td>
                        <td>{{ $order->ordering_date }}</td>
                        <td>{{ $order->delivery_date }}</td>
                        <td>{{ $order->isbilled ? 'Igen' : 'Nem' }}</td>
                        <td>{{ $order->isdelivered ? 'Igen' : 'Nem' }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-info btn-sm">Megnézem</a>
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning btn-sm">Szerkesztés</a>
                            <a href="/orders/{{  $order->id }}/products/create" class="btn btn-info btn-sm">Termékek</a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Nyomtatási lehetöségek
                                </button>
                                <div class="dropdown-menu">
                                    <a href="{{ route('orders.print', $order->id) }}" class="dropdown-item">Megrendelés nyomtatása</a>
                                    <a href="{{ route('orders.print.etikett', $order->id) }}" class="dropdown-item">Etikett nyomtatás</a>
                                </div>
                            </div>
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
        $('#orders-table').DataTable({
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
