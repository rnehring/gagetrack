@extends('layouts.app')
@section('title', 'Change Password')
@section('content')
<div class="max-w-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Change Password</h1>
    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('change-password.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-input" required>
            </div>
            <div>
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="form-input" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Change Password</button>
                <a href="{{ route('calendar.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
