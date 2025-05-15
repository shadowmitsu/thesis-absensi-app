@extends('layouts.app')
@section('title', 'Riwayat Perizinan')
@section('page-title', 'Riwayat Perizinan')
@section('page-description', 'Halaman untuk melakukan pengecekan riwayat perizinan.')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white rounded-top shadow-sm px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-white">Data Pengajuan Izin</h5>
                        <button class="btn btn-primary" onclick="addExcuse()" data-bs-toggle="modal"
                            data-bs-target="#excuseModal">
                            <i class="fas fa-plus me-1"></i> Ajukan Izin
                        </button>
                    </div>
                    <form method="GET" class="row g-3">
                        <div class="col-12 position-relative">
                            <input type="text" name="search" class="form-control rounded-3 ps-4"
                                placeholder="Cari nama..." value="{{ request('search') }}">
                            <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control rounded-3"
                                value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control rounded-3"
                                value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                                </option>
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-filter me-2"></i> Filter
                            </button>
                            <a href="{{ route('excuses.history') }}" class="btn btn-danger text-white rounded-pill px-4">
                                <i class="fas fa-times-circle me-2"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Tanggal Izin</th>
                                    <th>Jenis</th>
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($excuses as $excuse)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $excuse->user->userDetail->full_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($excuse->start_date)->format('d-m-Y') }} -
                                            {{ \Carbon\Carbon::parse($excuse->end_date)->format('d-m-Y') }}</td>
                                        <td>{{ ucfirst($excuse->type) }}</td>
                                        <td>{{ $excuse->reason }}</td>
                                        <td>
                                            <span
                                                class="badge rounded-pill bg-{{ $excuse->status == 'approved' ? 'success' : ($excuse->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($excuse->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($excuse->status == 'pending')
                                                <form action="{{ route('excuses.updateStatus', $excuse->id) }}"
                                                    method="POST" class="d-flex gap-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button name="status" value="approved"
                                                        class="btn btn-icon btn-success btn-sm" title="Setujui">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                    <button name="status" value="rejected"
                                                        class="btn btn-icon btn-danger btn-sm" title="Tolak">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">Tidak dapat diubah</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Tidak ada data perizinan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <div class="modal fade" id="excuseModal" tabindex="-1" aria-labelledby="excuseModalLabel" aria-hidden="true"
        style="z-index: 999999">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST" id="excuseForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="formMethod">
                    <input type="hidden" name="id" id="excuseId">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="excuseModalLabel">Ajukan Izin</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Jenis Izin</label>
                                <select class="form-select" name="type" id="type" required>
                                    <option value="sick">Sakit</option>
                                    <option value="leave">Cuti</option>
                                    <option value="personal">Pribadi</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="photo" class="form-label">Upload Bukti (opsional)</label>
                                <input type="file" class="form-control" name="photo" id="photo">
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" required>
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" required>
                            </div>

                            <div class="col-12">
                                <label for="reason" class="form-label">Alasan</label>
                                <textarea class="form-control" name="reason" id="reason" rows="3"
                                    placeholder="Contoh: Sakit demam, tidak bisa hadir..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
    function addExcuse() {
        document.getElementById('excuseForm').reset();
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('excuseForm').action = "{{ route('excuses.store') }}";
    }
</script>
@endpush