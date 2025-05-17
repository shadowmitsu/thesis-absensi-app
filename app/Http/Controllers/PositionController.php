<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $positions = Position::all();
        $editPosition = null;

        if ($request->has('edit')) {
            $editPosition = Position::findOrFail($request->get('edit'));
        }

        return view('positions.index', compact('positions', 'editPosition'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:positions,name',
            'description' => 'nullable|string',
        ]);

        $position = Position::create($request->all());

        return response()->json(['message' => 'Position created successfully.', 'data' => $position]);
    }

    public function edit(Position $position)
    {
        return view('positions.index', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|unique:positions,name,' . $position->id,
            'description' => 'nullable|string',
        ]);

        $position->update($request->all());

        return response()->json(['message' => 'Position updated successfully.', 'data' => $position]);
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return response()->json(['message' => 'Position deleted successfully.']);
    }

}
