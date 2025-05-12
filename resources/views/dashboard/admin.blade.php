@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Kelola Absensi anda dan lihat riwayat absensi yang sudah ada..')

@section('content')
    <div class="row g-4">
        <div class="col-md-6 d-flex align-items-stretch">
            <div class="card shadow-sm p-4 rounded-lg h-100 w-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-calendar-check text-primary me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0">Status Check-in</h5>
                </div>

                @if ($canCheckIn)
                    <div class="alert alert-success d-flex align-items-center p-3 rounded">
                        <i class="fas fa-check-circle me-2"></i>
                        <p class="mb-0">Anda sudah melakukan check-in pada pukul
                            <strong>{{ $canCheckIn->check_in }}</strong> WIB.<br>Semangat menjalani aktivitas hari ini!
                            🚀
                        </p>
                    </div>
                @elseif (!$canCheckIn)
                    <p class="mb-4 text-muted">Mulailah hari Anda dengan semangat! Lakukan check-in untuk memulai aktivitas
                        dan catat kehadiran Anda.</p>

                    <form action="{{ route('attendance.checkin.store') }}" method="POST">
                        @csrf
                        <button id="checkInButton" class="btn btn-success btn-lg w-100">Check-in</button>
                    </form>
                @else
                    <p class="alert alert-info mb-4 text-muted">Absensi sudah dilakukan untuk hari ini. Terima kasih!</p>
                @endif
            </div>
        </div>

        <div class="col-md-6 d-flex align-items-stretch">
            <div class="card shadow-sm p-4 rounded-lg h-100 w-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-sign-out-alt text-success me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0">Status Check-out</h5>
                </div>

                @if ($canCheckIn)
                    @if ($canCheckIn->check_out)
                        <div class="alert alert-success d-flex align-items-center p-3 rounded">
                            <i class="fas fa-check-circle me-2"></i>
                            <p class="mb-0">Anda telah berhasil check-out pada pukul
                                <strong>{{ $canCheckIn->check_out }}</strong> WIB.<br>Terima kasih atas kerja keras
                                Anda hari ini! 💪
                            </p>
                        </div>
                    @elseif (\Carbon\Carbon::now('Asia/Jakarta')->format('H:i') < $settings->check_out_start)
                        <div class="alert alert-warning d-flex align-items-center p-3 rounded">
                            <i class="fas fa-clock me-2"></i>
                            <p class="mb-0">Belum waktunya check-out.<br>Check-out bisa dilakukan mulai pukul
                                <strong>{{ $settings->check_out_start }}</strong> WIB. Tetap semangat!
                            </p>
                        </div>
                    @else
                        <div class="mb-3">
                            <p class="text-muted">Sudah waktunya untuk menyelesaikan hari kerja Anda. Silakan lakukan
                                check-out di bawah ini:</p>
                        </div>
                        <form action="{{ route('attendance.checkout.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="attendance_id" value="{{ $adminAttendance->id }}">
                            <button type="submit" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-sign-out-alt me-2"></i> Check-out Sekarang
                            </button>
                        </form>
                    @endif
                @else
                    <div class="alert alert-info shadow-sm p-3 rounded">
                        <i class="fas fa-info-circle me-2 text-info"></i>
                        <p class="mb-0">Anda belum melakukan check-in hari ini. Silakan check-in terlebih dahulu untuk
                            mengaktifkan check-out.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>



    <style>
        .stats-section {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-icon {
            font-size: 1.5rem;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background-color: #eef1f6;
            color: #4a4a4a;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-title {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
        }

        @media (min-width: 768px) {
            .stat-columns {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 1.25rem;
            }
        }
    </style>

    <div class="stats-section mt-3">
        <div class="stat-columns">
            <div class="stat-item">
                <div class="stat-icon text-success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Hadir</span>
                    <span class="stat-value">{{ $totalPresent }}</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon text-danger">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Tidak Hadir</span>
                    <span class="stat-value">{{ $totalAbsent }}</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon text-primary">
                    <i class="fas fa-list-ul"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Total Absensi</span>
                    <span class="stat-value">{{ $totalPresent + $totalAbsent }}</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon text-secondary">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Cuti Pending</span>
                    <span class="stat-value">{{ $totalPendingExcuses }}</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Cuti Disetujui</span>
                    <span class="stat-value">{{ $totalApprovedExcuses }}</span>
                </div>
            </div>

            <div class="stat-item">
                <div class="stat-icon text-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Cuti Ditolak</span>
                    <span class="stat-value">{{ $totalRejectedExcuses }}</span>
                </div>
            </div>
        </div>
    </div>

@endsection
