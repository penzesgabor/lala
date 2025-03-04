@extends('layouts.app')
@section('title', 'Customers')
@section('content_header')
    <div class="card card-primary">
        <div class="card-header">
            <h1>Partnerek</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('customers.create') }}" class="btn btn-primary">Új partner felvitele</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table id="customers-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Megnevezés</th>
                        <th>Ir. szám</th>
                        <th>Város</th>
                        <th>Utca</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->zip }}</td>
                            <td>{{ $customer->city }}</td>
                            <td>{{ $customer->street }}</td>
                            <td>
                                <a href="{{ route('customers.edit', $customer) }}"
                                    class="btn-sm btn-secondary">Módosítás</a>
                                <a href="{{ route('customers.orders.create', $customer->id) }}"
                                    class="btn-sm btn-success">Új megrendelés</a>

                                <a href="{{ route('customers.orders.index', $customer->id) }}"
                                    class="btn-sm btn-primary">Megrendelések</a>

                                <button class=" btn btn-warning dropdown-toggle btn-sm" type="button" id="actionDropdown"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    További menü
                                </button>
                               
                                <div class="dropdown-menu" aria-labelledby="actionDropdown">
                                    <a href="{{ route('orders.import.form', ['customer_id' => $customer->id]) }}"
                                        class="btn btn-warning btn-sm ">
                                        Rendelés importálása
                                    </a>

                                    <a href="/customers/{{ $customer->id }}/prices" class="btn-sm btn-primary">Árak</a>
                                    <button type="button" class="btn  btn-danger btn-sm" data-toggle="modal"
                                        data-target="#updatePriceModal" data-customer-id="{{ $customer->id }}">
                                        Globális ármodosítás
                                    </button>
                                    <a href="{{ route('customers.dashboard', $customer->id) }}"
                                        class="btn btn-info btn-sm">Statisztika</a>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update Prices Modal -->
    <div class="modal fade" id="updatePriceModal" tabindex="-1" role="dialog" aria-labelledby="updatePriceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="updatePriceForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="updatePriceModalLabel">Ár módosítása</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="percentage">Add meg a százalékot</label>
                            <input type="number" name="percentage" id="percentage" class="form-control"
                                placeholder="Enter percentage" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Bezárás</button>
                        <button type="submit" class="btn btn-success">Módosítás</button>
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
            $('#customers-table').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    paginate: {
                        previous: "&laquo;",
                        next: "&raquo;"
                    }
                }
            });

            // Update the form action dynamically based on the customer ID
            $('#updatePriceModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var customerId = button.data('customer-id');
                var formAction = "{{ route('customers.prices.updateAll', ':id') }}".replace(':id',
                    customerId);
                $('#updatePriceForm').attr('action', formAction);
            });
        });
    </script>
@endsection
