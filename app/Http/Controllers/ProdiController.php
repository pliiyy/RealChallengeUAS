<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prodi::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%')->orWhere("kode", 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }
        if ($request->filled('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        // Pagination, misal 10 data per halaman
        $prodi = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $prodi->appends($request->all());
        $fakultas = Fakultas::where('status',"AKTIF")->get();

        return view('prodi', compact('prodi','fakultas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|unique:prodi,kode',
        'fakultas_id' => 'required',
        'jenjang' => 'required',
        ]);
        $validated["status"] = "AKTIF";
        Prodi::create($validated);
        return redirect('/prodi')->with('success', 'Program Studi '. $validated["nama"] .' berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $prodi = Prodi::findOrFail($id);
        
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'kode' => 'required|string|unique:prodi,kode,'.$id,
        'fakultas_id' => 'required',
        'jenjang' => 'required',
        ]);
        
        $validated['status'] = "AKTIF";

        $prodi->update($validated);

        return redirect('/prodi')->with('success', 'Program Studi ' . $prodi->nama . ' berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $prodi = Prodi::findOrFail($id);
        $prodi->status = "NONAKTIF";
        $prodi->update();

        return redirect('/prodi')->with('success', 'Program Studi ' . $prodi->nama . ' berhasil dihapus!');
    }
}
