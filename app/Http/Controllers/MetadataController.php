<?php

namespace App\Http\Controllers;

use App\Models\Metadata;
use Illuminate\Http\Request;

class MetadataController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->has('reset')) {
                session()->forget('metadata_filters');
            } else {
                session(['metadata_filters' => ['search_category' => $request->input('search_category', '')]]);
            }
            return redirect()->route('metadata.index');
        }

        $filters = session('metadata_filters', ['search_category' => '']);
        $query = Metadata::orderBy('category')->orderBy('value');

        if (!empty($filters['search_category'])) {
            $query->where('category', $filters['search_category']);
        }

        $records = $query->paginate(50)->withQueryString();
        $categories = Metadata::categories();

        return view('metadata.index', compact('records', 'filters', 'categories'));
    }

    public function create()
    {
        return view('metadata.form', [
            'metadata' => null,
            'categories' => Metadata::categories(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'value' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);
        Metadata::create($validated);
        return redirect()->route('metadata.index')->with('success', 'Metadata saved successfully.');
    }

    public function edit(Metadata $metadata)
    {
        return view('metadata.form', [
            'metadata' => $metadata,
            'categories' => Metadata::categories(),
        ]);
    }

    public function update(Request $request, Metadata $metadata)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'value' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);
        $metadata->update($validated);
        return redirect()->route('metadata.index')->with('success', 'Metadata updated successfully.');
    }

    public function destroy(Metadata $metadata)
    {
        $metadata->delete();
        return redirect()->route('metadata.index')->with('success', 'Metadata deleted.');
    }
}
