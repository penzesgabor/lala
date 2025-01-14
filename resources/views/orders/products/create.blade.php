@extends('adminlte::page')

@section('title', 'Add Products to Order')

@section('content_header')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />    
<style>
.select2-container .select2-selection--single {
    height: 38px !important; 
}
</style>

@endsection 
<div class="card card-primary">
<div class="card-header">
    <div class="card-title">
    <h3>Megrendelés száma: {{ $order->id }} , Megrendelö: {{ $order->customer->name }}</h3>
    </div>
</div>
</div>
@endsection

@section('content')
    <div class="row">
        <!-- Left Side: Add Product Form -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form id="productForm" action="{{ route('order.products.store', $order) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select name="product_id" id="product_id" class="form-control select2" required>
                                <option value="" disabled selected>Select a Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-base-price="{{ $product->base_price }}"
                                        data-customer-price="{{ $customerPrices[$product->id] ?? '' }}">
                                        {{ $product->name }} ({{ $product->productGroup->name ?? 'No Group' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="width">Szélesség (mm)</label>
                                    <input type="number" step="0.01" name="width" id="width" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="height">Magasság (mm)</label>
                                    <input type="number" step="0.01" name="height" id="height" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantity">Darabszám</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="divider_id">Osztó</label>
                                    <select name="divider_id" id="divider_id" class="form-control">
                                        <option value="" disabled selected>Osztó választás</option>
                                        @foreach ($dividers as $divider)
                                            <option value="{{ $divider->id }}">{{ $divider->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="divider_length">Divider Length</label>
                                    <input type="number" step="0.01" name="divider_length" id="divider_length"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dividercross">Divider Cross (Pieces)</label>
                                    <input type="number" name="dividercross" id="dividercross" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dividerend">Divider End (Pieces)</label>
                                    <input type="number" name="dividerend" id="dividerend" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gasfilling">Gas Filling</label>
                                    <select name="gasfilling" id="gasfilling" class="form-control">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="extracharge">Extra Charge (%)</label>
                                    <input type="number" step="0.01" name="extracharge" id="extracharge"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="barcode">Barcode</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control"
                                        value="{{ old('barcode', $orderProduct->barcode ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customers_order_text">Customer's Order Text</label>
                                    <textarea name="customers_order_text" id="customers_order_text" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="squaremeter_display">Square Meter</label>
                                    <input type="text" id="squaremeter_display" class="form-control" readonly>
                                    <input type="hidden" name="squaremeter" id="squaremeter">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="flowmeter_display">Flow Meter</label>
                                    <input type="text" id="flowmeter_display" class="form-control" readonly>
                                    <input type="hidden" name="flowmeter" id="flowmeter">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="calculated_price_display">Price</label>
                                    <input type="text" id="calculated_price_display" class="form-control" readonly>
                                    <input type="hidden" name="calculated_price" id="calculated_price">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="agreed_price_display">Agreed price</label>
                                    <input type="text" name="agreed_price" id="agreed_price" class="form-control">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Add Product</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Added Products Table -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rögzített tételek</h3>
                </div>
                <div class="card-body">
                    <table id="addedProductsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Termék</th>
                                <th>Méret</th>
                                <th>Mennyiség</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedProducts as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $product['product_name'] }}</td>
                                    <td>{{ $product['width'] }} x {{ $product['height'] }}</td>
                                    <td>{{ $product['total_quantity'] }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" 
                                              onclick="editProduct({{ $order->id }}, '{{ $product['product_name'] }}', {{ $product['width'] }}, {{ $product['height'] }})">
                                              Szerkesztés
                                            </button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="deleteProduct({{ $order->id }}, '{{ $product['product_name'] }}', {{ $product['width'] }}, {{ $product['height'] }})">
                                            Törlés
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 on the select element
        $('#product_id').select2({
            allowClear: true, // Allows clearing the selection
            width: '100%', // Ensures the dropdown spans the full width of its container
        });
    });
</script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heightInput = document.getElementById('height');
            const widthInput = document.getElementById('width');
            const quantityInput = document.getElementById('quantity');
            const productSelect = document.getElementById('product_id');
            const squareMeterInput = document.getElementById('squaremeter');
            const flowMeterInput = document.getElementById('flowmeter');
            const priceInput = document.getElementById('price');

            function calculateValues() {
                const height = parseFloat(heightInput.value) || 0;
                const width = parseFloat(widthInput.value) || 0;
                const squareMeters = (height * width) / 1e6; // Convert mm² to m²
                const flowMeters = (2 * (height + width)) / 1000; // Perimeter in meters


                squaremeter_display.value = squareMeters.toFixed(2);
                flowmeter_display.value = flowMeters.toFixed(2);
                squaremeter.value = squareMeters.toFixed(2);
                flowmeter.value = flowMeters.toFixed(2);

                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const basePrice = parseFloat(selectedOption?.dataset.basePrice) || 0;
                const customerPrice = parseFloat(selectedOption?.dataset.customerPrice) || basePrice;
                const pricePerSquareMeter = customerPrice || basePrice;
                const calculatedPrice = pricePerSquareMeter * squareMeters;

                //                const calculatedPrice = customerPrice * squareMeters;

                calculated_price_display.value = calculatedPrice.toFixed(2);

                calculated_price.value = calculatedPrice.toFixed(2);
            }


            [heightInput, widthInput, productSelect].forEach(input => {
                input.addEventListener('input', calculateValues);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productForm = document.getElementById('productForm');

            productForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(productForm);
                const url = productForm.action;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            //refreshTable(data.products);
                            //productForm.reset();
                            location.reload();
                        } else {
                            alert('Error adding product.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            function refreshTable(products) {
    const tableBody = document.querySelector('#addedProductsTable tbody');
    tableBody.innerHTML = '';

    products.forEach((product, index) => {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${product.product_name}</td>
                <td>${product.width} x ${product.height}</td>
                <td>${product.total_quantity}</td>
                <td>
                    <button class="btn btn-warning btn-sm" 
                            onclick="editProduct(${product.order_id}, ${product.product_id}, ${product.width}, ${product.height})">
                        Edit
                    </button>
                    <button class="btn btn-danger btn-sm" 
                            onclick="deleteProduct(${product.order_id}, '${product.product_name}', ${product.width}, ${product.height})">
                        Delete
                    </button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}


            window.deleteProduct = function(orderId, productName, width, height) {
                if (!confirm('Are you sure you want to delete these products?')) {
                    return;
                }

                fetch(`/orders/${orderId}/products/delete-group`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_name: productName,
                            width: width,
                            height: height
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Refresh the page
                            location.reload();
                        } else {
                            alert('Error deleting products.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            };


            window.editProduct = function(orderId, productName, width, height) {
    fetch(`/orders/${orderId}/products/get-details`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_name: productName, width: width, height: height }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateForm(data.product);
        } else {
            alert('Error fetching product details.');
        }
    })
    .catch(error => console.error('Error:', error));
};

function populateForm(product) {
    document.getElementById('product_id').value = product.product_id;
    document.getElementById('height').value = product.height;
    document.getElementById('width').value = product.width;
    document.getElementById('quantity').value = product.quantity;
    document.getElementById('squaremeter_display').value = product.squaremeter;
    document.getElementById('flowmeter_display').value = product.flowmeter;
    document.getElementById('calculated_price_display').value = product.calculated_price;
    // Populate other fields as necessary
}

        });
    </script>
@endsection
