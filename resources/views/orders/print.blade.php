<html>
@section('content')
<div class="card">
    <div class="card-body">
        <!-- Header Section -->
        <div class="row">
            <div class="col-md-6">
                <h5>Szállító</h5>
                <p>Salgotherm Kft.<br>
                1138 Budapest<br>
                Viza u. 7/B 6.em./261<br>
                Tel/Fax: <br>
                Bankszámlaszám: 10700220-69887259-51100005<br>
                Adószám: 14741339-2-41
                </p>
            </div>
            <div class="col-md-6">
                <h5>Megrendelő</h5>
                <p>{{ $order->customer->name }}<br>
                {{ $order->customer->street }}<br>
                {{ $order->customer->zip }} {{ $order->customer->city }}<br>
                {{ $order->customer->tax_number }}
                </p>
            </div>
        </div>

        <!-- Order Information Section -->
        <div class="row">
            <div class="col-md-6">
                <p><strong>Megrendelés száma:</strong> {{ $order->id }}</p>
                <p><strong>Megrendelés dátuma:</strong> {{ $order->ordering_date }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Szállítási határidő:</strong> {{ $order->delivery_date }}</p>
            </div>
        </div>

        <!-- Order Details Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sorszám</th>
                    <th>Megnevezés</th>
                    <th>Méret</th>
                    <th>Mennyiség</th>
                    <th>NM</th>
                    <th>FT/NM</th>
                    <th>Nettó ár</th>
                    <th>Bruttó ár</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->width }} x {{ $product->height }}</td>
                        <td>{{ $product->quantity }} db</td>
                        <td>{{ $product->squaremeter }}</td>
                        <td>{{ number_format($product->price_per_sqm, 2) }}</td>
                        <td>{{ number_format($product->net_price, 2) }}</td>
                        <td>{{ number_format($product->gross_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="row mt-3">
            <div class="col-md-6">
                <p><strong>Üzemvezető:</strong> </p>
            </div>
            <div class="col-md-6 text-right">
                <p><strong>Vállalkozó:</strong></p>
            </div>
        </div>

        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td><strong>Összesen Darab:</strong> {{ $order->products->sum('quantity') }}</td>
                    <td><strong>NM:</strong> {{ $order->products->sum('squaremeter') }}</td>
                    <td><strong>FM:</strong> <!-- Add FM calculation here if needed --></td>
                    <td><strong>Nettó összeg:</strong> {{ number_format($order->products->sum('net_price'), 2) }} Ft</td>
                    <td><strong>Bruttó összeg:</strong> {{ number_format($order->products->sum('gross_price'), 2) }} Ft</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer Section -->
        <p class="mt-3">
            A mai napon az alábbi megrendeléseket fogadtuk el. Ezek a termékek minőségileg megfelelnek az EMI a-174/97 előírásainak, melyekre 5 év garanciát vállalunk.
        </p>
    </div>
</div>
@endsection
