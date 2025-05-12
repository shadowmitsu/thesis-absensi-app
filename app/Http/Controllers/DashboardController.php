<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Excuse;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $settings = Setting::first();

        $user = auth()->user();
        $dateNow = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $canCheckIn = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $dateNow)
            ->first();

        if ($user->role == 'admin') {
            $attendances = Attendance::with('user')->get();
            $excuses = Excuse::all();
            $totalPresent = $attendances->where('status', 'present')->count();
            $totalAbsent = $attendances->where('status', 'absent')->count();
            $totalAttendances = $attendances->count();

            $lateCount = $attendances->filter(function ($attendance) {
                $settings = Setting::first();
                $checkInTime = Carbon::parse($attendance->check_in);
                $checkInStartTime = Carbon::parse($settings->check_in_start);
                return $checkInTime->gt($checkInStartTime);
            })->count();

            $latePercentage = $totalAttendances > 0 ? ($lateCount / $totalAttendances) * 100 : 0;

            $totalPendingExcuses = $excuses->where('status', 'pending')->count();
            $totalApprovedExcuses = $excuses->where('status', 'approved')->count();
            $totalRejectedExcuses = $excuses->where('status', 'rejected')->count();

            $adminAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', today())
                ->first();

            return view('dashboard.admin', compact(
                'attendances',
                'excuses',
                'totalPresent',
                'totalAbsent',
                'latePercentage',
                'totalPendingExcuses',
                'totalApprovedExcuses',
                'totalRejectedExcuses',
                'adminAttendance',
                'lateCount',
                'totalAttendances',
                'settings',
                'canCheckIn' // Kirimkan status absensi hari ini
            ));
        }


        $attendance = Attendance::where('user_id', $user->id)->whereDate('date', today())->first();
        return view('dashboard.user', compact('canCheckIn', 'settings'));
    }
}
