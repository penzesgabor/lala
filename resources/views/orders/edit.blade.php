@extends('adminlte::page')

@section('title', 'Edit Order')

@section('content_header')
    <h1>Edit Order</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="customer_id">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control" required>
                    <option value="" disabled>Select a Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="ordering_date">Ordering Date</label>
                <input type="date" name="ordering_date" id="ordering_date" class="form-control" value="{{ $order->ordering_date }}" required>
            </div>

            <div class="form-group">
                <label for="delivery_date">Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ $order->delivery_date }}">
            </div>

            <div class="form-group">
                <label for="delivery_address_id">Delivery Address</label>
                <select name="delivery_address_id" id="delivery_address_id" class="form-control" required>
                    <option value="" disabled>Select a Delivery Address</option>
                    @foreach ($addresses as $address)
                        <option value="{{ $address->id }}" {{ $order->delivery_address_id == $address->id ? 'selected' : '' }}>
                            {{ $address->street }}, {{ $address->city }} {{ $address->zip }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="production_date">Production Date</label>
                <input type="date" name="production_date" id="production_date" class="form-control" value="{{ $order->production_date }}">
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3">{{ $order->notes }}</textarea>
            </div>

            <div class="form-check">
                <input type="checkbox" name="isbilled" id="isbilled" class="form-check-input" {{ $order->isbilled ? 'checked' : '' }}>
                <label for="isbilled" class="form-check-label">Is Billed</label>
            </div>

            <div class="form-check">
                <input type="checkbox" name="isdelivered" id="isdelivered" class="form-check-input" {{ $order->isdelivered ? 'checked' : '' }}>
                <label for="isdelivered" class="form-check-label">Is Delivered</label>
            </div>

            <button type="submit" class="btn btn-success mt-3">Update Order</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customerSelect = document.getElementById('customer_id');
        const deliveryAddressSelect = document.getElementById('delivery_address_id');

        customerSelect.addEventListener('change', function () {
            const customerId = customerSelect.value;

            if (customerId) {
                // Clear the existing options
                deliveryAddressSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';

                // Fetch delivery addresses
                fetch(`/customers/${customerId}/delivery-addresses`)
                    .then(response => response.json())
                    .then(data => {
                        deliveryAddressSelect.innerHTML = '<option value="" disabled selected>Select a Delivery Address</option>';

                        if (data.length > 0) {
                            data.forEach(address => {
                                const option = document.createElement('option');
                                option.value = address.id;
                                option.textContent = `${address.street}, ${address.city} ${address.zip}`;
                                deliveryAddressSelect.appendChild(option);
                            });
                        } else {
                            const option = document.createElement('option');
                            option.value = "";
                            option.textContent = "No delivery addresses available";
                            deliveryAddressSelect.appendChild(option);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching delivery addresses:', error);
                        deliveryAddressSelect.innerHTML = '<option value="" disabled selected>Error loading addresses</option>';
                    });
            }
        });
    });
</script>
@endsection
