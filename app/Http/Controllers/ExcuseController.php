<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Excuse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExcuseController extends Controller
{
    public function index()
    {
        $excuses = Excuse::where('user_id', auth()->id())->latest()->get();
        return view('excuses.index', compact('excuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:sick,leave,personal',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('excuses', 'public');
        }

        $data['user_id'] = auth()->id();
        Excuse::create($data);

        return redirect()->route('excuses.index')->with('success', 'Izin berhasil diajukan.');
    }

    public function update(Request $request, $id)
    {
        $excuse = Excuse::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $data = $request->validate([
            'type' => 'required|in:sick,leave,personal',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($excuse->photo) {
                Storage::disk('public')->delete($excuse->photo);
            }
            $data['photo'] = $request->file('photo')->store('excuses', 'public');
        }

        $excuse->update($data);
        return redirect()->route('excuses.index')->with('success', 'Izin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $excuse = Excuse::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        if ($excuse->photo) {
            Storage::disk('public')->delete($excuse->photo);
        }

        $excuse->delete();
        return redirect()->route('excuses.index')->with('success', 'Izin berhasil dihapus.');
    }

    public function history(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $statusFilter = $request->status;
        $search = $request->search;

        $user = auth()->user();

        $excuses = Excuse::with(['user.userDetail'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '<=', $endDate->toDateString())
                    ->whereDate('updated_at', '>=', $startDate->toDateString());
            })
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('user.userDetail', function ($query) use ($search) {
                    $query->where('full_name', 'like', '%' . $search . '%');
                });
            })
            ->when($statusFilter, function ($query) use ($statusFilter) {
                return $query->where('status', $statusFilter);
            })
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('start_date', 'desc')
            ->get();

        return view('excuses.history', compact('excuses', 'startDate', 'endDate', 'statusFilter', 'search'));
    }


    public function updateStatus(Request $request, $id)
    {
        $excuse = Excuse::findOrFail($id);

        if ($excuse->status != 'pending') {
            return redirect()->back()->with('error', 'Status izin sudah diproses sebelumnya.');
        }

        $excuse->status = $request->status;
        $excuse->save();

        return redirect()->route('excuses.history')->with('success', 'Status izin berhasil diperbarui.');
    }


}
