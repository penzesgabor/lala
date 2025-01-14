<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiketts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .page {
            display: flex;
            flex-wrap: wrap;
            page-break-after: always;
            padding: 10px;
        }
        .etikett {
            width: 48%;
            margin: 1%;
            border: 1px solid #000;
            padding: 10px;
            box-sizing: border-box;
            text-align: center;
        }
        .barcode {
            margin: 10px 0;
        }
        .details {
            font-size: 14px;
            margin-top: 10px;
        }
        @media print {
            .page {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @foreach ($etiketts->chunk(12) as $page) <!-- 12 etikett labels per page -->
        <div class="page">
            @foreach ($page as $etikett)
                <div class="etikett">
                    <div class="barcode">
                        {!! DNS1D::getBarcodeHTML($etikett['barcode'], 'C128', 2, 50) !!}
                    </div>
                    <div class="details">
                        <p><strong>Customer:</strong> {{ $etikett['customer_name'] }}</p>
                        <p><strong>Order Text:</strong> {{ $etikett['customer_order_text'] }}</p>
                        <p><strong>Size:</strong> {{ $etikett['size'] }}</p>
                        <p><strong>Product:</strong> {{ $etikett['product_name'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>
