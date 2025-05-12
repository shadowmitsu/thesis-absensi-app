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
     // Menyimpan data baru
     public function store(Request $request)
     {
         $request->validate([
             'name' => 'required|unique:positions,name',
             'description' => 'nullable|string',
         ]);
 
         Position::create($request->all());
         return redirect()->route('positions.index')->with('success', 'Position created successfully.');
     }
 
     // Menampilkan form edit
     public function edit(Position $position)
     {
         return view('positions.index', compact('position'));
     }
 
     // Mengupdate data
     public function update(Request $request, Position $position)
     {
         $request->validate([
             'name' => 'required|unique:positions,name,' . $position->id,
             'description' => 'nullable|string',
         ]);
 
         $position->update($request->all());
         return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
     }
 
     // Menghapus data
     public function destroy(Position $position)
     {
         $position->delete();
         return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
     }
}
