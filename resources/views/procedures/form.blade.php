@extends('layouts.app')
@section('title', $procedure ? 'Edit Procedure' : 'Add Procedure')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">{{ $procedure ? 'Edit Procedure' : 'Add Procedure' }}</h1>
    <a href="{{ route('procedures.index') }}" class="btn-secondary">← Back</a>
</div>

<div class="max-w-lg">
    <form method="POST" action="{{ $procedure ? route('procedures.update', $procedure->id) : route('procedures.store') }}" class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
        @csrf
        @if($procedure) @method('PUT') @endif

        <div>
            <label class="form-label">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $procedure?->name) }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="5" class="form-textarea">{{ old('description', $procedure?->description) }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Save</button>
            <a href="{{ route('procedures.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
