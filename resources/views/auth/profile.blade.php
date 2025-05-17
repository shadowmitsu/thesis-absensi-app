@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">Pengaturan Profile</h5>
                    <small>Silahkan Edit Profile kamu atau rubah password kamu</small>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="{{ $user->username }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}"
                                    required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Password Baru (Kosongkan jika tidak ingin diubah)</label>
                                <input type="password" name="password" class="form-control" id="new-password"
                                    placeholder="Kosongkan jika tidak ingin ubah" autocomplete="new-password">

                            </div>
                            <div class="col-md-6">
                                <label>Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control"
                                    value="{{ $user->detail->full_name ?? '' }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="birth_date" class="form-control"
                                    value="{{ $user->detail->birth_date ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label>Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ $user->detail->phone ?? '' }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const data = new FormData(form);

            data.set('_method', 'PUT');

            axios.post("{{ route('profile.update') }}", data, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                        'Content-Type': 'multipart/form-data',
                    }
                })
                .then(response => {
                    Swal.fire('Berhasil', response.data.message, 'success');
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        const messages = Object.values(error.response.data.errors).flat().join('<br>');
                        Swal.fire('Validasi Gagal', messages, 'error');
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan saat memperbarui profil.', 'error');
                    }
                });
        });
    </script>
@endpush
