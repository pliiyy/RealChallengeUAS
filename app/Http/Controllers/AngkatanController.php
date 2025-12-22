<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use Illuminate\Http\Request;

class AngkatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Angkatan::with(['kelas']);

        if ($request->filled('search')) {
            $query->where('tahun', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }

        // Pagination, misal 10 data per halaman
        $angkatan = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $angkatan->appends($request->all());

        return view('angkatan', compact('angkatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'tahun' => 'required',
        ]);
        $validated["status"] = "AKTIF";
        Angkatan::create($validated);
        return redirect('/angkatan')->with('success', 'Angkatan '. $validated["tahun"] .' berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $angkatan = Angkatan::findOrFail($id);
        
        $validated = $request->validate([
        'tahun' => 'required',
        ]);

        
        $validated['status'] = "AKTIF";

        $angkatan->update($validated);

        return redirect('/angkatan')->with('success', 'Angkatan ' . $angkatan->nama . ' berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $angkatan = Angkatan::findOrFail($id);
        $angkatan->status = "NONAKTIF";
        $angkatan->update();

        return redirect('/angkatan')->with('success', 'Angkatan ' . $angkatan->nama . ' berhasil dihapus!');
    }
}
