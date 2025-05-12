@extends('layouts.app')
@section('title', 'Pengaturan Website')
@section('page-title', ' Pengaturan Website')
@section('page-description', 'Halaman untuk melakukan pengaturan website absensi')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!-- Basic Inputs Validation start -->
            <div class="card">
                <div class="card-header">
                    <h5>Basic Inputs Validation</h5>
                    <span>Add class of <code>.form-control</code> with <code>&lt;input&gt;</code> tag</span>
                </div>
                <div class="card-block">
                    <form method="POST" action="{{ route('settings.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" id="site_name" name="site_name"
                                value="{{ old('site_name', $setting->site_name ?? '') }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" id="logo" name="logo" class="form-control">
                            @if (!empty($setting->logo))
                                <img src="{{ asset('storage/' . $setting->logo) }}" height="40" class="mt-2">
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="favicon" class="form-label">Favicon</label>
                            <input type="file" id="favicon" name="favicon" class="form-control">
                            @if (!empty($setting->favicon))
                                <img src="{{ asset('storage/' . $setting->favicon) }}" height="40" class="mt-2">
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="check_in_start" class="form-label">Check In Start</label>
                                <input type="time" id="check_in_start" name="check_in_start"
                                    value="{{ old('check_in_start', \Carbon\Carbon::parse($setting->check_in_start)->format('H:i') ?? '07:00') }}"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="check_out_start" class="form-label">Check Out Start</label>
                                <input type="time" id="check_out_start" name="check_out_start"
                                    value="{{ old('check_out_start', \Carbon\Carbon::parse($setting->check_out_start)->format('H:i') ?? '16:00') }}"
                                    class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="whitelisted_ips" class="form-label">Whitelisted IPs <small>(pisahkan dengan
                                    koma)</small></label>
                            <input type="text" id="whitelisted_ips" name="whitelisted_ips" class="form-control"
                                value="{{ old('whitelisted_ips', $setting->whitelisted_ips ?? '') }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    @endsection
