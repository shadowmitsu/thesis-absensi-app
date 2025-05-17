@extends('layouts.app')
@section('title', 'Posisi Karyawan')
@section('page-title', 'Posisi Karyawan')
@section('page-description', 'Mengelola Posisi Karyawan')

@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">List Positions</h5>
                    <a class="btn btn-success rounded-pill text-white" href="{{ route('positions.index') }}">+ Tambah Position</a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($positions as $position)
                                <tr>
                                    <td>{{ $position->name }}</td>
                                    <td>{{ $position->description }}</td>
                                    <td>
                                        <a href="{{ route('positions.index', ['edit' => $position->id]) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $position->id }}"
                                            data-url="{{ route('positions.destroy', $position->id) }}">Hapus</button>

                                    </td>
                                </tr>
                            @endforeach
                            @if ($positions->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada data.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card" id="formCard">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($editPosition) ? 'Edit Position' : 'Tambah Position' }}</h5>
                </div>
                <div class="card-body">
                    <form id="position-form" data-edit="{{ isset($editPosition) ? '1' : '0' }}"
                        data-action="{{ isset($editPosition) ? route('positions.update', $editPosition->id) : route('positions.store') }}"
                        data-method="{{ isset($editPosition) ? 'PUT' : 'POST' }}">
                        @csrf
                        @if (isset($editPosition))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Posisi</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $editPosition->name ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $editPosition->description ?? '') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit"
                                class="btn {{ isset($editPosition) ? 'btn-warning' : 'btn-primary' }}">{{ isset($editPosition) ? 'Perbarui' : 'Simpan' }}</button>
                            @if (isset($editPosition))
                                <a href="{{ route('positions.index') }}" class="btn btn-secondary">Batal</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const formCard = document.getElementById('formCard');
            formCard.scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>

    <script>
        document.getElementById('position-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const isEdit = form.getAttribute('data-edit') === '1';
            const url = form.getAttribute('data-action');
            const method = form.getAttribute('data-method');
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;

            axios({
                    method: method,
                    url: url,
                    data: {
                        name: name,
                        description: description,
                        _method: method,
                        _token: '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: isEdit ? 'Berhasil Diupdate' : 'Berhasil Disimpan',
                        text: response.data.message || 'Data berhasil disimpan.',
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = '/positions';
                    });
                })
                .catch(error => {
                    const message = error.response?.data?.message || 'Terjadi kesalahan';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: message
                    });
                });
        });
    </script>

    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.dataset.url;

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(url, {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            })
                            .then(response => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.data.message ||
                                        'Data berhasil dihapus.',
                                    showConfirmButton: true
                                }).then(() => {
                                    window.location.href = '/positions';
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menghapus',
                                    text: error.response?.data?.message ||
                                        'Terjadi kesalahan.'
                                });
                            });
                    }
                });
            });
        });
    </script>

@endsection
