@extends('layouts.app')
@section('title', $supplier ? 'Edit Supplier' : 'Add Supplier')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">{{ $supplier ? 'Edit Supplier: ' . $supplier->name : 'Add Supplier' }}</h1>
    <a href="{{ route('suppliers.index') }}" class="btn-secondary">← Back</a>
</div>

<form method="POST" action="{{ $supplier ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" class="space-y-4">
    @csrf
    @if($supplier) @method('PUT') @endif

    <div class="bg-white rounded-xl shadow border border-gray-100 p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $supplier?->name) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Code</label>
                <input type="text" name="code" value="{{ old('code', $supplier?->code) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Contact</label>
                <input type="text" name="contact" value="{{ old('contact', $supplier?->contact) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $supplier?->email) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $supplier?->phone) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Fax</label>
                <input type="text" name="fax" value="{{ old('fax', $supplier?->fax) }}" class="form-input">
            </div>
            <div class="sm:col-span-2">
                <label class="form-label">Address</label>
                <input type="text" name="address" value="{{ old('address', $supplier?->address) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">City</label>
                <input type="text" name="city" value="{{ old('city', $supplier?->city) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">State</label>
                <input type="text" name="state" value="{{ old('state', $supplier?->state) }}" class="form-input" maxlength="2" placeholder="MI">
            </div>
            <div>
                <label class="form-label">Zip</label>
                <input type="text" name="zipcode" value="{{ old('zipcode', $supplier?->zipcode) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Supplier Type</label>
                <input type="text" name="supplierType" value="{{ old('supplierType', $supplier?->supplierType) }}" class="form-input">
            </div>
            <div class="flex items-center gap-2 pt-5">
                <input type="checkbox" name="isActive" value="1" id="isActive" @checked(old('isActive', $supplier?->isActive ?? true)) class="rounded">
                <label for="isActive" class="form-label mb-0">Active</label>
            </div>
        </div>
    </div>

    <div class="flex gap-3 items-center">
        <button type="submit" class="btn-primary">Save Supplier</button>
        <a href="{{ route('suppliers.index') }}" class="btn-secondary">Cancel</a>
        @if($supplier)
            <form method="POST" action="{{ route('suppliers.destroy', $supplier->id) }}" class="inline ml-auto">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger text-xs"
                    onclick="return confirm('Delete this supplier?')">Delete</button>
            </form>
        @endif
    </div>
</form>

@endsection
