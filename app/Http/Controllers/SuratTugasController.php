<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Matakuliah;
use App\Models\Pengampu_mk;
use App\Models\Semester;
use App\Models\Surat_tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuratTugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat_tugas::with('dekan.user.biodata','dosen.user.biodata','semester','pengampu_mk.kelas');

        if ($request->filled('search')) {
            $query->where('nomor_sk', 'like', '%'.$request->search.'%')->orWhere("nomor_surat", 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Pagination, misal 10 data per halaman
        $surat = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $surat->appends($request->all());
        $dosen = Dosen::with(['user.biodata'])->get();
        $matakuliah = Matakuliah::where("status",'AKTIF')->get();
        $semester = Semester::where("status",'AKTIF')->get();
        $kelas = Kelas::where("status",'AKTIF')->get();

        return view('surat', compact('surat','dosen','matakuliah','semester','kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'nomor_surat' => 'required|string|max:255',
        'nomor_sk' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'semester_id' => 'required|string|max:255',
        'pengampu' => 'required|array',
        'pengampu.*.matakuliah_id' => 'required|string',
        'pengampu.*.kelas_id' => 'required|string',
        'pengampu.*.sks' => 'required',
        ]);

        $validated["status"] = "AKTIF";
        
        DB::transaction(function () use ($request) {
            $savedSurat = Surat_tugas::create([
                'tanggal' => $request->tanggal,
                'nomor_sk' => $request->nomor_sk,
                'nomor_surat' => $request->nomor_surat,
                'semester_id' => $request->semester_id,
                'dekan_id' => Auth::user()->dekan?->id,
                'dosen_id' => $request->dosen_id
            ]);
            foreach ($request->pengampu as $pengampu) {
                $pengampu['surat_tugas_id'] = $savedSurat->id;
                Pengampu_mk::create($pengampu);
            }
        });

        return redirect('/surat')->with('success', 'Surat Tugas Mengajar '. $validated["nomor_surat"] .' berhasil ditambahkan!');
    }
    public function update(Request $request,  $id)
    {
    }
    public function destroy( $id)
    {
        $surat = Surat_tugas::findOrFail($id);
        $surat->destroy();

        return redirect('/surat')->with('success', 'Surat Tugas Mengajat ' . $surat->nomor_surat . ' berhasil dihapus!');
    }
}
