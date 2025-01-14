@extends('adminlte::page')

@section('title', 'Create Product')

@section('content_header')
<div class="card card-danger">
    <div class="card-header">
    <h1>Termék felvitele</h1>
    </div>
</div>
@endsection

@section('content')
<div class="card card-danger">
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Megnevezés</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Típus</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="simple">Alapanyag</option>
                            <option value="configurable">Konfigurálható termék</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dropdowns -->
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="base_material_type_id">Alapanyag típusa</label>
                        <select name="base_material_type_id" id="base_material_type_id" class="form-control">
                            <option value="">Select</option>
                            @foreach ($baseMaterialTypes as $baseMaterialType)
                                <option value="{{ $baseMaterialType->id }}">{{ $baseMaterialType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="vat_id">ÁFA</label>
                        <select name="vat_id" id="vat_id" class="form-control" required>
                            <option value="" disabled selected>Válassz</option>
                            @foreach ($vats as $vat)
                                <option value="{{ $vat->id }}">{{ $vat->name }} ({{ $vat->value }}%)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="product_group_id">Termék csoport</label>
                        <select name="product_group_id" id="product_group_id" class="form-control">
                            <option value="">Select</option>
                            @foreach ($productGroups as $productGroup)
                                <option value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Additional Fields -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="english_name">Angol megnevezés</label>
                        <input type="text" name="english_name" id="english_name" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="weight_per_squaremeter">Súly / m2</label>
                        <input type="number" step="0.01" name="weight_per_squaremeter" id="weight_per_squaremeter" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="liseccode">Lisec kód</label>
                        <input type="text" name="liseccode" id="liseccode" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="base_price">Alapár</label>
                        <input type="number" step="0.01" name="base_price" id="base_price" class="form-control" value="{{ old('base_price', $product->base_price ?? '') }}">
                    </div>
                </div>
            </div>

  <!-- Configurable Section -->
  <div id="configurable-section" style="display: none;">
    <div class="row">
        <!-- Component List -->
        <div class="col-md-6">
            <h3>Összetevök</h3>
            <div id="component-list">
                <div class="form-group component-item">
                    <label for="components[0][id]">Összetevö</label>
                    <select name="components[0][id]" class="form-control product-select">
                        <option value="">Válassz</option>
                        @foreach ($simpleProducts as $simpleProduct)
                            <option value="{{ $simpleProduct->id }}" data-thickness="{{ preg_match('/^\d+/', $simpleProduct->name, $match) ? $match[0] : 0 }}">
                                {{ $simpleProduct->name }}
                            </option>
                        @endforeach
                    </select>
                
                    <!-- Hidden Quantity Field -->
                    <input type="hidden" name="components[0][quantity]" class="thickness-input" value="">
                    <button type="button" class="btn btn-danger mt-2 remove-component">Eltávolítás</button>
                </div>
            </div>
            <button type="button" id="add-component" class="btn btn-secondary mt-3">Komponens hozzáadása</button>
        </div>

        <!-- Cross-Section Visualization -->
        <div class="col-md-6">
            <h4>Üveg keresztmetszet</h4>
            <div id="cross-section" class="p-3 border" style="background: #f8f9fa; border-radius: 8px; min-width: 100px; min-height: 300px;">
                <div id="glass-layers" class="text-center text-muted">Komponens hozzáadása</div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Button -->
<button type="submit" class="btn btn-success mt-4">Mentés</button>
</form>
</div>
</div>
@endsection
@section('js')
<script>
    document.getElementById('type').addEventListener('change', function () {
        const configurableSection = document.getElementById('configurable-section');
        configurableSection.style.display = this.value === 'configurable' ? 'block' : 'none';
    });

    let componentIndex = 1;
   

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

    
    document.getElementById('add-component').addEventListener('click', function () {
        const componentList = document.getElementById('component-list');
        const newComponent = componentList.children[0].cloneNode(true);

        newComponent.querySelectorAll('select, input').forEach((input) => {
            const name = input.getAttribute('name');
            input.setAttribute('name', name.replace(/\d+/, componentIndex));
            if (input.classList.contains('product-select')) {
                input.value = '';
                input.addEventListener('change', function () {
                    updateThickness(this);
                });
            } else {
                input.value = '';
            }
        });

        componentList.appendChild(newComponent);
        componentIndex++;
    });


    document.getElementById('component-list').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-component')) {
            e.target.closest('.form-group').remove();
            updateCrossSection();
        }
    });


function updateCrossSection() {
    const layersContainer = document.getElementById('glass-layers');
    layersContainer.innerHTML = ''; 

    const components = document.querySelectorAll('#component-list .form-group');

    components.forEach((component, index) => {
        const componentName = component.querySelector('select option:checked').textContent;
        const thickness = component.querySelector('.thickness-input').value;

        if (componentName && thickness) {
            const layer = document.createElement('div');
            layer.style.height = thickness + 'px'; function updateCrossSection() {
    const layersContainer = document.getElementById('glass-layers');
    layersContainer.innerHTML = ''; // Clear existing layers

    const components = document.querySelectorAll('#component-list .form-group');

    components.forEach((component, index) => {
        const componentName = component.querySelector('select option:checked').textContent;
        const thickness = component.querySelector('.thickness-input').value;

        if (componentName && thickness) {
            const layer = document.createElement('div');
            layer.style.height = thickness + 'px'; 
            layer.style.width = '100%'; // Full width
            layer.style.background = index % 2 === 0 ? '#007bff' : '#6c757d'; // Alternate colors
            layer.style.marginBottom = '2px';
            layer.style.color = 'white';
            layer.style.textAlign = 'center';
            layer.style.lineHeight = thickness + 'px'; // Center text vertically
            layer.style.borderRadius = '4px';
            layer.textContent = `${componentName} (${thickness}mm)`;

            layersContainer.appendChild(layer);
        }
    });

    if (components.length === 0) {
        layersContainer.innerHTML = '<div class="text-center text-muted">Add components to visualize the cross-section</div>';
    }
}
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
        layersContainer.innerHTML = '<div class="text-center text-muted">Add components to visualize the cross-section</div>';
    }
}

</script>

@endsection
