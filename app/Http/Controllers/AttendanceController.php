<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function storeCheckIn(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $settings = Setting::first();
        $whitelistedIps = explode(',', $settings->whitelisted_ips);

        if (!in_array($request->ip(), $whitelistedIps)) {
            return response()->json([
                'success' => false,
                'message' => 'IP Anda tidak terdaftar dalam whitelist.'
            ], 403);
        }

        $today = Carbon::now()->format('Y-m-d');
        $existingAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in hari ini.'
            ], 409);
        }

        $attendance = new Attendance();
        $attendance->user_id = Auth::id();
        $attendance->date = $today;
        $attendance->check_in = Carbon::parse($request->check_in)->format('H:i:s');
        $attendance->status = 'present';
        $attendance->check_in_ip_address = $request->ip();
        $attendance->created_at = Carbon::now();
        $attendance->updated_at = Carbon::now();
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil!',
            'check_in_time' => $attendance->check_in
        ]);
    }


    public function storeCheckOut(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $settings = Setting::first();

        $whitelistedIps = explode(',', $settings->whitelisted_ips);
        if (!in_array($request->ip(), $whitelistedIps)) {
            return response()->json(['message' => 'IP Anda tidak terdaftar dalam whitelist.'], 403);
        }

        $attendance = Attendance::where('id', $request->attendance_id)
            ->whereDate('created_at', Carbon::now('Asia/Jakarta')->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Absensi tidak ditemukan.'], 404);
        }

        $checkOutStartTime = $settings->check_out_start;
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');

        if ($checkOutStartTime > $currentTime) {
            return response()->json(['message' => 'Belum waktunya check-out.'], 400);
        }

        $attendance->check_out = Carbon::now('Asia/Jakarta')->format('H:i:s');
        $attendance->check_out_ip_address = $request->ip(); // Simpan IP check-out
        $attendance->save();

        return response()->json([
            'success' => true,
            'check_out_time' => $attendance->check_out
        ]);
    }


    public function history(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        return view('attendance.history', compact('startDate', 'endDate'));
    }

    public function historyList(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $checkInStart = Setting::first()->check_in_start;
        $checkInStart = Carbon::parse($checkInStart);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $search = $request->search;

        $user = auth()->user();

        $query = Attendance::with(['user.userDetail'])
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user.userDetail', function ($query) use ($search) {
                    $query->where('full_name', 'like', '%' . $search . '%');
                });
            })
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('date', 'desc');

        $attendances = $query->paginate(10);

        foreach ($attendances as $attendance) {
            if ($attendance->check_in) {
                $checkInTime = Carbon::parse($attendance->check_in);
                if ($checkInTime->gt($checkInStart)) {
                    $attendance->late_minutes = $checkInStart->diffInMinutes($checkInTime);
                } else {
                    $attendance->late_minutes = 0;
                }
            } else {
                $attendance->late_minutes = null;
            }
        }

        return response()->json([
            'data' => $attendances->items(),
            'current_page' => $attendances->currentPage(),
            'last_page' => $attendances->lastPage(),
            'per_page' => $attendances->perPage(),
            'total' => $attendances->total(),
        ]);
    }

}
