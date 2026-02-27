@extends('layouts.app')
@section('title', 'Suppliers')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Suppliers</h1>
    <a href="{{ route('suppliers.create') }}" class="btn-primary">+ Add Supplier</a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Name</th>
                <th class="table-th">Code</th>
                <th class="table-th">Contact</th>
                <th class="table-th">Email</th>
                <th class="table-th">Phone</th>
                <th class="table-th">Active</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($suppliers as $supplier)
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-brand-600 hover:text-brand-800">✏️</a></td>
                    <td class="table-td font-medium">
                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-brand-700 hover:underline">{{ $supplier->name }}</a>
                    </td>
                    <td class="table-td text-gray-500">{{ $supplier->code }}</td>
                    <td class="table-td text-gray-500">{{ $supplier->contact }}</td>
                    <td class="table-td text-gray-500">{{ $supplier->email }}</td>
                    <td class="table-td text-gray-500">{{ $supplier->phone }}</td>
                    <td class="table-td">
                        @if($supplier->isActive)
                            <span class="text-green-600">✓</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="table-td text-center text-gray-400 py-8">No suppliers found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
