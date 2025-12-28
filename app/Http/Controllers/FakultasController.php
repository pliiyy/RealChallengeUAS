<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    public function index(Request $request)
    {
        $query = Fakultas::with(["dekan",'prodi']);

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }

        // Pagination, misal 10 data per halaman
        $fakultas = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $fakultas->appends($request->all());

        return view('fakultas', compact('fakultas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        ]);

        $validated["status"] = "AKTIF";
        Fakultas::create($validated);
        return redirect('/fakultas')->with('success', 'Fakultas '. $validated["nama"] .' berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $fakultas = Fakultas::findOrFail($id);
        
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        ]);

        
        $validated['status'] = "AKTIF";

        $fakultas->update($validated);

        return redirect('/fakultas')->with('success', 'Fakultas ' . $fakultas->nama . ' berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $fakultas = Fakultas::findOrFail($id);
        $fakultas->status = "NONAKTIF";
        $fakultas->update();

        return redirect('/fakultas')->with('success', 'Fakultas ' . $fakultas->nama . ' berhasil dihapus!');
    }

    
}
