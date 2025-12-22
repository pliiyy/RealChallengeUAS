<?php

namespace App\Http\Controllers;

use App\Models\Matakuliah;
use Illuminate\Http\Request;

class MatakuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = Matakuliah::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%')->orWhere('kode', 'like', '%'.$request->search.'%')->orWhere('semester', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }

        // Pagination, misal 10 data per halaman
        $matakuliah = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $matakuliah->appends($request->all());

        return view('matakuliah', compact('matakuliah'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|max:255',
        'sks' => 'required',
        ]);

        $validated["status"] = "AKTIF";
        Matakuliah::create($validated);
        return redirect('/matakuliah')->with('success', 'Matakuliah '. $validated["nama"] .' berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $matakuliah = Matakuliah::findOrFail($id);
        
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|max:255',
        'sks' => 'required',
        ]);

        
        $validated['status'] = "AKTIF";

        $matakuliah->update($validated);

        return redirect('/matakuliah')->with('success', 'Matakuliah ' . $matakuliah->nama . ' berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $matakuliah = Matakuliah::findOrFail($id);
        $matakuliah->status = "NONAKTIF";
        $matakuliah->update();

        return redirect('/matakuliah')->with('success', 'Matakuliah ' . $matakuliah->nama . ' berhasil dihapus!');
    }
}
