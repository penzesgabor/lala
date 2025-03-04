@extends('adminlte::page')

@section('title', 'Add Products to Order')

@section('content_header')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .table-scroll {
        max-height: 540px; /* Set a fixed height */
        overflow-y: auto;  /* Enable vertical scrolling */
    }
    </style>

@endsection
<div class="card card-primary">
    <div class="card-header">
        <div class="card-title">
            <h2>Megrendelés száma: {{ $order->id }} | Megrendelő: {{ $order->customer->name }}    <a href="http://localhost:8000/orders/{{ $order->id }} /print" class="btn btn-success right">Megrendelés nyomtatása</a></h2>
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
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="order_product_id" id="order_product_id">
                    <input type="hidden" name="order_id" id="order_id" value=" {{ $order->id }}">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Termék</label>
                                <select name="product_id" id="product_id" class="form-control select2" required>
                                    <option value="" disabled selected>Válassz</option>
                                    @foreach ($products as $product)
                                        @if ($product->product_type === 'simple')
                                            <option value="{{ $product->product_id }}"
                                                data-glass-price="{{ $product->product_price ?? 0 }}">
                                                {{ $product->product_name }}
                                            </option>
                                        @else
                                            <option value="{{ $product->product_id }}"
                                                data-glass-price="{{ $product->components[0]->total_price ?? 0 }}"
                                                data-spacer-price="{{ $product->components[1]->total_price ?? 0 }}">
                                                {{ $product->product_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div id="priceAlert" class="alert alert-success" role="alert" style="display:none;">
                                <p></p><span id="squareMeterPrice">0</span>
                            </div>

                        </div>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="divider_id">Osztó</label>
                                <select name="divider_id" id="divider_id" class="form-control">
                                    <option value="" selected>Osztó választás</option>

                                    @foreach ($dividers as $divider)
                                        <option value="{{ $divider->id }}"
                                            data-divider-price="{{ $divider->divider_price }}">{{ $divider->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="divider_length"> fm</label>
                                <input type="text" name="divider_length" id="divider_length" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="dividercross">Keresztelem</label>
                                <input type="text" name="dividercross" id="dividercross" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="dividerend">Végelem</label>
                                <input type="text" name="dividerend" id="dividerend" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="gasfilling">Gáztöltés</label>
                                <select name="gasfilling" id="gasfilling" class="form-control">
                                    <option value="1">Igen</option>
                                    <option value="0">Nem</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="extracharge">Felár (%)</label>
                                <input type="text" name="extracharge" id="extracharge" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div id="priceAlertRow" class="row" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info" role="alert">
                                <strong>Árkalkuláció:</strong>
                                <ul>
                                    <li><strong>Osztó ár:</strong> <span id="dividerPriceText">0.00</span>
                                        <strong>Keresztelem ár:</strong> <span id="dividerCrossPriceText">0.00</span>
                                        <strong>Végelem ár:</strong> <span id="dividerEndPriceText">0.00</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="barcode">Vonalkód</label>
                                <input type="text" name="barcode" id="barcode" class="form-control"
                                    value="{{ old('barcode', $orderProduct->barcode ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customers_order_text">Ügyfél rendelés szám</label>
                                <input type="text" name="customers_order_text" id="customers_order_text"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="notes">Megjegyzés</label>
                                <input type="text" name="notes" id="notes" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div id="extraCalculationsRow" class="row" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info" role="alert">
                                <strong>Részletes számítások:</strong>
                                <ul>
                                    <li><strong>m² / db:</strong> <span id="squareMeterPerPiece">0.00</span> m²</li>
                                    <li><strong>Teljes m²:</strong> <span id="totalSquareMeter">0.00</span> m²</li>
                                    <li><strong>FM / db:</strong> <span id="flowMeterPerPiece">0.00</span> FM</li>
                                    <li><strong>Teljes FM:</strong> <span id="totalFlowMeter">0.00</span> FM</li>
                                    <li><strong>Kalkulált ár / db:</strong> <span
                                            id="calculatedPricePerPiece">0.00</span> Ft</li>
                                    <li><strong>Kalkulált ár / m²:</strong> <span
                                            id="calculatedPricePerSquareMeter">0.00</span> Ft</li>
                                    <li><strong>Teljes kalkulált ár:</strong> <span
                                            id="totalCalculatedPrice">0.00</span> Ft</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">



                        <div class="col-md-3">
                            <div class="form-group">
                                <div id="squareAlert" class="alert alert-success" role="alert"
                                    style="display:none;">
                                    <p></p><span id="squareMeterDisplay">0.00</span>
                                </div>
                                <input type="hidden" name="squaremeter" id="squaremeter">
                                <input type="hidden" name="randomid" id="randomid">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="hidden" name="flowmeter" id="flowmeter">
                                <div id="flowAlert" class="alert alert-success" role="alert"
                                    style="display:none;">
                                    <p></p><span id="flowMeterDisplay">0.00</span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="calculated_price_display">Kalkulált ár</label>
                                <input type="text" id="calculated_price" name="calculated_price"  class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="agreed_price_display">Módosított ár</label>
                                <input type="text" name="agreed_price" id="agreed_price" class="form-control">
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitButton" class="btn btn-success">Mentés</button>
                    <button type="button" id="updateButton" class="btn btn-warning d-none">Módosítás</button>
                    <button type="button" id="saveAsNewButton" class="btn btn-primary d-none">Mentés újként</button>

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
            <div class="card-body table-scroll">
                    <table id="addedProductsTable" class="table table-bordered table-sm text-sm">
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
                                <td>{{ $product['product_name'] }} </td>
                                <td>{{ $product['width'] }} x {{ $product['height'] }}</td>
                                <td>{{ $product['total_quantity'] }}</td>
                                <td>

                                    <button class="btn btn-warning btn-xs p-1"
                                        onclick="editProduct(
                                                    {{ $order->id }},
                                                    '{{ $product['product_id'] }}',
                                                    {{ $product['height'] }},
                                                    {{ $product['width'] }},
                                                    {{ $product['total_quantity'] }},
                                                    {{ $product['extracharge'] ?? 0 }},
                                                    {{ $product['squaremeter'] ?? 0 }},
                                                    {{ $product['flowmeter'] ?? 0 }},
                                                    {{ $product['calculated_price'] ?? 0 }},
                                                    {{ $product['agreed_price'] ?? 0 }},
                                                    '{{ $product['divider_id'] ?? '' }}',
                                                    '{{ $product['divider_length'] ?? '' }}',
                                                    '{{ $product['dividercross'] ?? '' }}',
                                                    '{{ $product['dividerend'] ?? '' }}',
                                                    '{{ $product['gasfilling'] ?? '' }}',
                                                    '{{ $product['barcode'] ?? '' }}',
                                                    '{{ $product['customers_order_text'] ?? '' }}',
                                                    '{{ $product['notes'] ?? '' }}',
                                                    {{ $product['randomid'] }},
                                                )">
                                                                                    Szerkesztés

                                    </button>
                                    <button class="btn btn-danger btn-xs p-1"
                                        onclick="deleteProduct({{ $order->id }},{{ $product['randomid']}})">
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
    document.addEventListener('DOMContentLoaded', function() {
        let editMode = false;
        let editProductId = null;

        const productForm = document.getElementById('productForm');
        const formMethod = document.getElementById('formMethod');
        const orderProductIdInput = document.getElementById('order_product_id');
        const submitButton = document.getElementById('submitButton');
        const updateButton = document.getElementById('updateButton');
        const saveAsNewButton = document.getElementById('saveAsNewButton');
        const orderId = document.getElementById('order_id').value;

        const productSelect = document.getElementById('product_id');
        const heightInput = document.getElementById('height');
        const widthInput = document.getElementById('width');
        // const squareMeterDisplay = document.getElementById('squaremeter_display');
        // const flowMeterDisplay = document.getElementById('flowmeter_display');
        //const calculatedPriceDisplay = document.getElementById('calculated_price_display');
        const calculatedPriceInput = document.getElementById('calculated_price');
        const agreedPriceInput = document.getElementById('agreed_price');
        const dividerLengthInput = document.getElementById('divider_length');
        const dividerSelect = document.getElementById('divider_id');
        const dividerCrossInput = document.getElementById('dividercross'); // Corrected
        const dividerEndInput = document.getElementById('dividerend'); // Corrected

        const extrachargeInput = document.getElementById('extracharge');

        const squareMeterPriceSpan = document.getElementById('squareMeterPrice');
        const squareMeterSpan = document.getElementById('squareMeterDisplay');
        const flowMeterSpan = document.getElementById('flowMeterDisplay');
        const priceAlert = document.getElementById('priceAlert');
        const squareAlert = document.getElementById('squareAlert');
        const flowAlert = document.getElementById('flowAlert');

        const dividerPriceText = document.getElementById('dividerPriceText');
        const dividerCrossPriceText = document.getElementById('dividerCrossPriceText');
        const dividerEndPriceText = document.getElementById('dividerEndPriceText');

        const extraCalculationsRow = document.getElementById('extraCalculationsRow');

        const quantityInput = document.getElementById('quantity');
        const squareMeterPerPiece = document.getElementById('squareMeterPerPiece');
        const totalSquareMeter = document.getElementById('totalSquareMeter');
        const flowMeterPerPiece = document.getElementById('flowMeterPerPiece');
        const totalFlowMeter = document.getElementById('totalFlowMeter');
        const calculatedPricePerPiece = document.getElementById('calculatedPricePerPiece');
        const calculatedPricePerSquareMeter = document.getElementById('calculatedPricePerSquareMeter');
        const totalCalculatedPrice = document.getElementById('totalCalculatedPrice');


        function updateExtraCalculations() {
            const quantity = parseFloat(quantityInput.value) || 1;
            const squareMeters = parseFloat(document.getElementById('squaremeter').value) || 0;
            const flowMeters = parseFloat(document.getElementById('flowmeter').value) || 0;
            const calculatedPrice = parseFloat(document.getElementById('calculated_price').value) || 0;

            // Calculations
            const squareMeterPiece = squareMeters;
            const totalSquareMeters = squareMeters * quantity;
            const flowMeterPiece = flowMeters;
            const totalFlowMeters = flowMeters * quantity;
            const calculatedPricePiece = calculatedPrice;
            const calculatedPricePerSqM = calculatedPrice / squareMeters;
            const totalPrice = calculatedPrice * quantity;

            // Update UI
            squareMeterPerPiece.textContent = squareMeterPiece.toFixed(2);
            totalSquareMeter.textContent = totalSquareMeters.toFixed(2);
            flowMeterPerPiece.textContent = flowMeterPiece.toFixed(2);
            totalFlowMeter.textContent = totalFlowMeters.toFixed(2);
            calculatedPricePerPiece.textContent = calculatedPricePiece.toFixed(0);
            calculatedPricePerSquareMeter.textContent = calculatedPricePerSqM.toFixed(0);
            totalCalculatedPrice.textContent = totalPrice.toFixed(0);

            // Show the row if values are greater than 0
            //if (squareMeters > 0 || flowMeters > 0 || calculatedPrice > 0) {
            //    extraCalculationsRow.style.display = "block";
            //} else {
            //    extraCalculationsRow.style.display = "none";
            //}
        }

        [quantityInput, document.getElementById('squaremeter'), document.getElementById('flowmeter'), document
            .getElementById('calculated_price')
        ].forEach(input => {
            if (input) {
                input.addEventListener('input', updateExtraCalculations);
            }
        });

        updateExtraCalculations();


        function updatePriceAlert() {
            const dividerSelectedOption = dividerSelect.options[dividerSelect.selectedIndex] || {};

            const dividerPrice = parseFloat(dividerSelectedOption.dataset.dividerPrice) || 0;
            const dividerFM = parseFloat(dividerLengthInput.value) || 0;
            const dividerCross = parseFloat(dividerCrossInput.value) || 0;
            const dividerEnd = parseFloat(dividerEndInput.value) || 0;

            // Calculate item prices
            const dividerTotal = dividerPrice * dividerFM;
            const dividerCrossTotal = dividerCross * 5; // Example price per piece
            const dividerEndTotal = dividerEnd * 3; // Example price per piece

            // Update alert text
            dividerPriceText.textContent = dividerTotal.toFixed(0) + " Ft.-";
            dividerCrossPriceText.textContent = dividerCrossTotal.toFixed(0) + " Ft.-";
            dividerEndPriceText.textContent = dividerEndTotal.toFixed(0) + " Ft.-";
            //  gasFillingPriceText.textContent = gasFillingTotal.toFixed(0) + " Ft.-";
            //  extraChargePriceText.textContent = extraChargeTotal.toFixed(0) + " Ft.-";

            // Show row if any field has a value
            //if (dividerFM > 0 || dividerCross > 0 || dividerEnd > 0) {
            //    priceAlertRow.style.display = "block";
            // } else {
            //    priceAlertRow.style.display = "none";
            // }
        }

        // Attach event listeners to inputs
        [dividerSelect, dividerLengthInput, dividerCrossInput, dividerEndInput].forEach(input => {
            input.addEventListener('input', updatePriceAlert);
        });

        function calculateValues() {
            const selectedOption = productSelect.options[productSelect.selectedIndex] || {};
            const glassPrice = parseFloat(selectedOption.dataset.glassPrice) || 0;
            const spacerPrice = parseFloat(selectedOption.dataset.spacerPrice) || 0;

            const dividerSelectedOption = dividerSelect.options[dividerSelect.selectedIndex] || {};
            const dividerPrice = parseFloat(dividerSelectedOption.dataset.dividerPrice) || 0;
            const dividerFM = parseFloat(dividerLengthInput.value) || 0;

            const height = parseFloat(heightInput.value) || 0;
            const width = parseFloat(widthInput.value) || 0;
            const extracharge = parseFloat(extrachargeInput.value) || 0;

            const squareMeters = (height * width) / 1e6;
            squareMeterDisplay.value = squareMeters.toFixed(2);
            document.getElementById('squaremeter').value = squareMeters.toFixed(2);

            const flowMeters = (2 * (height + width)) / 1000;
            flowMeterDisplay.value = flowMeters.toFixed(2);
            document.getElementById('flowmeter').value = flowMeters.toFixed(2);

            const glassCost = glassPrice * squareMeters;
            const spacerCost = spacerPrice * flowMeters;
            const dividerCost = dividerPrice * dividerFM;

            const totalPriceNoPerc = glassCost + spacerCost + dividerCost;
            const totalPrice = totalPriceNoPerc * (1 + (extracharge / 100));
           // calculatedPriceDisplay.value = totalPrice.toFixed(0);
            calculatedPriceInput.value = totalPrice.toFixed(0);
            agreedPriceInput.value = totalPrice.toFixed(0);

            squareMeterPriceSpan.textContent = glassPrice.toFixed(0) + " Ft/m² (üveg)";
            priceAlert.style.display = "block";
            squareMeterSpan.textContent = squareMeters.toFixed(2) + " m²";
            squareAlert.style.display = "block";
            flowMeterSpan.textContent = flowMeters.toFixed(2) + " fm";
            flowAlert.style.display = "block";
        }

        function editProduct(orderProductId, productId, width, height, quantity, extracharge, squaremeter,
            flowmeter, calculated_price, agreed_price, divider_id, divider_length, dividercross, dividerend,
            gasfilling, barcode, customers_order_text, notes, randomid) {

            editMode = true;
            editProductId = orderProductId;

            populateForm({
                product_id: productId,
                height: height,
                width: width,
                quantity: quantity,
                extracharge: extracharge,
                squaremeter: squaremeter,
                flowmeter: flowmeter,
                calculated_price: calculated_price,
                agreed_price: agreed_price,
                divider_id: divider_id,
                divider_length: divider_length,
                dividercross: dividercross,
                dividerend: dividerend,
                gasfilling: gasfilling,
                barcode: barcode,
                customers_order_text: customers_order_text,
                notes: notes,
                randomid: randomid
            });

            formMethod.value = "PUT";
            orderProductIdInput.value = orderProductId;

            submitButton.classList.add('d-none');
            updateButton.classList.remove('d-none');
            saveAsNewButton.classList.remove('d-none');
        }

        function populateForm(product) {
            for (const key in product) {
                if (document.getElementById(key)) {
                    document.getElementById(key).value = product[key];
                }
            }
        }

        
        window.deleteProduct = function(orderId,randomId) {
                        if (!confirm('Are you sure you want to delete these products?')) return;

            fetch(`/orders/${orderId}/products/delete-group`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        randomid: randomId
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `/orders/${orderId}/products/create`; // Redirec
                    } 
                    else alert('Error deleting products.');
                })
                .catch(error => console.error('Error:', error));
        }

        updateButton.addEventListener('click', function() {

            const formMethod = document.getElementById('formMethod');
            const productForm = document.getElementById('productForm');
            const randomId = document.getElementById('randomid').value;

            alert(randomId);
            formMethod.value = "PUT";
            productForm.action = `/orders/${orderId}/products/update-group/${randomId}`; 
            // Ensure the form submits to the correct URL
            //document.getElementById('productForm').action =
            //    `/orders/${orderProductId}/products/${productId}`;
            productForm.submit();
        });

     

        saveAsNewButton.addEventListener('click', function() {
            formMethod.value = "POST";
            orderProductIdInput.value = "";
            submitButton.classList.remove('d-none');
            updateButton.classList.add('d-none');
            saveAsNewButton.classList.add('d-none');
            productForm.submit();
        });

        productSelect.addEventListener('change', calculateValues);
        [heightInput, widthInput, dividerSelect, dividerLengthInput, extrachargeInput].forEach(input => {
            input.addEventListener('input', calculateValues);
        });

        calculateValues();
        window.editProduct = editProduct;
    });
</script>

@endsection
