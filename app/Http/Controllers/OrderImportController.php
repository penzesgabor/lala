<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class OrderImportController extends Controller
{

    public function showImportForm($customer_id = null)
    {
        if (!$customer_id) {
            return redirect()->route('customers.index')->with('error', 'Customer ID is required.');
        }

        $customer = \App\Models\Customer::findOrFail($customer_id);
        return view('orders.import.form', compact('customer'));
    }


    public function processImport(Request $request)
    {
        #dd($request);
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        
        $file = $request->file('csvfile');

        $rows = array_map(function ($line) {
            $convertedLine = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');
            return str_getcsv($convertedLine, ';');
        }, file($file->getRealPath()));

        $rows = array_filter($rows, function ($row) {
            return array_filter($row); 
        });

        $header = [
            'customer_reference_id',
            'counter',
            'customer_order_text',
            'quantity',
            'position',
            'width',
            'height',
            'customer_product_name',
            'customer_product_name_long',
            'position2',
            'position3',
            'position4',
            'position5',
            'position6',
            'position7',
            'position8',
            'barcode',
            'vmi',
        ];
        #dd($rows);
        $products = collect($rows)->map(function ($row) use ($header) {
            return array_combine($header, array_slice($row, 0, count($header)));
        });

        $customer_id = $request->customer_id;

        $order = session('order');

        $customerProductNames = $products->pluck('customer_product_name')->unique();
        $matchedProducts = \App\Models\CustomerProductMapping::where('customer_id', $customer_id)
            ->whereIn('customer_product_name', $customerProductNames)
            ->with('product')
            ->get()
            ->keyBy('customer_product_name');

        $internalProducts = \App\Models\Product::all(['id', 'name']);

        return view('orders.import.match', compact('products', 'matchedProducts', 'internalProducts', 'customer_id', 'order'));
    }

    public function saveMatching(Request $request)
    {


        $request->validate([
            'matches' => 'required|array',
            'matches.*' => 'nullable|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
        ]);
    
        foreach ($request->matches as $customerProductName => $productId) {
            if ($productId) {
                \App\Models\CustomerProductMapping::updateOrCreate(
                    [
                        'customer_product_name' => $customerProductName,
                        'customer_id' => $request->customer_id,
                    ],
                    [
                        'product_id' => $productId,
                    ]
                );
            }
        }
       
        $order = \App\Models\Order::create([
            'customer_id' => $request->customer_id,
            'ordering_date' => now(),
            'delivery_date' => null, 
            'notes' => 'Imported from CSV',
            'production_date' => null, 
            'isbilled' => false,
            'isdelivered' => false,
            'imported' => true,
            'delivery_address_id' => null,
        ]);


        $products = collect(json_decode($request->products, true));
        #dd($request);
        
        foreach ($products as $productData) {
            $productId = $request->matches[$productData['customer_product_name']] ?? null;
        
            if ($productId) {
                $squareMeter = ($productData['width'] * $productData['height']) / 1e6;
                $flowMeter = (2 * ($productData['width'] + $productData['height'])) / 1000;
        
                // Check if the product is configurable
                $product = \App\Models\Product::with('components')->find($productId);
                $isConfigurable = $product->type === "configurable";
                $totalPrice = 0;
                #dd($product);
                if ($isConfigurable) {
                    // Get all components
                    $componentPrices = DB::table('configurable_product_components as cpc')
                    ->join('products as sp', 'cpc.simple_product_id', '=', 'sp.id')
                    ->leftJoin('customer_product_prices as cpp', function ($join) use ($order) {
                        $join->on('cpp.product_id', '=', 'sp.id')
                            ->where('cpp.customer_id', '=', $order->customer_id);
                    })
                    ->where('cpc.configurable_product_id', $productId)
                    ->groupBy('sp.base_material_type_id') // Group by base material type
                    ->havingRaw('SUM(COALESCE(cpp.custom_price, sp.base_price)) > 0') // Exclude groups with price 0 or NULL
                    ->select(
                        'sp.base_material_type_id',
                        DB::raw('SUM(COALESCE(cpp.custom_price, sp.base_price)) as total_price')
                    )
                    ->get()
                    ->pluck('total_price', 'base_material_type_id'); // Convert collection to key-value pairs
    
                    #dd($componentPrices);
                    // Calculate price based on material type
                    $squareMeterPrice = ($componentPrices->get(1, 0) * $squareMeter); // Material Type 1
                    $flowMeterPrice = ($componentPrices->get(2, 0) * $flowMeter); // Material Type 2
        
                    // Total price
                    $totalPrice = $squareMeterPrice + $flowMeterPrice;
                } else {
                    // Get base price for simple products
                    $basePrice = \App\Models\CustomerProductPrice::where('customer_id', $order->customer_id)
                        ->where('product_id', $productId)
                        ->value('custom_price')
                        ?? $product->base_price
                        ?? 0;
        
                    $totalPrice = $squareMeter * $basePrice;
                }
        
                $randomId = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
                \App\Models\OrderProduct::create([
                    'order_id' => $order->id,
                    'randomid' => $randomId,
                    'product_id' => $productId,
                    'height' => $productData['height'],
                    'width' => $productData['width'],
                    'squaremeter' => $squareMeter,
                    'flowmeter' => $flowMeter,
                    'calculated_price' => $totalPrice,
                    'agreed_price' => $totalPrice,
                    'customers_order_text' => $productData['customer_order_text'],
                    'notes' => $productData['customer_reference_id'],
                    'barcode' => $productData['barcode'],
                    'customer_product_name' => $productData['customer_product_name_long'],
                ]);
            }
        }
        

        return redirect()->route('orders.import.third-step', ['order_id' => $order->id])
            ->with('success', 'Order saved successfully. Proceed to finalize the order.');
    }



    public function saveOrder(Request $request)
    {

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|json',
        ]);

        $customer = \App\Models\Customer::findOrFail($request->customer_id);
        $products = collect(json_decode($request->products, true));

        $order = \App\Models\Order::create([
            'customer_id' => $customer->id,
            'ordering_date' => now(),
            'delivery_date' => now()->addDays(7), 
            'notes' => 'Imported from CSV',
            'production_date' => now()->addDays(3), 
            'isbilled' => false,
            'isdelivered' => false,
            'imported' => true,
        ]);

        foreach ($products as $productData) {
            $mappedProduct = \App\Models\CustomerProductMapping::where('customer_product_name', $productData['customer_product_name'])
                ->where('customer_id', $customer->id)
                ->first();

            if ($mappedProduct) {
                $product = $mappedProduct->product;
                
                for ($i = 0; $i < $productData['quantity']; $i++) {
                    $squareMeter = ($productData['width'] * $productData['height']) / 1e6;
                    $flowMeter = (2 * ($productData['width'] + $productData['height'])) / 1000;
                    $basePrice = $product->base_price ?? 0;
                    $customerPrice = \App\Models\CustomerProductPrice::where('customer_id', $customer->id)
                        ->where('product_id', $product->id)
                        ->first()->price ?? $basePrice;

                    $calculatedPrice = $squareMeter * $customerPrice;

                    \App\Models\OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'height' => $productData['height'],
                        'width' => $productData['width'],
                        'squaremeter' => $squareMeter,
                        'flowmeter' => $flowMeter,
                        'calculated_price' => $calculatedPrice,
                        'agreed_price' => $calculatedPrice,
                        'customers_order_text' => $productData['customer_order_text'],
                        'notes' => 'Imported',
                        'customer_product_name' => $productData['customer_product_name'],
                    ]);
                }
            }
        }

        return redirect()->route('orders.show', $order->id)->with('success', 'Order saved successfully.');
    }
    public function thirdStepForm($order_id)
    {
        $order = \App\Models\Order::findOrFail($order_id);
        $orderProducts = \App\Models\OrderProduct::where('order_id', $order_id)
        ->where('calculated_price', 0.00)
        ->get();
        $deliveryAddresses = \App\Models\DeliveryAddress::where('customer_id', $order->customer_id)->get();
        $defaultDeliveryAddressId = $order->delivery_address_id ?? $deliveryAddresses->first()->id ?? null;

        return view('orders.import.third-step', compact('order', 'orderProducts', 'deliveryAddresses','defaultDeliveryAddressId'));    
    }


    
    public function saveThirdStep(Request $request, $order_id)
{
    $order = \App\Models\Order::findOrFail($order_id);

    $request->validate([
        'delivery_date' => 'required|date',
        'delivery_address_id' => 'required|numeric',      
        'production_date' => 'required|date',
        'prices' => 'nullable|array',
        'prices.*' => 'nullable|numeric|min:0',
    ]);

    if ($request->has('prices')) {
        foreach ($request->prices as $orderProductId => $price) {
            $orderProduct = \App\Models\OrderProduct::find($orderProductId);
            if ($orderProduct) {
                $orderProduct->update([
                    'calculated_price' => $price,
                    'agreed_price' => $price,
                ]);
            }
        }
    }

    // Update order details
    $order->update([
        'delivery_date' => $request->delivery_date,
        'delivery_address_id' => $request->delivery_address_id,
        'production_date' => $request->production_date,
    ]);

    return redirect()->route('orders.show', $order_id)->with('success', 'Order finalized successfully.');
}




}
