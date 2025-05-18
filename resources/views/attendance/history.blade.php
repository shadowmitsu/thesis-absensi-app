@extends('layouts.app')
@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')
@section('page-description', 'Halaman untuk melakukan pengecekan riwayat absensi.')

@section('content')
    <div class="card shadow-sm border-0 rounded-2">
        <div class="card-header bg-primary text-white rounded-top shadow-sm">
            <h5 class="mb-3 text-white">Riwayat Absensi</h5>
        </div>

        <div class="card-body p-3">
            <form id="filterForm" class="mb-3" onsubmit="return false;">
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative flex-grow-1">
                        <input type="text" id="search" name="search" class="form-control ps-4 rounded-3"
                            placeholder="Cari nama..." style="border-color: #ddd; height: 40px;">
                        <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                    </div>

                    @php
                        use Carbon\Carbon;
                        date_default_timezone_set('Asia/Jakarta');

                        $endDate = Carbon::now('Asia/Jakarta');
                        $startDate = $endDate->copy()->subDays(6);
                    @endphp

                    <input type="date" id="start_date" name="start_date" class="form-control rounded-3"
                        value="{{ $startDate->format('Y-m-d') }}" style="height: 38px; max-width: 200px;">

                    <input type="date" id="end_date" name="end_date" class="form-control rounded-3"
                        value="{{ $endDate->format('Y-m-d') }}" style="height: 38px; max-width: 200px;">


                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="filterBtn">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                    <button type="button" id="resetBtn" class="btn btn-danger rounded-pill px-4">
                        <i class="fas fa-times-circle me-2"></i> Reset
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="attendanceTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Lengkap</th>
                            <th>Check In</th>
                            <th>IP Check In</th>
                            <th>Check Out</th>
                            <th>IP Check Out</th>
                            <th>Status</th>
                            <th>Terlambat (Menit)</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody">
                        <tr>
                            <td colspan="8" class="text-center text-muted">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center mt-3" id="pagination">

                </ul>
            </nav>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const attendanceTableBody = document.getElementById('attendanceTableBody');
                const pagination = document.getElementById('pagination');

                function formatDate(dateStr) {
                    if (!dateStr) return '-';
                    const options = {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    };
                    return new Date(dateStr).toLocaleDateString('id-ID', options);
                }

                function getStatusBadge(status) {
                    const map = {
                        'present': 'success',
                        'absent': 'danger',
                        'late': 'warning',
                    };
                    const badgeClass = map[status] || 'secondary';
                    const text = status.charAt(0).toUpperCase() + status.slice(1);
                    return `<span class="badge bg-${badgeClass}">${text}</span>`;
                }

                function renderData(data) {
                    attendanceTableBody.innerHTML = '';
                    if (data.length === 0) {
                        attendanceTableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Tidak ada data absensi pada rentang waktu ini.</td>
                    </tr>`;
                        return;
                    }

                    data.forEach(item => {
                        attendanceTableBody.innerHTML += `
                    <tr>
                        <td>${formatDate(item.date)}</td>
                        <td>${item.user.user_detail?.full_name || '-'}</td>
                        <td>${item.check_in || '-'}</td>
                        <td><small>${item.check_in_ip_address || '-'}</small></td>
                        <td>${item.check_out || '-'}</td>
                        <td><small>${item.check_out_ip_address || '-'}</small></td>
                        <td>${getStatusBadge(item.status)}</td>
                        <td>
                            ${item.late_minutes !== null
                                ? (item.late_minutes > 0
                                    ? `<span class="text-danger">${item.late_minutes} menit</span>`
                                    : `<span class="text-success">Tepat Waktu</span>`)
                                : '-'}
                        </td>
                    </tr>
                `;
                    });
                }

                function renderPagination(currentPage, lastPage) {
                    pagination.innerHTML = '';
                    if (lastPage <= 1) return;

                    function createPageItem(page, text = null, active = false, disabled = false) {
                        return `<li class="page-item ${active ? 'active' : ''} ${disabled ? 'disabled' : ''}">
                    <button class="page-link" ${disabled ? 'tabindex="-1"' : ''} data-page="${page}">
                        ${text || page}
                    </button>
                </li>`;
                    }

                    pagination.innerHTML += createPageItem(currentPage - 1, 'Previous', false, currentPage === 1);

                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(lastPage, startPage + 4);
                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        pagination.innerHTML += createPageItem(i, null, i === currentPage);
                    }

                    pagination.innerHTML += createPageItem(currentPage + 1, 'Next', false, currentPage === lastPage);

                    Array.from(pagination.querySelectorAll('button.page-link')).forEach(button => {
                        button.addEventListener('click', () => {
                            const page = parseInt(button.getAttribute('data-page'));
                            if (!isNaN(page) && page >= 1 && page <= lastPage) {
                                loadData(page);
                            }
                        });
                    });
                }

                function getFilterParams() {
                    return {
                        search: document.getElementById('search').value.trim(),
                        start_date: document.getElementById('start_date').value,
                        end_date: document.getElementById('end_date').value,
                    };
                }

                function loadData(page = 1) {
                    const params = getFilterParams();
                    params.page = page;

                    attendanceTableBody.innerHTML =
                        '<tr><td colspan="8" class="text-center text-muted">Memuat data...</td></tr>';

                    axios.get('{{ route('attendance.history.list') }}', {
                            params
                        })
                        .then(response => {
                            const res = response.data;
                            renderData(res.data);
                            renderPagination(res.current_page, res.last_page);
                        })
                        .catch(() => {
                            attendanceTableBody.innerHTML = `
                        <tr><td colspan="8" class="text-center text-danger">Gagal memuat data absensi.</td></tr>
                    `;
                        });
                }

                document.getElementById('filterBtn').addEventListener('click', () => loadData(1));

                document.getElementById('resetBtn').addEventListener('click', () => {
                    document.getElementById('search').value = '';
                    document.getElementById('start_date').value = '{{ $startDate->format('Y-m-d') }}';
                    document.getElementById('end_date').value = '{{ $endDate->format('Y-m-d') }}';
                    loadData(1);
                });

                loadData();
            });
        </script>
    @endsection
