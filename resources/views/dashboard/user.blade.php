@extends('layouts.app')
@section('title', 'Dashboard Absensi')
@section('page-title', ' Dashboard Absensi')
@section('page-description', 'Kelola absensi kamu agar menjadi lebih baik.')

@section('content')
    <div class="row g-4">
        <div class="col-md-6 d-flex align-items-stretch">
            <div class="card shadow-sm p-4 rounded-lg h-100 w-100">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-calendar-check text-primary me-2" style="font-size: 1.5rem;"></i>
                    <h5 class="mb-0">Status Check-in</h5>
                </div>

                <div id="checkInContainer">
                    @if ($canCheckIn)
                        <div class="alert alert-success d-flex align-items-center p-3 rounded">
                            <i class="fas fa-check-circle me-2"></i>
                            <p class="mb-0">
                                Anda sudah melakukan check-in pada pukul
                                <strong>{{ $canCheckIn->check_in }}</strong> WIB.<br>
                                Semangat menjalani aktivitas hari ini! 🚀
                            </p>
                        </div>
                    @else
                        <p class="mb-4 text-muted">
                            Mulailah hari Anda dengan semangat! Lakukan check-in untuk memulai aktivitas dan catat kehadiran
                            Anda.
                        </p>

                        <button id="checkInButton" class="btn btn-success btn-lg w-100">Check-in</button>
                        <div id="checkInStatus" class="mt-3"></div>
                    @endif
                </div>
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

                        <input type="hidden" id="attendanceId" value="{{ $canCheckIn->id }}">
                        <button id="checkOutButton" class="btn btn-warning btn-lg w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> Check-out Sekarang
                        </button>
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

    <script>
        document.getElementById('checkInButton')?.addEventListener('click', async () => {
            const now = new Date();
            const currentTime = now.toTimeString().slice(0, 8);

            try {
                const response = await axios.post("{{ route('attendance.checkin.store') }}", {
                    check_in: currentTime
                });

                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Check-in Berhasil!',
                        text: `Check-in pada pukul ${response.data.check_in_time} WIB.`,
                        confirmButtonText: 'OK'
                    });

                    document.getElementById('checkInButton').disabled = true;
                    document.getElementById('checkInButton').innerText = 'Sudah Check-in';
                }

            } catch (error) {
                const message = error.response?.data?.message || 'Terjadi kesalahan saat check-in.';

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: message,
                    confirmButtonText: 'Tutup'
                });
            }
        });
    </script>

    <script>
        document.getElementById('checkOutButton')?.addEventListener('click', async () => {
            const attendanceId = document.getElementById('attendanceId').value;

            try {
                const response = await axios.post("{{ route('attendance.checkout.store') }}", {
                    attendance_id: attendanceId
                });

                if (response.data.success || response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Check-out Berhasil!',
                        text: `Sampai jumpa! Terima kasih atas kerja keras hari ini 💪`,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                }

            } catch (error) {
                const message = error.response?.data?.message || 'Terjadi kesalahan saat check-out.';

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: message,
                    confirmButtonText: 'Tutup'
                });
            }
        });
    </script>
@endsection
