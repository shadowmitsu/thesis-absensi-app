<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp',
            'favicon' => 'nullable|image|mimes:jpg,jpeg,png,ico',
            'check_in_start' => 'required|date_format:H:i',
            'check_out_start' => 'required|date_format:H:i',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius' => 'required|integer|min:1',
            'whitelisted_ips' => 'nullable|string',
        ]);

        $setting = Setting::firstOrNew();

        $setting->site_name = $validated['site_name'];
        $setting->check_in_start = $validated['check_in_start'];
        $setting->check_out_start = $validated['check_out_start'];
        $setting->latitude = $validated['latitude'];
        $setting->longitude = $validated['longitude'];
        $setting->radius = $validated['radius'];

        if ($request->whitelisted_ips) {
            $setting->whitelisted_ips = $validated['whitelisted_ips'];
        }

        if ($request->hasFile('logo')) {
            $setting->logo = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            $setting->favicon = $request->file('favicon')->store('favicons', 'public');
        }

        $setting->save();

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
