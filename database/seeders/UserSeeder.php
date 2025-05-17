<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $manager = Position::where('name', 'Manager')->first();
        $staff = Position::where('name', 'Staff')->first();

        $admin = User::create([
            'username' => 'admin01',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        UserDetail::create([
            'user_id' => $admin->id,
            'position_id' => $manager->id,
            'full_name' => 'Admin Utama',
            'phone' => '08123456789',
            'address' => 'Jl. Admin No.1',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
        ]);

        $user = User::create([
            'username' => 'user01',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        UserDetail::create([
            'user_id' => $user->id,
            'position_id' => $staff->id,
            'full_name' => 'Karyawan Satu',
            'phone' => '082233445566',
            'address' => 'Jl. Karyawan No.2',
            'birth_date' => '1995-05-05',
            'gender' => 'female',
        ]);
    }
}
