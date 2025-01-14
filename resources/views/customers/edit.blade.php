@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card card-primary">
            <div class="card-header">
                <h3>Partner módosítása</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Customer Details -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="name">Megnevezés</label>
                            <input type="text" name="name" id="name" value="{{ $customer->name }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="city">Város</label>
                            <input type="text" name="city" id="city" value="{{ $customer->city }}"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="street">Utca</label>
                            <input type="text" name="street" id="street" value="{{ $customer->street }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="zip">Ir. szám</label>
                            <input type="text" name="zip" id="zip" value="{{ $customer->zip }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="contact_name">Kapcsolattartó neve</label>
                            <input type="text" name="contact_name" id="contact_name"
                                value="{{ $customer->contact_name }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="phone">Telfonszáma</label>
                            <input type="text" name="phone" id="phone" value="{{ $customer->phone }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="bank_account_nr">Bankszámlaszáma</label>
                            <input type="text" name="bank_account_nr" id="bank_account_nr"
                                value="{{ $customer->bank_account_nr }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="tax_number">Adószám</label>
                            <input type="text" name="tax_number" id="tax_number" value="{{ $customer->tax_number }}"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="booking_id">Könyvelési azonosító</label>
                            <input type="text" name="booking_id" id="booking_id" value="{{ $customer->booking_id }}"
                                class="form-control" required>
                        </div>
                    </div>


                    <div class="card-header">
                        <h4 class="mt-4">Szállítási cím</h4>
                    </div>

                    <table class="table table-bordered" id="delivery-addresses-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Város</th>
                                <th>utca</th>
                                <th>Ir. szám</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->deliveryAddresses as $i => $address)
                                <tr class="delivery-address">
                                    <td>
                                        <input type="text" name="delivery_addresses[{{ $i }}][city]"
                                            class="form-control" value="{{ $address->city }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="delivery_addresses[{{ $i }}][street]"
                                            class="form-control" value="{{ $address->street }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="delivery_addresses[{{ $i }}][zip]"
                                            class="form-control" value="{{ $address->zip }}" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-address">Törlés</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" id="add-address" class="btn btn-secondary btn-sm">Új szállítási cím
                        hozzáadása</button>

                    <div>
                        <button type="submit" class="btn btn-success mt-4">Módosítás</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        let addressIndex = {{ $customer->deliveryAddresses->count() }};

        document.getElementById('add-address').addEventListener('click', function() {
            const table = document.getElementById('delivery-addresses-table').querySelector('tbody');
            const newRow = table.rows[0].cloneNode(true);

            newRow.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/\d+/, addressIndex));
                input.value = '';
            });

            table.appendChild(newRow);
            addressIndex++;
        });

        document.getElementById('delivery-addresses-table').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-address')) {
                e.target.closest('tr').remove();
            }
        });
    </script>
@endsection
