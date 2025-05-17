@extends('layouts.app')
@section('title', 'Riwayat Perizinan')
@section('page-title', 'Riwayat Perizinan')
@section('page-description', 'Halaman untuk melakukan pengecekan riwayat perizinan.')
@section('content')
    <div class="row">
        <div class="col-12">
            <div id="excuseCardForm" class="card shadow-sm border-0 d-none">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i> Formulir Pengajuan Izin</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="toggleExcuseForm()"
                        aria-label="Tutup"></button>
                </div>

                <form method="POST" action="{{ route('excuses.store') }}" id="excuseForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="formMethod">
                    <input type="hidden" name="id" id="excuseId">

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold">Jenis Izin</label>
                                <select class="form-select rounded-3" name="type" id="type" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="sick">Sakit</option>
                                    <option value="leave">Cuti</option>
                                    <option value="personal">Pribadi</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="photo" class="form-label fw-semibold">Upload Bukti (opsional)</label>
                                <input type="file" class="form-control rounded-3" name="photo" id="photo">
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control rounded-3" name="start_date" id="start_date"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" class="form-control rounded-3" name="end_date" id="end_date"
                                    required>
                            </div>

                            <div class="col-12">
                                <label for="reason" class="form-label fw-semibold">Alasan</label>
                                <textarea class="form-control rounded-3" name="reason" id="reason" rows="3"
                                    placeholder="Contoh: Sakit demam, tidak bisa hadir..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-secondary rounded-pill px-4" onclick="toggleExcuseForm()">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white rounded-top shadow-sm px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-white">Data Pengajuan Izin</h5>
                        @if (Auth::user()->role != 'admin')
                            <button class="btn btn-success rounded-pill text-white" type="button"
                                onclick="toggleExcuseForm()">
                                <i class="fas fa-plus me-1"></i> Ajukan Izin
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <form id="filterForm" class="row g-3 mb-4" onsubmit="event.preventDefault(); loadExcuses(1);">
                        <div class="col-12 position-relative">
                            <input type="text" name="search" id="search" class="form-control rounded-3 ps-4 pe-5"
                                placeholder="Cari nama...">
                            <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"
                                style="pointer-events:none;"></i>
                        </div>

                        <div class="col-md-4">
                            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date_filter" class="form-control rounded-3"
                                value="{{ now()->startOfDay()->format('Y-m-d') }}">
                        </div>

                        <div class="col-md-4">
                            <label for="end_date" class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date_filter" class="form-control rounded-3"
                                value="{{ now()->endOfDay()->format('Y-m-d') }}">
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select name="status" id="status" class="form-select rounded-3">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-filter me-2"></i> Filter
                            </button>
                            <button type="button" id="resetBtn" class="btn btn-danger rounded-pill px-4">
                                <i class="fas fa-times-circle me-2"></i> Reset
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="excusesTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Nama</th>
                                    <th style="width: 18%;">Tanggal Izin</th>
                                    <th style="width: 12%;">Jenis</th>
                                    <th style="width: 25%;">Alasan</th>
                                    <th style="width: 10%;">Bukti Izin</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="excusesTableBody">

                            </tbody>
                        </table>

                        <div id="noDataMessage" class="text-center text-muted mt-3" style="display:none;">
                            Tidak ada data perizinan.
                        </div>

                        <nav>
                            <ul class="pagination justify-content-center" id="pagination"></ul>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        const currentUserRole = "{{ Auth::user()->role }}";
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('excuseForm');
            const formCard = document.getElementById('excuseCardForm');
            const resetBtn = document.getElementById('resetBtn');

            const toggleExcuseForm = () => formCard.classList.toggle('d-none');

            const showAlert = (icon, title, text, timer = 2000) => {
                Swal.fire({
                    icon,
                    title,
                    text,
                    timer,
                    showConfirmButton: false
                });
            };

            const showErrors = (errors) => {
                const html = Object.values(errors).map(msg => msg.join(' ')).join('<br>');
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    html
                });
            };

            const formatDate = date => new Date(date).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            const capitalize = str => str.charAt(0).toUpperCase() + str.slice(1);

            const badgeClass = status => ({
                approved: 'success',
                rejected: 'danger',
                pending: 'warning'
            } [status] || 'secondary');

            const actionButtons = id => `
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success" onclick="updateStatusWithConfirm(${id}, 'approved')">Setujui</button>
                    <button class="btn btn-sm btn-danger" onclick="updateStatusWithConfirm(${id}, 'rejected')">Tolak</button>
                </div>
            `;

            window.toggleExcuseForm = toggleExcuseForm;
            window.updateStatusWithConfirm = (id, status) => {
                const statusText = status === 'approved' ? 'menyetujui' : 'menolak';

                Swal.fire({
                    title: `Anda yakin ingin ${statusText} izin ini?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    axios.patch(`/excuses/${id}/update-status`, {
                            status
                        })
                        .then(res => {
                            Swal.fire('Berhasil', res.data.message, 'success');
                            loadExcuses();
                        })
                        .catch(err => {
                            const message = err.response?.data?.message || 'Terjadi kesalahan.';
                            Swal.fire('Gagal', message, 'error');
                        });
                });
            };

            form?.addEventListener('submit', e => {
                e.preventDefault();

                const url = form.action;
                const method = document.getElementById('formMethod').value || 'POST';
                const formData = new FormData(form);
                if (method.toUpperCase() !== 'POST') formData.append('_method', method);

                axios({
                        method,
                        url,
                        data: formData,
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(res => {
                        form.reset();
                        toggleExcuseForm();
                        showAlert('success', 'Berhasil', res.data.message || 'Izin berhasil diajukan!');
                        loadExcuses(1);
                    })
                    .catch(err => {
                        if (err.response?.status === 422) {
                            showErrors(err.response.data.errors);
                        } else {
                            showAlert('error', 'Error', err.response?.data.message ||
                                'Terjadi kesalahan, coba lagi nanti.');
                        }
                    });
            });

            resetBtn?.addEventListener('click', () => {
                document.getElementById('search').value = '';
                document.getElementById('start_date').value = document.getElementById('end_date').value =
                    new Date().toISOString().slice(0, 10);
                document.getElementById('status').value = '';
                loadExcuses(1);
            });

            window.loadExcuses = (page = 1) => {
                const params = {
                    page,
                    search: document.getElementById('search').value,
                    start_date: document.getElementById('start_date_filter').value,
                    end_date: document.getElementById('end_date_filter').value,
                    status: document.getElementById('status').value
                };

                axios.get('{{ route('excuses.history.list') }}', {
                        params
                    })
                    .then(res => {
                        const {
                            data
                        } = res;
                        const tbody = document.getElementById('excusesTableBody');
                        const pagination = document.getElementById('pagination');
                        const noData = document.getElementById('noDataMessage');

                        tbody.innerHTML = '';
                        pagination.innerHTML = '';
                        noData.style.display = data.data.length ? 'none' : 'block';

                        data.data.forEach((excuse, i) => {
                            const no = (data.per_page * (data.current_page - 1)) + i + 1;
                            const typeMap = {
                                sick: 'Sakit',
                                leave: 'Cuti',
                                personal: 'Pribadi'
                            };

                            const displayType = type => typeMap[type] || type;

                            const statusMap = {
                                pending: 'Menunggu',
                                approved: 'Disetujui',
                                rejected: 'Ditolak'
                            };

                            const badgeClass = status => ({
                                approved: 'success',
                                rejected: 'danger',
                                pending: 'warning'
                            } [status] || 'secondary');

                            const capitalize = str => str.charAt(0).toUpperCase() + str.slice(1);
                           tbody.innerHTML += `
                                <tr>
                                    <td>${no}</td>
                                    <td>${excuse.user.user_detail?.full_name || '-'}</td>
                                    <td>${formatDate(excuse.start_date)} - ${formatDate(excuse.end_date)}</td>
                                    <td>${displayType(excuse.type)}</td>
                                    <td>${excuse.reason}</td>
                                    <td>
                                        ${
                                            excuse.photo
                                            ? `<a href="/storage/${excuse.photo}" target="_blank" rel="noopener noreferrer">
                                                <img src="/storage/${excuse.photo}" alt="Bukti Foto" style="max-width: 80px; max-height: 80px; object-fit: cover; border-radius: 4px;">
                                                </a>`
                                            : '<span class="text-muted">Tidak ada foto</span>'
                                        }
                                    </td>

                                    <td>
                                        <span class="badge rounded-pill bg-${badgeClass(excuse.status)}">
                                            ${statusMap[excuse.status] || capitalize(excuse.status)}
                                        </span>
                                    </td>
                                    <td>
                                        ${
                                            excuse.status === 'pending' && currentUserRole === 'admin'
                                                ? actionButtons(excuse.id)
                                                : '<span class="text-muted">Tidak dapat diubah</span>'
                                        }
                                    </td>
                                </tr>`;

                        });

                        generatePagination(data);
                    })
                    .catch(() => showAlert('error', 'Error', 'Gagal memuat data perizinan.'));
            };

            const generatePagination = ({
                current_page,
                last_page
            }) => {
                const pagination = document.getElementById('pagination');
                let html = `
            <li class="page-item ${current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current_page - 1}">Previous</a>
            </li>`;

                for (let i = 1; i <= last_page; i++) {
                    html += `
                <li class="page-item ${i === current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
                }

                html += `
            <li class="page-item ${current_page === last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current_page + 1}">Next</a>
            </li>`;

                pagination.innerHTML = html;

                document.querySelectorAll('#pagination a.page-link').forEach(link =>
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        const page = parseInt(link.dataset.page);
                        if (page && page !== current_page) loadExcuses(page);
                    })
                );
            };

            loadExcuses();
        });
    </script>
@endpush
