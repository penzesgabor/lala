<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\BaseMaterialType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PriceHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\Vat;



class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('productGroup', 'baseMaterialType', 'components')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $vats = Vat::all();
        $productGroups = ProductGroup::all();
        $baseMaterialTypes = BaseMaterialType::all();
        $simpleProducts = Product::where('type', 'simple')->get();
        return view('products.create', compact('productGroups', 'baseMaterialTypes', 'simpleProducts','vats'));
    }

                

        public function store(Request $request)
        {
            $rules = [
                'name' => 'required|string|max:255',
                'type' => 'required|in:simple,configurable',
                'base_material_type_id' => 'nullable|exists:base_material_types,id',
                'base_price' => 'nullable|numeric|min:0',
                'vat_id' => 'nullable|exists:vats,id',
                'product_group_id' => 'nullable|exists:product_groups,id',
                'english_name' => 'nullable|string|max:255',
                'weight_per_squaremeter' => 'nullable|numeric',
                'liseccode' => 'nullable|string|max:255',
            ];
        
            // Add validation rules for configurable products
            if ($request->input('type') === 'configurable') {
                $rules['components'] = 'required|array';
                $rules['components.*.id'] = 'required|exists:products,id';
                $rules['components.*.quantity'] = 'required|numeric|min:1';
            }
        
            // Validate the request
            $validated = $request->validate($rules);
        
            // Create the product
            $product = Product::create($validated);
        
            // Attach components if the product is configurable
            if ($request->type === 'configurable') {
                foreach ($validated['components'] as $component) {
                    $product->components()->attach($component['id'], ['quantity' => $component['quantity']]);
                }
            }
        
            return redirect()->route('products.index')->with('success', 'Termék sikeresen hozzáadva.');
        }

    public function edit(Product $product)
    {
        $vats = Vat::all();
        $productGroups = ProductGroup::all();
        $baseMaterialTypes = BaseMaterialType::all();
        $simpleProducts = Product::where('type', 'simple')->get();
        $product->load('components');
        $simpleProducts2 = $simpleProducts->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'thickness' => preg_match('/^\d+/', $product->name, $match) ? $match[0] : 0,
            ];
        });
        

        return view('products.edit', compact('product', 'productGroups', 'baseMaterialTypes', 'simpleProducts','vats', 'simpleProducts2'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:simple,configurable',
            'base_material_type_id' => 'nullable|exists:base_material_types,id',
            'base_price' => 'nullable|numeric|min:0',
            'vat_id' => 'nullable|exists:vats,id',
            'product_group_id' => 'nullable|exists:product_groups,id',
            'english_name' => 'nullable|string|max:255',
            'weight_per_squaremeter' => 'nullable|numeric',
            'liseccode' => 'nullable|string|max:255',
            'components' => 'array',
            'components.*.id' => 'exists:products,id',
            'components.*.quantity' => 'numeric|min:1',
        ]);

        if ($product->base_price != $validated['base_price']) {
            PriceHistory::create([
                'product_id' => $product->id,
                'price' => $validated['base_price'],
                'changed_by' => Auth::user()->name ?? 'System',
            ]);
        }
        $product->update($validated);

        if ($request->type === 'configurable') {
            $product->components()->sync([]);
            foreach ($validated['components'] as $component) {
                $product->components()->attach($component['id'], ['quantity' => $component['quantity']]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Termék módosítva.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Termék törölve.');
    }

    public function updateAllBasePrices(Request $request)
{
    $validated = $request->validate([
        'percentage' => 'required|numeric',
    ]);

    $percentageFactor = 1 + ($validated['percentage'] / 100);

    foreach (Product::all() as $product) {
        $product->update([
            'base_price' => $product->base_price * $percentageFactor,
        ]);
    }

    return redirect()->route('products.index')
        ->with('success', 'Alapár frissítve.');
}



}
