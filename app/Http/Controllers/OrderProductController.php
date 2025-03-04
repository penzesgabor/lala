<?php
    
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;
#use setasign\Fpdf\Fpdf;
use setasign\Fpdf\Fpdf;
use PDF;
class OrderProductController extends Controller
{
   
public function create(Order $order)
{
    
    $customerId =$order->customer_id;
    #$products = Product::all(); // All products
    #$dividers = Product::where('product_group_id', operator: 5)->get(); // Only dividers
    
    $dividers = Product::where('product_group_id', 5)
    ->leftJoin('customer_product_prices as cpp', function ($join) use ($customerId) {
        $join->on('cpp.product_id', '=', 'products.id')
             ->where('cpp.customer_id', '=', $customerId);
    })
    ->select(
        'products.*',
        DB::raw('COALESCE(cpp.custom_price, products.base_price) as divider_price')
    )
    ->get();
    
    #dd($dividers);

    $products = DB::table('products as p')
    ->leftJoin('customer_product_prices as cpp', function ($join) use ($customerId) {
        $join->on('cpp.product_id', '=', 'p.id')
            ->where('cpp.customer_id', '=', $customerId);
    })
    ->select(
        'p.id as product_id',
        'p.name as product_name',
        'p.type as product_type',
        DB::raw('COALESCE(cpp.custom_price, p.base_price) as product_price')
    )
    ->get();

    // If product is configurable, fetch components
    foreach ($products as $product) {
        if ($product->product_type === 'configurable') {
            $product->components = $this->getConfigurableProductComponents($product->product_id, $customerId);
        }
    }
        #dd($dividers);
 
      $groupedProducts = $order->products()
        ->with('product') 
        ->where('order_id', $order->id) 
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('randomid')
        ->map(function ($group) {
            return [
                'product_name' => $group->first()->product->name,
                'width' => $group->first()->width,
                'height' => $group->first()->height,
                'total_quantity' => $group->count(), // Count rows for quantity
                'product_id' => $group->first()->product->id, // Ensure ID is included
                 'extracharge' => $group->first()->extracharge ?? 0,
                'squaremeter' => $group->first()->squaremeter ?? 0,
                'flowmeter' => $group->first()->flowmeter ?? 0,
                'calculated_price' => $group->first()->calculated_price ?? 0,
                'agreed_price' => $group->first()->agreed_price ?? 0,
                'divider_id' => $group->first()->divider_id ?? '',
                'divider_length' => $group->first()->divider_length ?? '',
                'dividercross' => $group->first()->dividercross ?? '',
                'dividerend' => $group->first()->dividerend ?? '',
                'gasfilling' => $group->first()->gasfilling ?? '',
                'barcode' => $group->first()->barcode ?? '',
                'customers_order_text' => $group->first()->customers_order_text ?? '',
                'notes' => $group->first()->notes ?? '',
                'randomid' => $group->first()->randomid,
            ];
        })
        ->sortByDesc('created_at') // Sort the final grouped collection by creation date (optional if already sorted)
        ->values(); 
    #dd($groupedProducts);
    return view('orders.products.create', compact('order', 'products', 'dividers',  'groupedProducts'));
}


public function getConfigurableProductComponents($productId, $customerId)
{
    return DB::table('configurable_product_components as cpc')
        ->join('products as sp', 'cpc.simple_product_id', '=', 'sp.id')
        ->leftJoin('customer_product_prices as cpp', function ($join) use ($customerId) {
            $join->on('cpp.product_id', '=', 'sp.id')
                 ->where('cpp.customer_id', '=', $customerId);
        })
        ->where('cpc.configurable_product_id', $productId)
        ->groupBy('sp.base_material_type_id') // Group by base material type only
        ->havingRaw('SUM(COALESCE(cpp.custom_price, sp.base_price)) > 0') // Exclude groups with price 0 or NULL
        ->select(
            'sp.base_material_type_id',
            DB::raw('SUM(COALESCE(cpp.custom_price, sp.base_price) ) as total_price')
        )
        ->get();
}

private function getProductPrice($product, $customerPrices)
{
    
    if (isset($customerPrices[$product->id])) {
        return (float) $customerPrices[$product->id];
    }

    if (!is_null($product->base_price) && $product->base_price != 0) {
        return (float) $product->base_price;
    }
    
    if ($product->type === 'configurable') {
      
        $calculatedPrice = $product->components->sum(function ($component) use ($customerPrices) {
            if (isset($customerPrices[$component->id])) {
                #dd($component->id);
                return (float) $customerPrices[$component->id] * 1;
            }
            #dd(($component->base_price ?? 0) * $component->pivot->quantity);
            return (float) ($component->base_price ?? 0) * $component->pivot->quantity;
        });

        #dd($calculatedPrice);
        return $calculatedPrice;
    }

    // 4. Default for simple products without base price
    return 0;
}


// Store a new product in the order
    public function store(Request $request, Order $order)
    {
        #dd($request);
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'height' => 'required|numeric|min:1',
            'width' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:1',
            'squaremeter' => 'nullable|numeric|min:0',
            'flowmeter' => 'nullable|numeric|min:0',
            'calculated_price' => 'nullable|numeric|min:0',
            'agreed_price' => 'nullable|numeric|min:0',
            'divider_id' => 'nullable|exists:products,id',
            'divider_length' => 'nullable|numeric|min:0',
            'dividercross' => 'nullable|integer|min:0',
            'dividerend' => 'nullable|integer|min:0',
            'gasfilling' => 'nullable|boolean',
            'extracharge' => 'nullable|numeric|min:0|max:100',
            'customers_order_text' => 'nullable|string',
            'notes' => 'nullable|string',
            'barcode' => 'nullable|string',
        ]);
        $validated['randomid'] = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        
        $validated['extracharge'] = $validated['extracharge'] ?? 0;
        
        for ($i = 0; $i < $validated['quantity']; $i++) {
            OrderProduct::create(array_merge($validated, ['order_id' => $order->id]));
        }

        // Return grouped products
        $groupedProducts = $order->products()
            ->with('product')
            ->where('order_id', $order->id) 
            ->get()
            ->groupBy('randomid')
            ->map(function ($group) {
                return [
                    'product_name' => $group->first()->product->name,
                    'width' => $group->first()->width,
                    'height' => $group->first()->height,
                    'total_quantity' => $group->count(),
                    'randomid' => $group->first()->randomid,
                ];
            })
            ->values();

       // return response()->json([
       //     'success' => true,
       //     'products' => $groupedProducts,
       //  ]);
       return redirect()->route('order.products.create', $order->id)
    ->with('success', 'A termék sikeresen hozzáadva a rendeléshez.');
    }

    // Edit a product
    public function edit(Order $order, OrderProduct $product)
    {
        $products = Product::all(); // All products for selection
        $dividers = Product::where('product_group_id', 15)->get(); // Only dividers
        $customerPrices = DB::table('customer_product_prices')
            ->where('customer_id', $order->customer_id)
            ->pluck('custom_price', 'product_id')
            ->toArray();

        return view('orders.products.edit', compact('order', 'product', 'products', 'dividers', 'customerPrices'));
    }

    // Update a product
    public function update(Request $request, Order $order, $randomid)
    { 
        //dd($request);
        $order->products()
            ->where('randomid', $request->randomid) 
            ->delete();
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'height' => 'required|numeric|min:1',
            'width' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:1',
            'divider_id' => 'nullable|exists:products,id',
            'divider_length' => 'nullable|numeric|min:0',
            'dividercross' => 'nullable|integer|min:0',
            'dividerend' => 'nullable|integer|min:0',
            'gasfilling' => 'nullable|boolean',
            'extracharge' => 'nullable|numeric|min:0|max:100',
            'customers_order_text' => 'nullable|string',
            'notes' => 'nullable|string',
            'squaremeter' => 'nullable|numeric|min:0',
            'flowmeter' => 'nullable|numeric|min:0',
            'calculated_price' => 'nullable|numeric|min:0',
            'agreed_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string',
            

        ]);

       // $order->products()
       // ->where('randomid', $randomid)
       // ->update($validated);
       

       $validated['randomid'] = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
        
       $validated['extracharge'] = $validated['extracharge'] ?? 0;
       
       for ($i = 0; $i < $validated['quantity']; $i++) {
           OrderProduct::create(array_merge($validated, ['order_id' => $order->id]));
       }

    return redirect()->route('order.products.create', $order->id)
        ->with('success', 'A termék sikeresen módosítva lett.');

        //$product->update(array_merge($validated, ['order_id' => $order->id]));

        // return redirect()->route('orders.show', $order)->with('success', 'Product updated successfully.');
        //return response()->json(['success' => true, 'message' => 'Product updated successfully']);


    }

    // Delete a product group
    public function deleteGroup(Request $request, Order $order)
    {
        # dd($request);
        //$validated = $request->validate([
        //    'randomid' => 'required|numeric',
        //]);

        //$product = Product::where('name', $validated['product_name'])->firstOrFail();

        $order->products()
            ->where('randomid', $request->randomid) 
            ->delete();

        // Fetch grouped products after deletion
        $groupedProducts = $order->products()
            ->with('product')
            ->where('order_id', $order->id) // Ensure scoped to the current order
            ->get()
            ->groupBy('randomid')
            ->map(function ($group) {
                return [
                    'product_name' => $group->first()->product->name,
                    'width' => $group->first()->width,
                    'height' => $group->first()->height,
                    'total_quantity' => $group->count(), 
                    'randomid' => $group->first()->randomid,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'products' => $groupedProducts,
        ]);
    }

    // Fetch product details for editing
    public function getDetails(Request $request, Order $order)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
        ]);

        $product = Product::where('name', $validated['product_name'])->firstOrFail();

        $orderProduct = $order->products()
            ->where('order_id', $order->id) // Ensure scoped to the current order
            ->where('product_id', $product->id)
            ->where('width', $validated['width'])
            ->where('height', $validated['height'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'product' => [
                'product_id' => $orderProduct->product_id,
                'height' => $orderProduct->height,
                'width' => $orderProduct->width,
                'quantity' => $order->products()
                    ->where('product_id', $product->id)
                    ->where('width', $validated['width'])
                    ->where('height', $validated['height'])
                    ->count(),
                'squaremeter' => $orderProduct->squaremeter,
                'flowmeter' => $orderProduct->flowmeter,
                'calculated_price' => $orderProduct->calculated_price,
            ],
        ]);
    }

   

}
