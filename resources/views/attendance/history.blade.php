@extends('layouts.app')
@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')
@section('page-description', 'Halaman untuk melakukan pengecekan riwayat absensi.')

@section('content')
    <div class="card shadow-sm border-0 rounded-2">
        <div class="card-header bg-primary text-white rounded-top shadow-sm">
            <h5 class="mb-3 text-white">Riwayat Absensi</h5>
        
            <form method="GET" class="mb-3">
                <div class="d-flex align-items-center gap-3">
                    <!-- Input Pencarian -->
                    <div class="position-relative flex-grow-1">
                        <input type="text" name="search" class="form-control ps-4 rounded-3" placeholder="Cari nama..."
                            value="{{ request('search') }}" style="border-color: #ddd; height: 40px;">
                        <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                    </div>
                    
                    <!-- Input Tanggal Mulai dan Selesai -->
                    <input type="date" name="start_date" class="form-control rounded-3" 
                        value="{{ request('start_date', $startDate->format('Y-m-d')) }}" style="height: 38px; max-width: 200px;">
                    <input type="date" name="end_date" class="form-control rounded-3" 
                        value="{{ request('end_date', $endDate->format('Y-m-d')) }}" style="height: 38px; max-width: 200px;">
                    
                    <!-- Tombol Filter -->
                    <button class="btn btn-primary rounded-3 d-flex align-items-center" type="submit" style="height: 38px;">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                    
                    <!-- Tombol Reset -->
                    <a href="{{ route('attendance.history') }}" class="btn btn-danger rounded-3" style="height: 38px;">
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
                            <th>Tanggal</th>
                            <th>Nama Lengkap</th>
                            <th>Check In</th>
                            <th>IP Check In</th>
                            <th>Check Out</th>
                            <th>IP Check Out</th>
                            <th>Status</th>
                            <th>Terlambat (Menit)</th> <!-- Kolom Keterlambatan -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $attendance)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                <td>{{ $attendance->user->userDetail->full_name ?? '-' }}</td>
                                <td>{{ $attendance->check_in ?? '-' }}</td>
                                <td><small>{{ $attendance->check_in_ip_address ?? '-' }}</small></td>
                                <td>{{ $attendance->check_out ?? '-' }}</td>
                                <td><small>{{ $attendance->check_out_ip_address ?? '-' }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status == 'present' ? 'success' : 'danger' }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($attendance->late_minutes !== null)
                                        @if ($attendance->late_minutes > 0)
                                            <span class="text-danger">{{ $attendance->late_minutes }} menit</span>
                                        @else
                                            <span class="text-success">Tepat Waktu</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Tidak ada data absensi pada rentang
                                    waktu ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
