<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            ['name' => 'Manager', 'description' => 'Memimpin dan mengawasi tim'],
            ['name' => 'Supervisor', 'description' => 'Mengatur operasional harian'],
            ['name' => 'Staff', 'description' => 'Karyawan pelaksana tugas'],
            ['name' => 'IT Support', 'description' => 'Menangani masalah teknis'],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
