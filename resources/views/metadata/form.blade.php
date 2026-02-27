@extends('layouts.app')
@section('title', $metadata ? 'Edit Metadata' : 'Add Metadata')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">{{ $metadata ? 'Edit Metadata' : 'Add Metadata' }}</h1>
    <a href="{{ route('metadata.index') }}" class="btn-secondary">← Back</a>
</div>

<div class="max-w-lg">
    <form method="POST" action="{{ $metadata ? route('metadata.update', $metadata->id) : route('metadata.store') }}" class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
        @csrf
        @if($metadata) @method('PUT') @endif

        <div>
            <label class="form-label">Category <span class="text-red-500">*</span></label>
            <input type="text" name="category" value="{{ old('category', $metadata?->category) }}" class="form-input" list="category-list" required>
            <datalist id="category-list">
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">
                @endforeach
            </datalist>
        </div>
        <div>
            <label class="form-label">Value <span class="text-red-500">*</span></label>
            <input type="text" name="value" value="{{ old('value', $metadata?->value) }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Description</label>
            <input type="text" name="description" value="{{ old('description', $metadata?->description) }}" class="form-input">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('metadata.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
