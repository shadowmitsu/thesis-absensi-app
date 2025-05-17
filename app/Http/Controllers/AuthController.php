<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_name_or_email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'password' => $request->password,
        ];

        if (filter_var($request->user_name_or_email, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->user_name_or_email;
        } else {
            $credentials['username'] = $request->user_name_or_email;
        }

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['user_name_or_email' => 'Username atau Email atau Password tidak sesuai']);

    }

    public function showProfile()
    {
        $user = Auth::user()->load('detail');
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        // return $request;
        $user = Auth::user();

        $rules = [
            'username' => 'required|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'full_name' => 'required|string',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string',
        ];

        $messages = [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'birth_date.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Update user data
        $dataToUpdate = [
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        // Update or create user detail
        $user->detail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $request->full_name,
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
            ]
        );

        return response()->json(['message' => 'Profil berhasil diperbarui.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
