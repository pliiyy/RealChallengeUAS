<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $query = Semester::query();

        if ($request->filled('search')) {
            $query->where('tahun_akademik', 'like', '%'.$request->search.'%')->orwhere('nama', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Pagination, misal 10 data per halaman
        $semester = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $semester->appends($request->all());

        return view('semester', compact('semester'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'jenis' => 'required|string|max:255',
        'semester' => 'required',
        'tahun_akademik' => 'required',
        'tanggal_mulai' => 'required',
        'tanggal_selesai' => 'required',
        ]);
        $validated["status"] = "AKTIF";
        Semester::create($validated);
        return redirect('/semester')->with('success', 'Semester '. $validated["jenis"] .' berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $semester = Semester::findOrFail($id);
        
        $validated = $request->validate([
        'jenis' => 'required|string|max:255',
        'semester' => 'required',
        'tahun_akademik' => 'required',
        'tanggal_mulai' => 'required',
        'tanggal_selesai' => 'required',
        ]);

        
        $validated['status'] = "AKTIF";

        $semester->update($validated);

        return redirect('/semester')->with('success', 'Semester ' . $semester->jenis . ' berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $semester = Semester::findOrFail($id);
        $semester->status = "NONAKTIF";
        $semester->update();

        return redirect('/semester')->with('success', 'Semester ' . $semester->jenis . ' berhasil dihapus!');
    }
}
