<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdminProductController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('admin.index', compact('customers'));
    }

    public function getConfigurableProducts(Request $request)
    {
        $configurableProducts = Product::where('type', 'configurable')->get(['id', 'name']);
        return response()->json($configurableProducts);
    }

    public function getConfigurableProductComponents(Request $request)
    {
        $customerId = 2;
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
                $product->components = $this->getConfigurableProductComponentss($product->product_id, $customerId);
            }
        }
        #dd($products);
        return $products;


    }

    public function getConfigurableProductComponentss($productId, $customerId)
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
}
