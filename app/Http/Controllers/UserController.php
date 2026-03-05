<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'nameFirst'       => 'required|string|max:50',
            'nameLast'        => 'required|string|max:50',
            'username'        => 'required|string|max:50|unique:users,username',
            'emailAddress'    => 'nullable|email|max:100',
            'password'        => 'required|string|min:6',
            'isActive'        => 'nullable|boolean',
            'isActive_master' => 'nullable|boolean',
            'isHidden'        => 'nullable|boolean',
        ]);

        $validated['password']        = User::legacyPasswordHash($validated['username'], $validated['password']);
        $validated['isActive']        = $request->boolean('isActive');
        $validated['isActive_master'] = $request->boolean('isActive_master', true);
        $validated['isHidden']        = $request->boolean('isHidden');

        DB::transaction(function () use ($validated) {
            $user = User::create($validated);
            UserMetadata::updateOrCreate(
                ['id' => $user->id],
                ['isActive' => $validated['isActive'], 'isHidden' => $validated['isHidden']]
            );
        });

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nameFirst'       => 'required|string|max:50',
            'nameLast'        => 'required|string|max:50',
            'username'        => 'required|string|max:50|unique:users,username,' . $user->id,
            'emailAddress'    => 'nullable|email|max:100',
            'isActive'        => 'nullable|boolean',
            'isActive_master' => 'nullable|boolean',
            'isHidden'        => 'nullable|boolean',
        ]);

        $validated['isActive']        = $request->boolean('isActive');
        $validated['isActive_master'] = $request->boolean('isActive_master', true);
        $validated['isHidden']        = $request->boolean('isHidden');

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $validated['password'] = User::legacyPasswordHash($validated['username'], $request->password);
        }

        DB::transaction(function () use ($user, $validated) {
            $user->update($validated);
            UserMetadata::updateOrCreate(
                ['id' => $user->id],
                ['isActive' => $validated['isActive'], 'isHidden' => $validated['isHidden']]
            );
        });

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            UserMetadata::where('id', $user->id)->delete();
            $user->delete();
        });
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
