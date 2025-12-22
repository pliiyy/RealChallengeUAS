<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\Kelas;
use App\Models\Prodi;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::with(["angkatan",'prodi']);

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%')->orWhere('kode', 'like', '%'.$request->search.'%')->orWhere('semester', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('angkatan_id')) {
            $query->where('angkatan_id','=',$request->angkatan_id);
        }
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id','=',$request->prodi_id);
        }
        if ($request->filled('tipe')) {
            $query->where('tipe','=',$request->tipe);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }

        // Pagination, misal 10 data per halaman
        $kelas = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $kelas->appends($request->all());
        $prodi = Prodi::where('status','=','AKTIF')->get();
        $angkatan = Angkatan::where('status','=','AKTIF')->get();

        return view('kelas', compact('kelas','prodi','angkatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|max:255',
        'prodi_id' => 'required',
        'angkatan_id' => 'required',
        'semester' => 'required',
        'tipe' => 'required',
        ]);

        $validated["status"] = "AKTIF";
        Kelas::create($validated);
        return redirect('/kelas')->with('success', 'Kelas '. $validated["nama"] .' berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $kelas = Kelas::findOrFail($id);
        
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|max:255',
        'prodi_id' => 'required',
        'angkatan_id' => 'required',
        'semester' => 'required',
        'tipe' => 'required',
        ]);

        
        $validated['status'] = "AKTIF";

        $kelas->update($validated);

        return redirect('/kelas')->with('success', 'Kelas ' . $kelas->nama . ' berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->status = "NONAKTIF";
        $kelas->update();

        return redirect('/kelas')->with('success', 'Kelas ' . $kelas->nama . ' berhasil dihapus!');
    }
}
