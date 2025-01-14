@extends('adminlte::page')

@section('title', 'Create Cutting List - Step 2')

@section('content_header')
    <h1>Create Cutting List - Step 2</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('cutting-lists.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_ids" value="{{ json_encode(request('order_ids')) }}">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Width</th>
                        <th>Height</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Cutting Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderProducts as $orderProduct)
                        <tr>
                            <td>{{ $orderProduct->order_id }}</td>
                            <td>{{ $orderProduct->width }}</td>
                            <td>{{ $orderProduct->height }}</td>
                            <td>{{ $orderProduct->product->name ?? 'Unknown' }}</td>
                            <td>{{ $orderProduct->quantity }}</td>
                            <td>
                                <select name="cutting_dates[{{ $orderProduct->order_id }}_{{ $orderProduct->width }}_{{ $orderProduct->height }}]" class="form-control">
                                    <option value="">skip</option>
                                    @foreach ($nextSevenDays as $date)
                                        <option value="{{ $date }}">{{ $date }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">Save Cutting List</button>
        </form>
    </div>
</div>
@endsection
