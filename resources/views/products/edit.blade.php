@extends('adminlte::page')

@section('title', 'Edit Product')

@section('content_header')
    <div class="card card-primary">
        <div class="card-header">
            <h1>Termék módosítása</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- General Product Details -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Megnevezés</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Típus</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="simple" {{ $product->type === 'simple' ? 'selected' : '' }}>Alap</option>
                            <option value="configurable" {{ $product->type === 'configurable' ? 'selected' : '' }}>Konfigurálható</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dropdowns -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="base_material_type_id">Alapanyag típus</label>
                        <select name="base_material_type_id" id="base_material_type_id" class="form-control">
                            <option value="">Válassz</option>
                            @foreach ($baseMaterialTypes as $baseMaterialType)
                                <option value="{{ $baseMaterialType->id }}" {{ $product->base_material_type_id == $baseMaterialType->id ? 'selected' : '' }}>
                                    {{ $baseMaterialType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="product_group_id">Termék csoport</label>
                        <select name="product_group_id" id="product_group_id" class="form-control">
                            <option value="">Válassz</option>
                            @foreach ($productGroups as $productGroup)
                                <option value="{{ $productGroup->id }}" {{ $product->product_group_id == $productGroup->id ? 'selected' : '' }}>
                                    {{ $productGroup->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vat_id">ÀFA</label>
                        <select name="vat_id" id="vat_id" class="form-control" required>
                            <option value="" disabled>Válassz</option>
                            @foreach ($vats as $vat)
                                <option value="{{ $vat->id }}" {{ $product->vat_id == $vat->id ? 'selected' : '' }}>
                                    {{ $vat->name }} ({{ $vat->value }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Additional Fields -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="english_name">Angol megnevezés</label>
                        <input type="text" name="english_name" id="english_name" class="form-control" value="{{ old('english_name', $product->english_name) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="weight_per_squaremeter">Súly / m2</label>
                        <input type="number" step="0.01" name="weight_per_squaremeter" id="weight_per_squaremeter" class="form-control" value="{{ old('weight_per_squaremeter', $product->weight_per_squaremeter) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="liseccode">Lisec kód</label>
                        <input type="text" name="liseccode" id="liseccode" class="form-control" value="{{ old('liseccode', $product->liseccode) }}">
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="form-group">
                <label for="base_price">Alapár</label>
                <input type="number" step="0.01" name="base_price" id="base_price" class="form-control" value="{{ old('base_price', $product->base_price ?? '') }}">
            </div>

            <!-- Configurable Section -->
            <div id="configurable-section" style="{{ $product->type === 'configurable' ? '' : 'display:none;' }}">
                <div class="row">
                    <!-- Component List -->
                    <div class="col-md-6">
                        <h4>Konfigurálható termék</h4>
                        <div id="component-list">
                            @foreach ($product->components as $i => $component)
                            <div class="form-group component-item">
                                <label for="components[{{ $i }}][id]">Komponens</label>
                                <select name="components[{{ $i }}][id]" class="form-control product-select">
                                    <option value="">Select</option>
                                    @foreach ($simpleProducts as $simpleProduct)
                                        <option value="{{ $simpleProduct->id }}" {{ $component->id == $simpleProduct->id ? 'selected' : '' }}
                                            data-thickness="{{ preg_match('/^\d+/', $simpleProduct->name, $match) ? $match[0] : 0 }}">
                                            {{ $simpleProduct->name }}
                                        </option>
                                    @endforeach
                                </select>
                            
                                <!-- Hidden Quantity Field -->
                                <input type="hidden" name="components[{{ $i }}][quantity]" class="thickness-input" value="{{ $component->pivot->quantity }}">
                                <button type="button" class="btn btn-danger mt-2 remove-component">Eltávolítás</button>
                            </div>
                            
                            @endforeach
                        </div>
                        <button type="button" id="add-component" class="btn btn-secondary mt-3">Hozzáadás</button>
                    </div>

                    <!-- Vertical Cross-Section Visualization -->
                    <div class="col-md-6">
                        <h4>Rajz</h4>
                        <div id="cross-section" class="p-3 border" style="background: #f8f9fa; border-radius: 8px; min-width: 100px; min-height: 300px;">
                            <div id="glass-layers" class="text-center text-muted">Rajz</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success mt-4">Módosítás</button>
        </form>
    </div>

    <!-- Price History -->
    <div class="card-footer">
        <h4>Ár változás</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Dátum</th>
                    <th>Ár</th>
                    <th>Módosította</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product->priceHistories as $history)
                    <tr>
                        <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $history->price }}</td>
                        <td>{{ $history->changed_by }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript for Dynamic Components -->
<script>
          const simpleProducts = @json($simpleProducts2);

    function updateThickness(selectElement) {
        const thickness = selectElement.options[selectElement.selectedIndex].getAttribute('data-thickness') || 0;
        const thicknessInput = selectElement.closest('.form-group').querySelector('.thickness-input');
        thicknessInput.value = thickness;
        updateCrossSection();
    }

    document.querySelectorAll('.product-select').forEach(select => {
        select.addEventListener('change', function () {
            updateThickness(this);
        });
    });

    let componentIndex = {{ $product->components->count() }};
    
    document.getElementById('add-component').addEventListener('click', function () {
    const componentList = document.getElementById('component-list');

    // Create a new component element
    const newComponent = document.createElement('div');
    newComponent.classList.add('form-group', 'component-item');

    // Component Dropdown
    const componentSelect = document.createElement('select');
    componentSelect.name = `components[${componentIndex}][id]`;
    componentSelect.classList.add('form-control', 'product-select');
    componentSelect.innerHTML = `
        <option value="">Select</option>
        ${simpleProducts.map(product => `
            <option value="${product.id}" data-thickness="${product.thickness || 0}">${product.name}</option>
        `).join('')}
    `;
    componentSelect.addEventListener('change', function () {
        updateThickness(this);
    });

    // Hidden Quantity Field
    const quantityInput = document.createElement('input');
    quantityInput.type = 'hidden';
    quantityInput.name = `components[${componentIndex}][quantity]`;
    quantityInput.classList.add('thickness-input');
    quantityInput.value = '';

    // Remove Button
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.classList.add('btn', 'btn-danger', 'mt-2', 'remove-component');
    removeButton.textContent = 'Remove';
    removeButton.addEventListener('click', function () {
        newComponent.remove();
        updateCrossSection();
    });

    // Append all elements to the new component
    newComponent.appendChild(componentSelect);
    newComponent.appendChild(quantityInput);
    newComponent.appendChild(removeButton);

    // Add the new component to the list
    componentList.appendChild(newComponent);
    componentIndex++;

    // Update the cross-section
    updateCrossSection();
});


    document.getElementById('component-list').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-component')) {
            e.target.closest('.component-item').remove();
            updateCrossSection();
        }
    });

    function updateCrossSection() {
        const layersContainer = document.getElementById('glass-layers');
        layersContainer.innerHTML = ''; // Clear existing layers

        const components = document.querySelectorAll('#component-list .form-group');

        components.forEach((component, index) => {
            const componentName = component.querySelector('select option:checked').textContent;
            const thickness = component.querySelector('.thickness-input').value;

            if (componentName && thickness) {
                const layer = document.createElement('div');
                layer.style.height = thickness + 'px'; // Thickness determines vertical size
                layer.style.width = '100%'; // Full width
                layer.style.height = '10%'; // Full width
                layer.style.background = index % 2 === 0 ? '#007bff' : '#6c757d'; // Alternate colors
                layer.style.marginBottom = '2px';
                layer.style.color = 'white';
                layer.style.textAlign = 'center';
                layer.style.lineHeight = thickness + 'px' * 5; // Center text vertically
                layer.style.borderRadius = '4px';
                layer.textContent = `${componentName} (${thickness}mm)`;

                layersContainer.appendChild(layer);
            }
        });

        if (components.length === 0) {
            layersContainer.innerHTML = '<div class="text-center text-muted">Komponens hiányzik</div>';
        }
    }
</script>

@endsection
