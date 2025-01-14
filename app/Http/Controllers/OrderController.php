<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryAddress;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use Milon\Barcode\DNS1D;
#use setasign\Fpdf\Fpdf;
use setasign\Fpdf\Fpdf;
use PDF;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'ordering_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'production_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'isbilled' => 'boolean',
            'isdelivered' => 'boolean',
            'imported' => 'boolean',
        ]);

        Order::create($validated);

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $groupedItems = $order->products->groupBy(function ($item) {
            return "{$item->height}_{$item->width}_{$item->product_id}_{$item->customers_order_text}";
        });

        $order->load('customer', 'deliveryAddress', 'products');
        return view('orders.show', compact('order', 'groupedItems'));
    }

    public function edit(Order $order)
    {
        $customers = Customer::all();
        $addresses = DeliveryAddress::where('customer_id', $order->customer_id)->get();
        return view('orders.edit', compact('order', 'customers', 'addresses'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'ordering_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'production_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'isbilled' => 'boolean',
            'isdelivered' => 'boolean',
            'imported' => 'boolean',
        ]);

        $order->update($validated);

        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    public function productsshow(Order $order)
    {
        return redirect()->route('orders.productsshow');
    }

    public function createFromCustomer(Customer $customer)
    {
        return view('orders.directcreate', compact('customer'));
    }

    public function storeFromCustomer(Request $request, Customer $customer)
    {
        $request->validate([
            'ordering_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'production_date' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
        ]);

        $order = $customer->orders()->create([
            'ordering_date' => $request->ordering_date,
            'delivery_date' => $request->delivery_date,
            'production_date' => $request->production_date,
            'notes' => $request->notes,
            'delivery_address_id' => $request->delivery_address_id,
            'isbilled' => false,
            'isdelivered' => false,
            'imported' => false,
        ]);

        return redirect()->route('customers.orders.index', $customer->id)->with('success', 'Order created successfully.');
    }


    public function printOrder($id)
    {
        $order = Order::with('customer')->findOrFail($id);

        $groupedProducts = $order->products->groupBy(function ($product) {
            return $product->width . '_' . $product->height . '_' . $product->product_id;
        })->map(function ($group) {
            $product = $group->first()->product; 
    
            return [
                'width' => $group->first()->width,
                'height' => $group->first()->height,
                'product_name' => $product->name,
                'vat' => $product->vat_id,
                'total_quantity' => $group->count(),
                'total_net_price' => $group->sum('agreed_price'),
                'total_gross_price' => ($product->vat_id/100)*$group->sum('agreed_price')+$group->sum('agreed_price'),
                'total_sqauremeter' => $group->sum('squaremeter'),
                'total_flowmeter' => $group->sum('flowmeter'),
                'price_per_squaremeter' => ($group->sum('agreed_price')/$group->count())/($group->sum('squaremeter')/$group->count()),
            ];
        });

        $pdf = new class($order,$groupedProducts) extends Fpdi {
            private $order;
            private $groupedProducts;

            public function __construct($order, $groupedProducts)
            {
                parent::__construct();
                $this->order = $order;
                $this->groupedProducts = $groupedProducts;
            }

            public function Header()
            {
                #$this->Image('logo.jpg',10,25,20,20);
                $this->SetFont( 'Arial','B',15);
                $this->SetY(10);
                $this->SetX(80);
                $this->Cell(30,10,iconv( 'utf-8','ISO- 8859-2','Rendelésfelvételi lap'),0,0,'C');
                $this->SetY(10);
                $this->SetX(150);
                $this->Ln();
                $this->SetFont( 'Arial','B',8);
                $this->SetY(20);
                $this->SetX(10);
                $this->Cell(25,6,iconv( 'utf-8','ISO- 8859-2','Szállító'),'LT');
                $this->SetY(20);
                $this->SetX(35);
                $this->Cell( 65,6,'','TR');
                $this->SetY(20);
                $this->SetX(100);
                $this->Cell(25,6,iconv( 'utf-8','ISO- 8859-2','Megrendelö ') ,'LT',0,'L');
                $this->SetY(20);
                $this->SetX(125);
                $this->Cell( 65,6, '' ,'TR',0,'R');
                $this->Ln();
                $this->SetY(26);
                $this->SetX(10);
                $this->Cell(90,24, '','LR');
                $this->SetY(26);
                $this->SetX(129);
                $this->Cell(61,25,'','R');
                $this->SetY(26);
                $this->SetX(100);
                $this->Ln();

                $this->SetFont('Arial', '', 8);
                $this->SetY(20);
                $this->SetX(40);
                $this->Cell(25, 6, 'Salgotherm Kft.', 0, 0, 'L');
                $this->SetY(25);
                $this->SetX(40);
                $this->Cell(25, 6, '1138 Budapest', 0, 0, 'L');
                $this->SetY(30);
                $this->SetX(40);
                $this->Cell(25, 6, 'Viza u. 7/B 6.em./261 .', 0, 0, 'L');
                $this->SetY(35);
                $this->SetX(40);
                $this->Cell(25, 6, 'Tel/Fax: ', 0, 0, 'L');
                $this->SetY(40);
                $this->SetX(40);
                $this->Cell(25, 6, iconv('utf-8', 'ISO-8859-2', 'Bankszlaszám:10700220-69887259-51100005'), 0, 0, 'L');
                $this->SetY(45);
                $this->SetX(40);
                $this->Cell(25, 6, iconv('utf-8', 'ISO-8859-2', 'Adószám: 14741339-2-41'), 0, 0, 'L');
                $this->SetFont('Arial', '', 8);
                $this->SetY(20);
                $this->SetX(120);
                $this->Cell(25, 6, iconv('utf-8', 'ISO-8859-2', $this->order->customer->name), 0, 0, 'L');
                $this->SetY(25);
                $this->SetX(120);
                $this->Cell(25, 6, $this->order->customer->zip . "   " . iconv('utf-8', 'ISO-8859-2', $this->order->customer->city), 0, 0, 'L');
                $this->SetY(30);
                $this->SetX(120);
                $this->Cell(25, 6, iconv('utf-8', 'ISO-8859-2', $this->order->customer->street), 0, 0, 'L');
                $this->SetY(35);
                $this->SetX(120);
                $this->Cell(25, 6, '', 0, 0, 'L');
                $this->SetY(40);
                $this->SetX(120);
                $this->Cell(25, 6, '', 0, 0, 'L');
                $this->SetY(45);
                $this->SetX(120);
                $this->Cell(25, 6, '', 0, 0, 'L');
                $this->Ln(5);
                $this->SetFont('Arial', 'B', 8);
                $this->SetY(50);
                $this->SetX(10);
                $this->Cell(35, 6, iconv('utf-8', 'ISO-8859-2', 'Megrendelés száma'), 1);
                $this->SetY(50);
                $this->SetX(45);
                $this->Cell(25, 6, $this->order->id, 1, 0, 'C');
                $this->SetY(50);
                $this->SetX(70);
                $this->Cell(35, 6, iconv('utf-8', 'ISO-8859-2', 'Megrendelés dátuma'), 1, 0, 'L');
                $this->SetY(50);
                $this->SetX(105);
                $this->Cell(25, 6, $this->order->ordering_date, 1, 0, 'C');
                $this->SetY(50);
                $this->SetX(130);
                $this->Cell(35, 6, iconv('utf-8', 'ISO-8859-2', 'Szállítási határidö'), 1, 0, 'L');
                $this->SetY(50);
                $this->SetX(165);
                $this->Cell(25, 6, $this->order->delivery_date, 1, 0, 'C');
                $this->Ln();
                $this->SetFont('Arial', 'B', 8);
                $y = 56;
                $this->SetY($y);
                $this->SetX(10);
                $this->Cell(15, 6, iconv('utf-8', 'ISO-8859-2', 'Sorszám'), 1, 'C');
                $this->SetY($y);
                $this->SetX(25);
                $this->Cell(60, 6, iconv('utf-8', 'ISO-8859-2', 'Megnevezés'), 1, 0, 'C');
                $this->SetY($y);
                $this->SetX(85);
                $this->Cell(16, 6, iconv('utf-8', 'ISO-8859-2', 'Méret'), 1, 0, 'C');
                $this->SetY($y);
                $this->SetX(101);
                $this->Cell(20, 6, iconv('utf-8', 'ISO-8859-2', 'Mennyiség'), 1, 0, 'C');
                $this->SetY($y);
                $this->SetX(121);
                $this->Cell(12, 6, 'm2', 1, 0, 'C');
                $this->SetY($y);
                $this->SetX(133);
                $this->Cell(17, 6, 'Ft/m2', 1, 0, 'C');
                $this->SetY($y);
                $this->SetX(150);
                $this->Cell(20, 6, iconv('utf-8', 'ISO-8859-2', 'Nettó ár'), 1, 0, 'C');
                $this->SetY($y);
                $this->SetX(170);
                $this->Cell(20, 6, iconv('utf-8', 'ISO-8859-2', 'Bruttó ár'), 1, 0, 'C');
                $this->Ln();

            }

            public function Body(){
                $y = 62;
                $height = 4;
                $index = 0;
                $total_gross_price = 0;
#                foreach ($this->order->products as $index => $product) {
                foreach ($this->groupedProducts as $group) {
                    $this->SetFont('Arial', '', 7);
                    $this->SetY($y);
                    $this->SetX(10);
                    $this->Cell(15, $height, $index + 1 . ".", 0, 0, 'C');
                    $this->SetY($y);
                    $this->SetX(25);
                    $this->Cell(60, $height, iconv( 'utf-8','ISO- 8859-2',$group['product_name']), 0, 0, 'C');
                    $this->SetY($y);
                    $this->SetX(85);
                    $this->Cell(16, $height, $group['width']  . ' x '. $group['height'] , 0, 0, 'C');
                    $this->SetY($y);
                    $this->SetX(101);
                    $this->Cell(20, $height, $group['total_quantity'] . " db", 0, 0, 'C');
                    $this->SetY($y);
                    $this->SetX(121);
                    $this->Cell(12, $height, number_format($group['total_sqauremeter'], 2), 0, 0, 'C');
                    $this->SetY($y);
                    $this->SetX(133);
                    $this->Cell(17, $height, number_format($group['price_per_squaremeter'], 2), 0, 0, 'R');
                    $this->SetY($y);
                    $this->SetX(150);
                    $this->Cell(20, $height, number_format($group['total_net_price'], 2), 0, 0, 'R');
                    $this->SetY($y);
                    $this->SetX(170);
                    $this->Cell(20, $height, number_format($group['total_gross_price'], 2), 0, 0, 'R');
                    $y += $height;
                    $index +=1;
                    $total_gross_price += $group['total_gross_price'];
                    if ($y >= 240) {
                        $y = 63;
                        $this->addPage();
                }
                    }

                   
    
                
                $this->SetFont('Arial', 'B', 10);
                $this->SetY(252);
                $this->SetX(10);
                $this->Cell(90, 6, iconv('utf-8', 'ISO-8859-2', 'Üzemvezetö'), 0, 0, 'C');
                $this->Cell(90, 6, iconv('utf-8', 'ISO-8859-2', 'Vállalkozó'), 0, 0, 'C');

                $this->SetY(260);
                $this->SetX(10);
                $this->Cell(30, 6, iconv('utf-8', 'ISO-8859-2', 'Összesen'), 1, 0, 'C');
                $this->Cell(20, 6, iconv('utf-8', 'ISO-8859-2', 'Darab'), 1, 0, 'C');
                $this->Cell(20, 6, 'NM', 1, 0, 'C');
                $this->Cell(20, 6, 'FM', 1, 0, 'C');
                $this->Cell(45, 6, iconv('utf-8', 'ISO-8859-2', 'Nettó összeg'), 1, 0, 'C');
                $this->Cell(45, 6, iconv('utf-8', 'ISO-8859-2', 'Bruttó összeg'), 1, 0, 'C');
                $this->SetFont('Arial', '', 10);
                $this->SetY(266);
                $this->SetX(10);
                $this->Cell(30, 6, '', 0, 0, 'C');
                $this->Cell(20, 6, $this->order->products->count(), 1, 0, 'C');
                $this->Cell(20, 6, number_format($this->order->products->sum('squaremeter'), 2, ".", ""), 1, 0, 'C');
                $this->Cell(20, 6, number_format($this->order->products->sum('flowmeter'), 2, ".", ""), 1, 0, 'C');
                $this->Cell(45, 6, number_format($this->order->products->sum('agreed_price'), 2, ".", " ") . " Ft", 1, 0, 'C');
                $this->Cell(45, 6, number_format($total_gross_price, 2, ".", " ") . " Ft", 1, 0, 'C');

              
            }

            public function Footer() {
                $this->SetFont('Arial', 'I', 8);
                $this->SetY(275);
                $this->SetX(90);
                $this->Cell(30, 10, iconv('utf-8', 'ISO-8859-2', 'A mai napon az alábbi megrendeléseket fogadtuk el. Ezek a termékek minöségileg megfelelnek az EMI a-174/97 elöírásainak, melyekre 5 év garanciát vállalunk.'), 0, 0, 'C');
        
                $this->SetY(280);
                $this->SetX(90);
                $this->Cell(30, 10, $this->PageNo() . '/{nb} oldal ', 0, 0, 'C');
        
            }
        };
        

        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->Body();
        $pdf->Output('I', "order_{$order->id}.pdf"); 
         exit;
    }

 #   public function printEtikett($id)
 #   {
 #       $order = Order::findOrFail($id);#

        // Generate the etikett print view or PDF
#        return view('orders.print-etikett', compact('order'));
#    }


public function printEtiketts($orderId)
{
    $order = \App\Models\Order::with('orderProducts.product', 'customer')->findOrFail($orderId);

    $pdf = new Fpdi();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    $marginX = 10;
    $marginY = 20;
    $etikettWidth = 90;
    $etikettHeight = 50;
    $gapX = 10;
    $gapY = 10;

    $x = $marginX;
    $y = $marginY;
    $col = 0;

    foreach ($order->orderProducts as $index => $orderProduct) {
        // Ensure the data is valid
        $barcode = (string) $orderProduct->id;

        // Generate the barcode image as a base64 string
        $dns1d = new DNS1D();
        $barcodeBase64 = $dns1d->getBarcodePNG($barcode, 'C128', 2, 35);

        // Decode the base64 image and save it to a temporary file
        $tempImagePath = tempnam(sys_get_temp_dir(), 'barcode') . '.png';
        file_put_contents($tempImagePath, base64_decode($barcodeBase64));

        #$pdf->Rect($x, $y, $etikettWidth, $etikettHeight);
        $pdf->Image($tempImagePath, $x + 2, $y + 20, 40);

        // Add details
        $customerName = optional($order->customer)->name ?? '';
        $productName = optional($orderProduct->product)->name ?? '';
        $size = "{$orderProduct->width} x {$orderProduct->height}";
        $orderText = $orderProduct->customers_order_text;

        $pdf->SetXY($x + 2, $y + 30);
        $pdf->MultiCell($etikettWidth - 4, 5, 
            iconv('utf-8', 'ISO-8859-2', "$customerName\n$orderText\n$size\n$productName"));

        // Clean up the temporary file
        unlink($tempImagePath);

        // Adjust position for the next etikett
        $col++;
        if ($col >= 2) {
            $col = 0;
            $x = $marginX;
            $y += $etikettHeight + $gapY;
        } else {
            $x += $etikettWidth + $gapX;
        }

        if (($index + 1) % 12 === 0) {
            $pdf->AddPage();
            $x = $marginX;
            $y = $marginY;
            $col = 0;
        }
    }

    $pdf->Output('I', 'etiketts-    '.$order->id);
    exit;
}


}
