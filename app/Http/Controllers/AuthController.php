<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_name_or_email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah input adalah username atau email
        $credentials = [
            'password' => $request->password,
        ];

        if (filter_var($request->user_name_or_email, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->user_name_or_email;
        } else {
            $credentials['username'] = $request->user_name_or_email;
        }

        // Proses login
        if (Auth::attempt($credentials)) {
            return redirect()->intended('/dashboard'); // Ganti dengan rute tujuan setelah login
        }

        return back()->withErrors(['user_name_or_email' => 'Username atau Email atau Password tidak sesuai']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
