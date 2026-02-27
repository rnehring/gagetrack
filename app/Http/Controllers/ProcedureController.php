<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    public function index()
    {
        $procedures = Procedure::orderBy('name')->get();
        return view('procedures.index', compact('procedures'));
    }

    public function create()
    {
        return view('procedures.form', ['procedure' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        Procedure::create($validated);
        return redirect()->route('procedures.index')->with('success', 'Procedure saved successfully.');
    }

    public function edit(Procedure $procedure)
    {
        return view('procedures.form', compact('procedure'));
    }

    public function update(Request $request, Procedure $procedure)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
        $procedure->update($validated);
        return redirect()->route('procedures.index')->with('success', 'Procedure updated successfully.');
    }

    public function destroy(Procedure $procedure)
    {
        $procedure->delete();
        return redirect()->route('procedures.index')->with('success', 'Procedure deleted.');
    }
}
