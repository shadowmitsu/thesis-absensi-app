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
        $settings = Setting::first();
        $whitelistedIps = explode(',', $settings->whitelisted_ips);
        if (!in_array(request()->ip(), $whitelistedIps)) {
            session()->flash('error', 'IP Anda tidak terdaftar dalam whitelist.');
            return redirect()->back();
        }

        $existingAttendance = Attendance::where('user_id', Auth::user()->id)
            ->whereDate('date', Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d'))
            ->first();

        if ($existingAttendance) {
            session()->flash('error', 'Anda sudah melakukan check-in hari ini.');
            return redirect()->back();
        }

        $attendance = new Attendance();
        $attendance->user_id = Auth::user()->id;
        $attendance->date = Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d');
        $attendance->check_in = Carbon::parse($request->check_in)->setTimezone('Asia/Jakarta')->format('H:i:s');
        $attendance->status = 'present';
        $attendance->check_in_ip_address = request()->ip();
        $attendance->created_at = Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        $attendance->updated_at = Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        $attendance->save();

        session()->flash('success', 'Check-in berhasil!');
        return redirect()->back();
    }


    public function storeCheckOut(Request $request)
    {
        $attendance = Attendance::where('id', $request->attendance_id)
            ->whereDate('created_at', Carbon::now()->setTimezone('Asia/Jakarta')->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Absensi tidak ditemukan.'], 400);
        }

        $settings = Setting::first();
        $checkOutStartTime = $settings->check_out_start;
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');

        if ($checkOutStartTime > $currentTime) {
            return response()->json(['message' => 'Anda belum bisa melakukan check-out, waktu check-out belum tiba.'], 400);
        }

        $attendance->check_out = Carbon::now('Asia/Jakarta')->format('H:i:s');
        $attendance->save();

        session()->flash('success', 'Check-in berhasil!');
        return redirect()->back();
    }


    public function history(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $checkInStart = Setting::first()->check_in_start;
        $checkInStart = Carbon::parse($checkInStart);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $search = $request->search;

        $user = auth()->user();

        $attendances = Attendance::with(['user.userDetail'])
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user.userDetail', function ($query) use ($search) {
                    $query->where('full_name', 'like', '%' . $search . '%');
                });
            })
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('date', 'desc')
            ->get();

        foreach ($attendances as $attendance) {
            if ($attendance->check_in) {
                $checkInTime = Carbon::parse($attendance->check_in);
                if ($checkInTime->gt($checkInStart)) {
                    $lateMinutes = $checkInStart->diffInMinutes($checkInTime);
                    $attendance->late_minutes = $lateMinutes;
                } else {
                    $attendance->late_minutes = 0;
                }
            } else {
                $attendance->late_minutes = null;
            }
        }

        return view('attendance.history', compact('attendances', 'startDate', 'endDate', 'checkInStart'));
    }

}
