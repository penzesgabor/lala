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
    // Create product form
    public function create(Order $order)
    {
        $products = Product::all(); // All products
        $dividers = Product::where('product_group_id', 15)->get(); // Only dividers
        $customerPrices = DB::table('customer_product_prices')
            ->where('customer_id', $order->customer_id)
            ->pluck('custom_price', 'product_id')
            ->toArray();

        // Group the order's products by name and size, summing quantities
        $groupedProducts = $order->products()
            ->with('product') // Load related product details
            ->where('order_id', $order->id) // Ensure scoped to the current order
            ->get()
            ->groupBy(function ($product) {
                return $product->product_id . '_' . $product->width . '_' . $product->height;
            })
            ->map(function ($group) {
                return [
                    'product_name' => $group->first()->product->name,
                    'width' => $group->first()->width,
                    'height' => $group->first()->height,
                    'total_quantity' => $group->count(), // Count rows for quantity
                ];
            })
            ->values(); // Reset keys to sequential

        return view('orders.products.create', compact('order', 'products', 'dividers', 'customerPrices', 'groupedProducts'));
    }

    // Store a new product in the order
    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'height' => 'required|numeric|min:1',
            'width' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:1',
            'squaremeter' => 'nullable|numeric|min:0',
            'flowmeter' => 'nullable|numeric|min:0',
            'calculated_price' => 'nullable|numeric|min:0',
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

        $validated['extracharge'] = $validated['extracharge'] ?? 0;

        for ($i = 0; $i < $validated['quantity']; $i++) {
            OrderProduct::create(array_merge($validated, ['order_id' => $order->id]));
        }

        // Return grouped products
        $groupedProducts = $order->products()
            ->with('product')
            ->where('order_id', $order->id) // Ensure scoped to the current order
            ->get()
            ->groupBy(function ($product) {
                return $product->product_id . '_' . $product->width . '_' . $product->height;
            })
            ->map(function ($group) {
                return [
                    'product_name' => $group->first()->product->name,
                    'width' => $group->first()->width,
                    'height' => $group->first()->height,
                    'total_quantity' => $group->count(), // Count rows for quantity
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'products' => $groupedProducts,
        ]);
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
    public function update(Request $request, Order $order, OrderProduct $product)
    {
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
        ]);

        $product->update(array_merge($validated, ['order_id' => $order->id]));

        return redirect()->route('orders.show', $order)->with('success', 'Product updated successfully.');
    }

    // Delete a product group
    public function deleteGroup(Request $request, Order $order)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
        ]);

        $product = Product::where('name', $validated['product_name'])->firstOrFail();

        $order->products()
            ->where('order_id', $order->id) // Ensure scoped to the current order
            ->where('product_id', $product->id)
            ->where('width', $validated['width'])
            ->where('height', $validated['height'])
            ->delete();

        // Fetch grouped products after deletion
        $groupedProducts = $order->products()
            ->with('product')
            ->where('order_id', $order->id) // Ensure scoped to the current order
            ->get()
            ->groupBy(function ($product) {
                return $product->product_id . '_' . $product->width . '_' . $product->height;
            })
            ->map(function ($group) {
                return [
                    'product_name' => $group->first()->product->name,
                    'width' => $group->first()->width,
                    'height' => $group->first()->height,
                    'total_quantity' => $group->count(), // Count rows for quantity
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
