@extends('layouts.app')
@section('title', 'Manajemen User (Karyawan)')
@section('page-title', 'Manajemen User (Karyawan')
@section('page-description', 'Kelola User (Karyawan) anda dan perhatikan lebih baik.')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-2">
            <!-- Card Header dengan judul dan tombol tambah pengguna -->
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top">
                <h5 class="mb-0">Daftar Pengguna</h5>
                <a href="{{ route('users.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Pengguna
                </a>
            </div>

            <!-- Card Body dengan Tabel -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Full Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Birth Date</th>
                                <th>Gender</th>
                                <th>Position</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role }}</td>
                                    <td>{{ optional($user->detail)->full_name ?? '-' }}</td>
                                    <td>{{ optional($user->detail)->phone ?? '-' }}</td>
                                    <td>{{ optional($user->detail)->address ?? '-' }}</td>
                                    <td>{{ optional($user->detail)->birth_date ?? '-' }}</td>
                                    <td>{{ optional($user->detail)->gender ?? '-' }}</td>
                                    <td>{{ optional($user->detail->position)->name ?? '-' }}</td>
                                    <td>
                                        <!-- Edit and Delete Buttons -->
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- No Data Message -->
                    @if ($users->isEmpty())
                        <div class="text-center text-muted mt-3">Tidak ada data pengguna.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection