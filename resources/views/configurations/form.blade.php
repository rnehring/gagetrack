@extends('layouts.app')
@section('title', 'Edit Configuration')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Edit: {{ $configuration->name }}</h1>
    <a href="{{ route('configurations.index') }}" class="btn-secondary">← Back</a>
</div>

<div class="max-w-lg">
    <form method="POST" action="{{ route('configurations.update', $configuration->id) }}" class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="form-label">Name</label>
            <input type="text" value="{{ $configuration->name }}" class="form-input bg-gray-50" disabled>
        </div>
        <div>
            <label class="form-label">Value</label>
            <input type="text" name="value" value="{{ old('value', $configuration->value) }}" class="form-input">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('configurations.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
