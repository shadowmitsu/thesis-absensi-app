<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['detail.position'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $positions = Position::all();
        return view('users.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user',
            'full_name' => 'required',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        UserDetail::create([
            'user_id' => $user->id,
            'position_id' => $request->position_id,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::with('detail')->findOrFail($id);
        $positions = Position::all();
        return view('users.edit', compact('user', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => "required|unique:users,username,$id",
            'email' => "required|email|unique:users,email,$id",
            'role' => 'required|in:admin,user',
            'full_name' => 'required',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->detail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'position_id' => $request->position_id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
            ]
        );

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
