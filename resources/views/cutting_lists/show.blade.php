@extends('adminlte::page')

@section('title', 'Cutting List Details')

@section('content_header')
    <h1>Cutting List Details for #{{ $cuttingList->id }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Size (Width x Height)</th>
                    <th>Total Quantity</th>
                    <th>Customer Names</th>
                    <th>Order IDs</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedSelections as $group)
                    <tr>
                        <td>{{ $group['product']->name ?? 'Unknown' }}</td>
                        <td>{{ $group['width'] }} x {{ $group['height'] }} mm</td>
                        <td>{{ $group['totalQuantity'] }}</td> 
                        <td>{{ $group['customers']->join(', ') }}</td>
                        <td>{{ $group['orderIds']->join(', ') }}</td>
                    </tr>
                    <tr id="components-{{ $group['product']->id }}" class="components-row" >
                        <td colspan="5">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Component Name</th>
                                        <th>Total Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['components'] as $component)
                                        <tr>
                                            <td>{{ $component['name'] }}</td>
                                            <td>{{ $component['totalQuantity'] *  $group['totalQuantity'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    
                    
                @endforeach
            </tbody>
            
        </table>
    </div>
</div>
@endsection




@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.toggle-components');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = button.dataset.id;
                const row = document.getElementById(`components-${id}`);
                row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
            });
        });
    });
</script>
@endsection
