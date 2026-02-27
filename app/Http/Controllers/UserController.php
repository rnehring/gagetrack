<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('nameLast')->orderBy('nameFirst')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nameFirst'    => 'required|string|max:50',
            'nameLast'     => 'required|string|max:50',
            'username'     => 'required|string|max:50|unique:users,username',
            'emailAddress' => 'nullable|email|max:100',
            'password'     => 'required|string|min:6',
            'isActive'     => 'nullable|boolean',
            'isHidden'     => 'nullable|boolean',
        ]);

        $validated['password'] = User::legacyPasswordHash($validated['username'], $validated['password']);
        $validated['isActive'] = $request->boolean('isActive');
        $validated['isHidden'] = $request->boolean('isHidden');

        User::create($validated);
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nameFirst'    => 'required|string|max:50',
            'nameLast'     => 'required|string|max:50',
            'username'     => 'required|string|max:50|unique:users,username,' . $user->id,
            'emailAddress' => 'nullable|email|max:100',
            'isActive'     => 'nullable|boolean',
            'isHidden'     => 'nullable|boolean',
        ]);

        $validated['isActive'] = $request->boolean('isActive');
        $validated['isHidden'] = $request->boolean('isHidden');

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $validated['password'] = User::legacyPasswordHash($validated['username'], $request->password);
        }

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
