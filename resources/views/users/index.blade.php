@extends('layouts.app')
@section('title', 'Manajemen User Karyawan')
@section('page-title', 'Manajemen User Karyawan')
@section('page-description', 'Kelola User Karyawan anda dan perhatikan lebih baik.')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-2">
                <div
                    class="card-header bg-primary text-white rounded-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h5 class="mb-0">Daftar Pengguna</h5>
                </div>


                <div class="card-body p-3">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-3">
                        <div class="d-flex flex-grow-1" style="max-width: 400px; width: 100%;">
                            <input type="search" id="searchInput" class="form-control"
                                placeholder="Cari username, nama lengkap, atau posisi..." aria-label="Cari pengguna"
                                style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <button class="btn btn-primary ms-0" id="searchBtn" type="button" aria-label="Cari pengguna"
                                style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                Cari
                            </button>
                        </div>

                        <a href="{{ route('users.create') }}"
                            class="btn btn-success rounded-pill text-white mt-3 mt-md-0 ms-md-3 flex-shrink-0"
                            aria-label="Tambah pengguna baru">
                            <i class="fas fa-plus-circle me-2"></i> Tambah Pengguna
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="usersTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Username</th>
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
                            <tbody id="usersTableBody">
                               
                            </tbody>
                        </table>

                        <div id="noDataMessage" class="text-center text-muted mt-3" style="display:none;">
                            Tidak ada data pengguna.
                        </div>

                        <nav>
                            <ul class="pagination justify-content-center" id="pagination">
                            
                            </ul>
                        </nav>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usersTableBody = document.getElementById('usersTableBody');
            const pagination = document.getElementById('pagination');
            const noDataMessage = document.getElementById('noDataMessage');

            function loadUsers(page = 1, search = '') {
                axios.get('{{ route('users.list') }}', {
                        params: {
                            page: page,
                            search: search
                        }
                    })
                    .then(response => {
                        const data = response.data;
                        usersTableBody.innerHTML = '';
                        pagination.innerHTML = '';

                        if (data.data.length === 0) {
                            noDataMessage.style.display = 'block';
                            return;
                        } else {
                            noDataMessage.style.display = 'none';
                        }

                        data.data.forEach((user, index) => {
                            const rowNumber = (data.per_page * (data.current_page - 1)) + index + 1;
                            usersTableBody.innerHTML += `
                                <tr>
                                    <td>${rowNumber}</td>
                                    <td>${user.username}</td>
                                    <td>${user.role}</td>
                                    <td>${user.detail?.full_name || '-'}</td>
                                    <td>${user.detail?.phone || '-'}</td>
                                    <td>${user.detail?.address || '-'}</td>
                                    <td>${user.detail?.birth_date || '-'}</td>
                                    <td>${
                                    user.detail?.gender
                                    ? (user.detail.gender === 'male' ? 'Laki-laki' : user.detail.gender === 'female' ? 'Perempuan' : user.detail.gender)
                                    : '-'
                                    }</td>
                                    <td>${user.detail?.position?.name || '-'}</td>
                                    <td>
                                        <a href="/users/${user.id}/edit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </a>
                                        <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        generatePagination(data);
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Gagal memuat data pengguna.', 'error');
                    });
            }

            function generatePagination(data) {
                const {
                    current_page,
                    last_page
                } = data;
                let html = '';

                html += `<li class="page-item ${current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${current_page - 1}">Previous</a>
                </li>`;

                for (let i = 1; i <= last_page; i++) {
                    html += `<li class="page-item ${i === current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                }

                html += `<li class="page-item ${current_page === last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${current_page + 1}">Next</a>
                </li>`;

                pagination.innerHTML = html;

                pagination.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (page && page !== current_page) {
                            loadUsers(page);
                        }
                    });
                });
            }

            window.deleteUser = function(userId) {
                Swal.fire({
                    title: 'Yakin ingin menghapus user ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete('/users/' + userId, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => {
                                Swal.fire('Terhapus!', response.data.message, 'success');
                                loadUsers();
                            })
                            .catch(error => {
                                Swal.fire('Error', 'Gagal menghapus user.', 'error');
                            });
                    }
                });
            };

            searchBtn.addEventListener('click', () => {
                currentSearch = searchInput.value.trim();
                loadUsers(1, currentSearch); // Load halaman 1 dengan search keyword
            });

            searchInput.addEventListener('keyup', (event) => {
                if (event.key === 'Enter') {
                    currentSearch = searchInput.value.trim();
                    loadUsers(1, currentSearch);
                }
            });
            loadUsers();
        });
    </script>

@endsection
