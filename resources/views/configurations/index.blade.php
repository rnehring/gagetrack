@extends('layouts.app')
@section('title', 'Configurations')
@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-4">Configurations</h1>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Name</th>
                <th class="table-th">Value</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($configurations as $config)
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="{{ route('configurations.edit', $config->id) }}" class="text-brand-600">✏️</a></td>
                    <td class="table-td font-medium">{{ $config->name }}</td>
                    <td class="table-td text-gray-600">{{ $config->value }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="table-td text-center text-gray-400 py-8">No configurations.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
