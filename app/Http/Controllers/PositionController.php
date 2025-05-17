<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $positions = Position::all();
        $editPosition = null;

        if ($request->has('edit')) {
            $editPosition = Position::findOrFail($request->get('edit'));
        }

        return view('positions.index', compact('positions', 'editPosition'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $request->validate([
            'name' => 'required|unique:positions,name',
            'description' => 'nullable|string',
        ]);

        $position = Position::create($request->all());

        return response()->json(['message' => 'Position created successfully.', 'data' => $position]);
    }

    public function edit(Position $position)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        return view('positions.index', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $request->validate([
            'name' => 'required|unique:positions,name,' . $position->id,
            'description' => 'nullable|string',
        ]);

        $position->update($request->all());

        return response()->json(['message' => 'Position updated successfully.', 'data' => $position]);
    }

    public function destroy(Position $position)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }
        $position->delete();

        return response()->json(['message' => 'Position deleted successfully.']);
    }

}
