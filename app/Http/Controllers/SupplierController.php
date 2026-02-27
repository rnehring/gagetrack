<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.form', ['supplier' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateSupplier($request);
        Supplier::create($validated);
        return redirect()->route('suppliers.index')->with('success', 'Supplier saved successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $this->validateSupplier($request);
        $supplier->update($validated);
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }

    private function validateSupplier(Request $request): array
    {
        return $request->validate([
            'name' => 'nullable|string|max:100',
            'contact' => 'nullable|string|max:100',
            'code' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:2',
            'zipcode' => 'nullable|string|max:10',
            'supplierType' => 'nullable|string|max:50',
            'isActive' => 'nullable|boolean',
        ]);
    }
}
