@extends('layouts.app')
@section('title', 'Users')
@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold text-gray-800">Users</h1>
    <a href="{{ route('users.create') }}" class="btn-primary">+ Add User</a>
</div>

<div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="table-th w-8"></th>
                <th class="table-th">Name</th>
                <th class="table-th">Username</th>
                <th class="table-th">Email</th>
                <th class="table-th">Active</th>
                <th class="table-th w-8"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="table-td"><a href="{{ route('users.edit', $user->id) }}" class="text-brand-600">✏️</a></td>
                    <td class="table-td font-medium">{{ $user->nameLast }}, {{ $user->nameFirst }}</td>
                    <td class="table-td text-gray-500">{{ $user->username }}</td>
                    <td class="table-td text-gray-500">{{ $user->emailAddress }}</td>
                    <td class="table-td">
                        @if($user->isActive)
                            <span class="text-green-600">✓</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="table-td">
                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs"
                                onclick="return confirm('Delete this user?')">✕</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="table-td text-center text-gray-400 py-8">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
