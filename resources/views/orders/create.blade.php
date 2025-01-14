@extends('adminlte::page')

@section('title', 'Create Order')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />    
<style>
.select2-container .select2-selection--single {
    height: 38px !important; 
}
</style>
@endsection

@section('content_header')
<div class="card card-success">
    <div class="card-header">
        <h1><strong>Új megrendelés </h1>
    </div>
</div>

@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            <!-- Customer Selection -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_id">Partner</label>
                        <select name="customer_id" id="customer_id" class="form-control select2" required>
                            <option value="" disabled selected>Válassz a listából</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>             
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="delivery_address_id">Szállítási cím</label>
                        <select name="delivery_address_id" id="delivery_address_id" class="form-control" required>
                            <option value="" disabled selected>Válassz szállítási címet</option>

                        </select>
                    </div>
                </div>
            </div>

            <!-- Dates Section -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ordering_date">Megrendelés dátuma</label>
                        <input type="date" name="ordering_date" id="ordering_date" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="delivery_date">Szállítás dátuma</label>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="production_date">Gyártásbaadás dátuma</label>
                        <input type="date" name="production_date" id="production_date" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="notes">Megjegyzés</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <!-- Flags Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="isbilled">Feladva számlázóba</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="isbilled" id="isbilled" class="custom-control-input">
                            <label class="custom-control-label" for="isbilled"></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="isdelivered">Kiszálitva</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="isdelivered" id="isdelivered" class="custom-control-input">
                            <label class="custom-control-label" for="isdelivered"></label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row mt-6">
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-block">Mentés</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2 for Customer Dropdown
        $('#customer_id').select2({
            placeholder: "Válassz partnert",
            allowClear: true,
        });

        const customerSelect = document.getElementById('customer_id');
        const deliveryAddressSelect = document.getElementById('delivery_address_id');

        $('#customer_id').on('change', function () { 
        const customerId = customerSelect.value;

            if (customerId) {
                deliveryAddressSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
                fetch(`/customers/${customerId}/delivery-addresses`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch delivery addresses');
                        }
                        return response.json();
                    })
                    .then(data => {
                        deliveryAddressSelect.innerHTML = '<option value="" disabled selected>Válassz szállítási címet</option>';
                        
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
                            option.textContent = "Nincs szállítási cím megadva";
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
