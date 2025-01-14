<?php
namespace App\Http\Controllers;

use App\Models\Vat;
use Illuminate\Http\Request;

class VatController extends Controller
{
    public function index()
    {
        $vats = Vat::paginate(10);
        return view('vats.index', compact('vats'));
    }

    public function create()
    {
        return view('vats.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vats,name',
            'value' => 'required|numeric|min:0|max:100',
        ]);

        Vat::create($validated);

        return redirect()->route('vats.index')->with('success', 'VAT created successfully.');
    }

    public function edit(Vat $vat)
    {
        return view('vats.edit', compact('vat'));
    }

    public function update(Request $request, Vat $vat)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vats,name,' . $vat->id,
            'value' => 'required|numeric|min:0|max:100',
        ]);

        $vat->update($validated);

        return redirect()->route('vats.index')->with('success', 'VAT updated successfully.');
    }

    public function destroy(Vat $vat)
    {
        $vat->delete();

        return redirect()->route('vats.index')->with('success', 'VAT deleted successfully.');
    }
}
