<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('user')) {
            return redirect()->route('calendar.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
            ->where('isActive', 1)
            ->first();

        if ($user && $user->verifyPassword($request->password)) {
            session(['user' => $user->toArray()]);
            session()->flash('success', 'Welcome back, ' . $user->nameFirst . '!');

            $intendedUrl = session()->pull('intended_url', route('calendar.index'));
            return redirect($intendedUrl);
        }

        return back()->withErrors(['login' => 'Invalid username or password.'])->withInput(['username' => $request->username]);
    }

    public function logout()
    {
        session()->forget('user');
        session()->flush();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $sessionUser = session('user');
        $user = User::find($sessionUser['id']);

        if (! $user || ! $user->verifyPassword($request->current_password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $newHash = User::legacyPasswordHash($user->username, $request->new_password);
        $user->update(['password' => $newHash]);

        return redirect()->route('calendar.index')->with('success', 'Password changed successfully.');
    }
}
