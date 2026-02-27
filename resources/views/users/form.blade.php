@extends('layouts.app')
@section('title', $user ? 'Edit User' : 'Add User')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">{{ $user ? 'Edit User: ' . $user->username : 'Add User' }}</h1>
    <a href="{{ route('users.index') }}" class="btn-secondary">← Back</a>
</div>

<div class="max-w-lg">
    <form method="POST" action="{{ $user ? route('users.update', $user->id) : route('users.store') }}" class="bg-white rounded-xl shadow border border-gray-100 p-5 space-y-4">
        @csrf
        @if($user) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="form-label">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="nameFirst" value="{{ old('nameFirst', $user?->nameFirst) }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                <input type="text" name="nameLast" value="{{ old('nameLast', $user?->nameLast) }}" class="form-input" required>
            </div>
        </div>

        <div>
            <label class="form-label">Username <span class="text-red-500">*</span></label>
            <input type="text" name="username" value="{{ old('username', $user?->username) }}" class="form-input" required>
        </div>

        <div>
            <label class="form-label">Email</label>
            <input type="email" name="emailAddress" value="{{ old('emailAddress', $user?->emailAddress) }}" class="form-input">
        </div>

        <div>
            <label class="form-label">{{ $user ? 'New Password (leave blank to keep current)' : 'Password' }} {{ !$user ? '*' : '' }}</label>
            <input type="password" name="password" class="form-input" {{ !$user ? 'required' : '' }}>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="isActive" value="1" @checked(old('isActive', $user?->isActive ?? true)) class="rounded">
                <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="isHidden" value="1" @checked(old('isHidden', $user?->isHidden)) class="rounded">
                <span class="text-sm font-medium text-gray-700">Hidden</span>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Save User</button>
            <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
