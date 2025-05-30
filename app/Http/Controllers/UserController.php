<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        return view('users.index');
    }

    public function list(Request $request)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $query = User::with(['detail.position']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhereHas('detail', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                            ->orWhereHas('position', function ($q3) use ($search) {
                                $q3->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $users = $query->paginate(10);

        return response()->json($users);
    }


    public function create()
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $positions = Position::all();
        return view('users.create', compact('positions'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $rules = [
            'username' => 'required|unique:users',
            'email' => 'required|email|min:6|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user',
            'full_name' => 'required',
        ];

        $messages = [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.min' => 'Email minimal 6 karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
        ];


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
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

        return response()->json(['message' => 'User berhasil ditambahkan.'], 200);
    }

    public function edit($id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $user = User::with('detail')->findOrFail($id);
        $positions = Position::all();
        return view('users.edit', compact('user', 'positions'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $rules = [
            'username' => "required|unique:users,username,$id",
            'email' => "required|email|unique:users,email,$id",
            'role' => 'required|in:admin,user',
            'full_name' => 'required',
        ];


        $messages = [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

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

        return response()->json(['message' => 'User berhasil diperbarui.'], 200);
    }

    public function destroy($id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}
