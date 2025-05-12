@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')
@section('page-description', 'Halaman untuk mengedit informasi pengguna yang sudah terdaftar')

@section('content')
<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 text-white">Form Edit Pengguna</h5>
                <small>Perbarui data pengguna sesuai kebutuhan</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                                    class="form-control" placeholder="Masukkan username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" id="password" name="password"
                                    class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <input type="text" id="full_name" name="full_name"
                                value="{{ old('full_name', $user->detail->full_name ?? '') }}"
                                class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">No. Telepon</label>
                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone', $user->detail->phone ?? '') }}"
                                class="form-control" placeholder="Contoh: 0812xxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label">Tanggal Lahir</label>
                            <input type="date" id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $user->detail->birth_date ?? '') }}"
                                class="form-control">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Jenis Kelamin</label>
                            <select name="gender" id="gender" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ ($user->detail->gender ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ ($user->detail->gender ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="position_id" class="form-label">Jabatan / Posisi</label>
                            <select name="position_id" id="position_id" class="form-select">
                                <option value="">-- Pilih Posisi --</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}" {{ ($user->detail->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea id="address" name="address" class="form-control" rows="3"
                            placeholder="Masukkan alamat lengkap">{{ old('address', $user->detail->address ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
