<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use App\Models\BaseMaterialType;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    public function index()
    {
        $productGroups = ProductGroup::with('baseMaterialType')->get();
        return view('product_groups.index', compact('productGroups'));
    }

    public function create()
    {
        $baseMaterialTypes = BaseMaterialType::all();
        return view('product_groups.create', compact('baseMaterialTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_material_types_id' => 'required|exists:base_material_types,id',
        ]);

        ProductGroup::create($request->all());

        return redirect()->route('product_groups.index')->with('success', 'Product Group created successfully.');
    }

    public function edit(ProductGroup $productGroup)
    {
        $baseMaterialTypes = BaseMaterialType::all();
        return view('product_groups.edit', compact('productGroup', 'baseMaterialTypes'));
    }

    public function update(Request $request, ProductGroup $productGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_material_types_id' => 'required|exists:base_material_types,id',
        ]);

        $productGroup->update($request->all());

        return redirect()->route('product_groups.index')->with('success', 'Product Group updated successfully.');
    }

    public function destroy(ProductGroup $productGroup)
    {
        $productGroup->delete();

        return redirect()->route('product_groups.index')->with('success', 'Product Group deleted successfully.');
    }
}
