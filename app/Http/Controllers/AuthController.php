<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Regenerasi session untuk keamanan
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    // Proses logout user
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate(); // Hapus session
        $request->session()->regenerateToken(); // Regenerasi CSRF token

        return redirect('/');
    }
}
