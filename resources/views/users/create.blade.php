@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')
@section('page-description', 'Halaman untuk menambahkan pengguna baru ke sistem')

@section('content')
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">Form Tambah Pengguna</h5>
                    <small>Isi data pengguna dengan lengkap</small>
                </div>
                <div class="card-body">
                    <form id="userForm">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                                        class="form-control" placeholder="Masukkan username" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Masukkan password" >
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-select" >
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Nama Lengkap</label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                                    class="form-control" placeholder="Masukkan nama lengkap" >
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">No. Telepon</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                    class="form-control" placeholder="Contoh: 0812xxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <select name="gender" id="gender" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="position_id" class="form-label">Jabatan / Posisi</label>
                                <select name="position_id" id="position_id" class="form-select">
                                    <option value="">-- Pilih Posisi --</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}"
                                            {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea id="address" name="address" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const requiredFields = form.querySelectorAll('[required]');
            let errors = [];

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    const label = form.querySelector(`label[for="${field.id}"]`);
                    const labelText = label ? label.innerText : field.name;
                    errors.push(`${labelText} wajib diisi.`);
                }
            });

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form tidak lengkap',
                    html: errors.map(e => `• ${e}`).join('<br>'),
                });
                return;
            }

            const formData = new FormData(form);

            axios.post('{{ route('users.store') }}', formData)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.data.message || 'User berhasil ditambahkan.',
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = '{{ route('users.index') }}';
                    });
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        let errors = error.response.data.errors;
                        let messages = Object.values(errors).map(err => `- ${err[0]}`).join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi server gagal',
                            html: messages
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: error.response?.data?.message || 'Gagal menyimpan data.'
                        });
                    }
                });
        });
    </script>

@endsection
