@extends('layouts.app')
@section('title', 'Metadata')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Metadata</h1>
    <a href="{{ route('metadata.create') }}" class="btn-primary">+ Add</a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4">
    <form method="POST" action="{{ route('metadata.search') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div>
            <label class="form-label">Category</label>
            <select name="search_category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" @selected($filters['search_category'] == $cat)>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        <button type="submit" name="reset" value="1" class="btn-secondary">Reset</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Category</th>
                <th class="table-th">Value</th>
                <th class="table-th">Description</th>
                <th class="table-th w-8"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($records as $record)
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="{{ route('metadata.edit', $record->id) }}" class="text-brand-600">✏️</a></td>
                    <td class="table-td font-medium text-gray-500">{{ $record->category }}</td>
                    <td class="table-td">{{ $record->value }}</td>
                    <td class="table-td text-gray-400 text-xs">{{ $record->description }}</td>
                    <td class="table-td">
                        <form method="POST" action="{{ route('metadata.destroy', $record->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs"
                                onclick="return confirm('Delete this metadata?')">✕</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="table-td text-center text-gray-400 py-8">No metadata found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($records->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $records->links() }}</div>
    @endif
</div>

@endsection
