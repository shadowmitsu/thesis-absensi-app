<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }


    public function store(Request $request)
    {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Akses ditolak');
        }

        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg|max:1024',
            'check_in_start' => 'required|date_format:H:i',
            'check_out_start' => 'required|date_format:H:i',
            'whitelisted_ips' => 'nullable|string',
        ], [
            'site_name.required' => 'Nama situs wajib diisi.',
            'site_name.max' => 'Nama situs maksimal 255 karakter.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.mimes' => 'Logo harus berupa file jpeg, png, jpg, gif, atau svg.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
            'favicon.image' => 'Favicon harus berupa gambar.',
            'favicon.mimes' => 'Favicon harus berupa file ico atau png.',
            'favicon.max' => 'Ukuran favicon maksimal 1MB.',
            'check_in_start.required' => 'Waktu mulai check-in wajib diisi.',
            'check_in_start.date_format' => 'Format waktu mulai check-in harus HH:mm.',
            'check_out_start.required' => 'Waktu mulai check-out wajib diisi.',
            'check_out_start.date_format' => 'Format waktu mulai check-out harus HH:mm.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

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

            return response()->json(['message' => 'Pengaturan berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan pengaturan: ' . $e->getMessage()
            ], 500);
        }
    }

}
