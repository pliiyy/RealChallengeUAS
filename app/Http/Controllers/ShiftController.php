<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Pagination, misal 10 data per halaman
        $shift = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $shift->appends($request->all());

        return view('shift', compact('shift'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'jam_mulai' => 'required',
        'jam_selesai' => 'required',
        'tipe' => 'required',
        ]);
        $validated["status"] = "AKTIF";
        Shift::create($validated);
        return redirect('/shift')->with('success', 'Shift '. $validated["nama"] .' berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $shift = Shift::findOrFail($id);
        
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'jam_mulai' => 'required',
        'jam_selesai' => 'required',
        'tipe' => 'required',
        ]);

        
        $validated['status'] = "AKTIF";

        $shift->update($validated);

        return redirect('/shift')->with('success', 'Shift ' . $shift->nama . ' berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $shift = Shift::findOrFail($id);
        $shift->status = "NONAKTIF";
        $shift->update();

        return redirect('/shift')->with('success', 'Shift ' . $shift->nama . ' berhasil dihapus!');
    }
}
