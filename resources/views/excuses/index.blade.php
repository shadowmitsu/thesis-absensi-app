@extends('layouts.app')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="mb-0">Data Pengajuan Izin</h5>
            <button class="btn btn-primary" onclick="addExcuse()" data-bs-toggle="modal" data-bs-target="#excuseModal">
                <i class="fas fa-plus me-1"></i> Ajukan Izin
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($excuses as $excuse)
                            <tr>
                                <td>{{ $excuse->start_date }} - {{ $excuse->end_date }}</td>
                                <td><span class="badge bg-info text-white">{{ ucfirst($excuse->type) }}</span></td>
                                <td>{{ $excuse->reason }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $excuse->status == 'approved' ? 'success' : ($excuse->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($excuse->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($excuse->photo)
                                        <a href="{{ asset('storage/' . $excuse->photo) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye me-1"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data pengajuan izin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
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
<script type="text/javascript" src="{{ asset('assets/js/modal.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/classie.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/modalEffects.js') }}"></script>
<script>
    function addExcuse() {
        document.getElementById('excuseForm').reset();
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('excuseForm').action = "{{ route('excuses.store') }}";
    }
</script>
@endpush