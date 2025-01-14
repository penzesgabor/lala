<h3>Products</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Height</th>
            <th>Width</th>
            <th>Divider</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->products as $product)
            <tr>
                <td>{{ $product->height }}</td>
                <td>{{ $product->width }}</td>
                <td>{{ $product->product->name ?? 'N/A' }}</td>
                <td>1</td>
                <td>
                    <form action="{{ route('order.products.destroy', [$order, $product]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
