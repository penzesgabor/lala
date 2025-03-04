<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <!-- Select Customer -->
    <label for="customerSelect">Select Customer:</label>
    <select id="customerSelect" class="form-control">
        <option value="">-- Select a Customer --</option>
        @foreach ($customers as $customer)
            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
        @endforeach
    </select>

    <!-- Select Configurable Product -->
    <label for="productSelect">Select Configurable Product:</label>
    <select id="productSelect" class="form-control" disabled>
        <option value="">-- Select a Product --</option>
    </select>

    <!-- Components Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Configurable Product Name</th>
                <th>Simple Product Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody id="componentsTableBody">
            <!-- Content will be inserted here dynamically -->
        </tbody>
    </table>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.getElementById('customerSelect');
            const productSelect = document.getElementById('productSelect');
            const componentsTableBody = document.getElementById('componentsTableBody');

            // Load Configurable Products when a Customer is selected
            customerSelect.addEventListener('change', function () {
                fetch(`/admin/configurable-products`)
                    .then(response => response.json())
                    .then(data => {
                        productSelect.innerHTML = '<option value="">-- Select a Product --</option>';
                        data.forEach(product => {
                            const option = document.createElement('option');
                            option.value = product.id;
                            option.textContent = product.name;
                            productSelect.appendChild(option);
                        });
                        productSelect.disabled = false;
                    })
                    .catch(error => console.error('Error:', error));
            });

            // Load Product Components when a Product is selected
            productSelect.addEventListener('change', function() {
                const productId = this.value;
                fetch(`/admin/configurable-products/components?product_id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        componentsTableBody.innerHTML = ''; // Clear previous entries
                        data.forEach(component => {
                            const row = `<tr>
                                <td>${component.configurable_product_id}</td>
                                <td>${component.simple_product_id}</td>
                            </tr>`;
                            componentsTableBody.innerHTML += row;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
        
        
        document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('productSelect');
    const componentsTableBody = document.getElementById('componentsTableBody');

    productSelect.addEventListener('change', function() {
        const productId = this.value;
        fetch(`/admin/configurable-products/components?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                componentsTableBody.innerHTML = ''; // Clear previous entries
                data.forEach(component => {
                    const row = `<tr>
                        <td>${component.configurable_name}</td>
                        <td>${component.simple_name}</td>
                    </tr>`;
                    componentsTableBody.innerHTML += row;
                });
            })
            .catch(error => console.error('Error:', error));
    });
});

    </script>

</body>
</html>
