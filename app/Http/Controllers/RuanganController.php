<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Ruangan::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }

        // Pagination, misal 10 data per halaman
        $ruangan = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $ruangan->appends($request->all());

        return view('ruangan', compact('ruangan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|unique:ruangan,kode',
        'kapasitas' => 'required',
        ]);
        $validated["status"] = "AKTIF";
        Ruangan::create($validated);
        return redirect('/ruangan')->with('success', 'Ruangan '. $validated["nama"] .' berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|unique:ruangan,kode,'.$id,
        'kapasitas' => 'required',
        ]);

        
        $validated['status'] = "AKTIF";

        $ruangan->update($validated);

        return redirect('/ruangan')->with('success', 'Ruangan ' . $ruangan->nama . ' berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->status = "NONAKTIF";
        $ruangan->update();

        return redirect('/ruangan')->with('success', 'Ruangan ' . $ruangan->nama . ' berhasil dihapus!');
    }
}
