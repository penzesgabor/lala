<?php

namespace App\Http\Controllers;

use App\Models\BaseMaterialType;
use Illuminate\Http\Request;

class BaseMaterialTypeController extends Controller
{
    public function index()
    {
        $baseMaterialTypes = BaseMaterialType::paginate(10);
        return view('base_material_types.index', compact('baseMaterialTypes'));
    }

    public function create()
    {
        return view('base_material_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:base_material_types,name',
        ]);

        BaseMaterialType::create($request->all());

        return redirect()->route('base_material_types.index')->with('success', 'Base Material Type created successfully.');
    }

    public function edit(BaseMaterialType $baseMaterialType)
    {
        return view('base_material_types.edit', compact('baseMaterialType'));
    }

    public function update(Request $request, BaseMaterialType $baseMaterialType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:base_material_types,name,' . $baseMaterialType->id,
        ]);

        $baseMaterialType->update($request->all());

        return redirect()->route('base_material_types.index')->with('success', 'Base Material Type updated successfully.');
    }

    public function destroy(BaseMaterialType $baseMaterialType)
    {
        $baseMaterialType->delete();

        return redirect()->route('base_material_types.index')->with('success', 'Base Material Type deleted successfully.');
    }
}
