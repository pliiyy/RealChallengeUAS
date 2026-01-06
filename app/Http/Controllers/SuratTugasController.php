<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Matakuliah;
use App\Models\Semester;
use App\Models\Surat_tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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
        }else{
            $query->where('status', "!=", "NONAKTIF");
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
            'semester_id' => 'required',
            'pengampu' => 'required|array',
            'pengampu.*.matakuliah_id' => 'required',
            'pengampu.*.kelas' => 'required|array',
            'pengampu.*.sks' => 'required',
        ]);

        DB::transaction(function () use ($request) {

            $savedSurat = Surat_tugas::create([
                'tanggal' => $request->tanggal,
                'nomor_sk' => $request->nomor_sk,
                'nomor_surat' => $request->nomor_surat,
                'semester_id' => $request->semester_id,
                'dekan_id' => Auth::user()->dekan?->id,
                'dosen_id' => $request->dosen_id,
                'status' => 'AKTIF',
            ]);

            foreach ($request->pengampu as $data) {

                // 1️⃣ Buat pengampu MK
                $pengampuMk = $savedSurat->pengampu_mk()->create([
                    'matakuliah_id' => $data['matakuliah_id'],
                    'sks' => $data['sks'],
                ]);

                // 2️⃣ Attach kelas ke pengampu MK
                $pengampuMk->kelas()->sync($data['kelas']);
            }
        });

        return redirect('/surat')
            ->with('success', 'Surat Tugas Mengajar ' . $validated['nomor_surat'] . ' berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $surat = Surat_tugas::findOrFail($id);

        $validated = $request->validate([
            'nomor_surat' => 'nullable|string|max:255',
            'nomor_sk' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'semester_id' => 'nullable',
            'pengampu' => 'nullable|array',
            'pengampu.*.matakuliah_id' => 'required',
            'pengampu.*.kelas' => 'nullable|array',
            'pengampu.*.sks' => 'required',
            'status' => 'nullable',
            'file' => 'nullable|file',
        ]);
        if ($request->hasFile('file')) {
            if ($surat->file && Storage::disk('public')->exists($surat->file)) {
                Storage::disk('public')->delete($surat->file);
            }

            $validated['file'] = $request->file('file')->store('profil', 'public');
        }

        $surat->update($validated);

        if ($request->has('pengampu')) {

            $surat->pengampu_mk()->delete();

            foreach ($request->pengampu as $data) {

                $pengampu = $surat->pengampu_mk()->create([
                    'matakuliah_id' => $data['matakuliah_id'],
                    'sks' => $data['sks'],
                ]);

                if (!empty($data['kelas'])) {
                    $pengampu->kelas()->sync($data['kelas']);
                }
            }
        }

        return redirect('/surat')->with('success', 'Surat tugas berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $surat = Surat_tugas::findOrFail($id);
        $surat->status = "NONAKTIF";
        $surat->update();

        return redirect('/surat')->with('success', 'Surat Tugas Mengajat ' . $surat->nomor_surat . ' berhasil dihapus!');
    }

    public function generateSurat(Request $request)
    {
        $suratId = $request->query('id');

        $suratTugas = Surat_tugas::with(['dosen.user.biodata','dekan.fakultas','dekan.user.biodata', 'pengampu_mk.kelas','pengampu_mk.matakuliah.prodi','semester'])
                                ->findOrFail($suratId); 

        $data = [
            'surat' => $suratTugas,
        ];

        $pdf = Pdf::loadView('pdf_surat', $data);
        
        return $pdf->stream('laporan_anda.pdf'); 
        
    }

    public function viewSurat(Request $request)
    {
        $id = $request->query('id');
        $doc = Surat_tugas::findOrFail($id);

        if (!$doc->file ||!Storage::disk('public')->exists($doc->file)) {
            abort(404, 'File not found');
        }

        $file = Storage::disk('public')->path($doc->file);

        return response()->file($file, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($file).'"'
        ]);
    }
}
