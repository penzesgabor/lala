<?php

namespace App\Http\Controllers;

use App\Models\Trolley;
use Illuminate\Http\Request;

class TrolleyController extends Controller
{
    public function index()
    {
        $trolleys = Trolley::all();
        return view('trolleys.index', compact('trolleys'));
    }

    public function create()
    {
        return view('trolleys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'space' => 'required|integer|min:0',
        ]);

        Trolley::create($request->all());

        return redirect()->route('trolleys.index')->with('success', 'Trolley created successfully.');
    }

    public function edit(Trolley $trolley)
    {
        return view('trolleys.edit', compact('trolley'));
    }

    public function update(Request $request, Trolley $trolley)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'space' => 'required|integer|min:0',
        ]);

        $trolley->update($request->all());

        return redirect()->route('trolleys.index')->with('success', 'Trolley updated successfully.');
    }

    public function destroy(Trolley $trolley)
    {
        $trolley->delete();

        return redirect()->route('trolleys.index')->with('success', 'Trolley deleted successfully.');
    }
}
