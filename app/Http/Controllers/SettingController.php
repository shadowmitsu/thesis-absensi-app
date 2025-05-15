<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $setting = Setting::firstOrNew();

            $setting->site_name = $request->input('site_name');
            $setting->check_in_start = $request->input('check_in_start');
            $setting->check_out_start = $request->input('check_out_start');
            $setting->whitelisted_ips = $request->input('whitelisted_ips');

            if ($request->hasFile('logo')) {
                $setting->logo = $request->file('logo')->store('logos', 'public');
            }

            if ($request->hasFile('favicon')) {
                $setting->favicon = $request->file('favicon')->store('favicons', 'public');
            }

            $setting->save();

            DB::commit();

            return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $e->getMessage();

            return redirect()->back()->withErrors('Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

}
