<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        Setting::create([
            'site_name' => 'Absensi PT Damai Glass',
            'logo' => 'storage/settings/logo.png',
            'favicon' => 'storage/settings/favicon.ico',
            'check_in_start' => '07:00:00',
            'check_out_start' => '16:00:00',
            'whitelisted_ips' => '127.0.0.1,192.11.7.8',
        ]);
    }
}
