@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Kelola Absensi anda dan lihat riwayat absensi yang sudah ada..')

@section('content')
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
