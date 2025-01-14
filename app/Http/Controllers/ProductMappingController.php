<?php


namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\CustomerProductMapping;
use Illuminate\Http\Request;

class ProductMappingController extends Controller
{
    public function index()
    {
        $mappings = CustomerProductMapping::with(['customer', 'product'])->paginate(10);
        return view('product-mappings.index', compact('mappings'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('product-mappings.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'customer_product_name' => 'required|string|unique:customer_product_mappings,customer_product_name',
        ]);

        CustomerProductMapping::create($request->all());

        return redirect()->route('product-mappings.index')->with('success', 'Product mapping created successfully.');
    }

    public function edit(CustomerProductMapping $productMapping)
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('product-mappings.edit', compact('productMapping', 'customers', 'products'));
    }

    public function update(Request $request, CustomerProductMapping $productMapping)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'customer_product_name' => 'required|string|unique:customer_product_mappings,customer_product_name,' . $productMapping->id,
        ]);

        $productMapping->update($request->all());

        return redirect()->route('product-mappings.index')->with('success', 'Product mapping updated successfully.');
    }

    public function destroy(CustomerProductMapping $productMapping)
    {
        $productMapping->delete();
        return redirect()->route('product-mappings.index')->with('success', 'Product mapping deleted successfully.');
    }
}
