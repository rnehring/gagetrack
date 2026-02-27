@extends('layouts.app')
@section('title', 'Procedures')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Procedures</h1>
    <a href="{{ route('procedures.create') }}" class="btn-primary">+ Add Procedure</a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Name</th>
                <th class="table-th">Description</th>
                <th class="table-th w-8"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($procedures as $procedure)
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="{{ route('procedures.edit', $procedure->id) }}" class="text-brand-600">✏️</a></td>
                    <td class="table-td font-medium">{{ $procedure->name }}</td>
                    <td class="table-td text-gray-500 text-xs">{{ Str::limit($procedure->description, 80) }}</td>
                    <td class="table-td">
                        <form method="POST" action="{{ route('procedures.destroy', $procedure->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs"
                                onclick="return confirm('Delete this procedure?')">✕</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="table-td text-center text-gray-400 py-8">No procedures found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
