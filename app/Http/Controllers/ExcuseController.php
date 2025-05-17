<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Excuse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExcuseController extends Controller
{
    public function index()
    {
        $excuses = Excuse::where('user_id', auth()->id())->latest()->get();
        return view('excuses.index', compact('excuses'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $messages = [
            'type.required' => 'Jenis izin wajib diisi.',
            'type.in' => 'Jenis izin harus salah satu dari: sakit, cuti, atau pribadi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'reason.string' => 'Alasan harus berupa teks.',
            'photo.image' => 'File bukti harus berupa gambar.',
            'photo.max' => 'Ukuran file bukti maksimal 2MB.',
        ];

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:sick,leave,personal',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ], $messages);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('excuses', 'public');
        }

        $data['user_id'] = auth()->id();
        $now = Carbon::now('Asia/Jakarta');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        Excuse::create($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Izin berhasil diajukan.'
            ], 201);
        }

        return redirect()->route('excuses.index')->with('success', 'Izin berhasil diajukan.');
    }

    public function historyList(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $statusFilter = $request->status;
        $search = $request->search;
        $user = auth()->user();

        $query = Excuse::with(['user.userDetail'])
            ->whereDate('created_at', '<=', $endDate->toDateString())
            ->whereDate('updated_at', '>=', $startDate->toDateString())
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user.userDetail', function ($query) use ($search) {
                    $query->where('full_name', 'like', '%' . $search . '%');
                });
            })
            ->when($statusFilter, function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('start_date', 'desc');

        $excuses = $query->paginate(10);

        return response()->json($excuses);
    }

    public function history()
    {
        return view('excuses.history');
    }


    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        date_default_timezone_set('Asia/Jakarta');
        $excuse = Excuse::find($id);

        if (!$excuse) {
            return response()->json([
                'message' => 'Data izin tidak ditemukan.'
            ], 404);
        }

        if ($excuse->status !== 'pending') {
            return response()->json([
                'message' => 'Status izin sudah diproses sebelumnya.'
            ], 422);
        }

        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
        ], [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus berupa approved atau rejected.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $excuse->status = $request->status;
        $excuse->save();

        return response()->json([
            'message' => 'Status izin berhasil diperbarui.',
            'data' => $excuse
        ]);
    }


}
