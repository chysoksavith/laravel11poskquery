<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }
    public function login_post(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $remember = $request->has('remember');
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            // Redirect based on user role
            if (Auth::user()->is_role == 1) {
                return redirect()->intended('admin/dashboard');
            } elseif (Auth::user()->is_role == 2) {
                return redirect()->intended('dashboard');
            } else {
                return redirect('/')->with('error', 'Role not available for this email');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid credentials');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
