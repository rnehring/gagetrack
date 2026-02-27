<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index()
    {
        $configurations = Configuration::orderBy('name')->get();
        return view('configurations.index', compact('configurations'));
    }

    public function edit(Configuration $configuration)
    {
        return view('configurations.form', compact('configuration'));
    }

    public function update(Request $request, Configuration $configuration)
    {
        $validated = $request->validate([
            'value' => 'nullable|string|max:500',
        ]);
        $configuration->update($validated);
        return redirect()->route('configurations.index')->with('success', 'Configuration updated successfully.');
    }
}
