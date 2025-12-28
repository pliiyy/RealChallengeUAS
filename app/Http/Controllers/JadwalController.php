<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Pengampu_mk;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $fakultas = Fakultas::where("status","AKTIF")->get();
        $selectedFakultas = $request->filled('fakultas_id')
        ? Fakultas::findOrFail($request->fakultas_id)
        : Fakultas::where('status','AKTIF')->firstOrFail();
        $selectedSemester = $request->filled('semester_id')
        ? Semester::findOrFail($request->semester_id)
        : Semester::where('status','AKTIF')->firstOrFail();

        $hari = ['Senin', 'Selasa','Rabu','Kamis','Jumat'];

        $shift = Shift::orderBy('jam_mulai')
        ->with(['jadwal' => function ($q) use ($selectedSemester, $request) {
            $q->whereHas('pengampu_mk.surat_tugas', function ($q2) use ($selectedSemester) {
                $q2->where('semester_id', $selectedSemester->id);
            })
            ->when($request->kelas, function ($q) use ($request) {
                $q->whereHas('pengampu_mk.kelas', function ($q2) use ($request) {
                    $q2->where('tipe', $request->kelas ?? "R");
                });
            })
            ->with([
                'ruangan',
                'pengampu_mk.kelas.prodi',
                'pengampu_mk.matakuliah',
                'pengampu_mk.surat_tugas.dosen',
                'pengampu_mk.surat_tugas.semester'
            ]);
        }])
        ->get();

        $kelas = Kelas::where('semester_id',$selectedSemester->id)
        ->whereHas('prodi.fakultas', function ($q) use ($selectedFakultas){
                $q->where('id', $selectedFakultas->id);
            })->get();
        
        $dosenId = optional(Auth::user())->dosen_id;

        $pengampu = Pengampu_mk::with(['matakuliah.prodi','kelas','jadwal'])->whereHas('surat_tugas', function ($q) use ($selectedSemester, $dosenId) {
            $q->where('semester_id', $selectedSemester->id)
            ->where('status', 'APPROVED')
            ->when($dosenId, function ($q2) use ($dosenId) {
                $q2->where('dosen_id', $dosenId);
            });
        })->whereHas('kelas', function ($q) use ($request) {
            $q->where('tipe', $request->kelas ?? "R");
        })
        ->whereDoesntHave('jadwal')
        ->get();

        $ruangan = Ruangan::where("status","AKTIF")->get();


        return view('jadwal', compact('fakultas','selectedFakultas','selectedSemester','kelas','shift','hari','pengampu','ruangan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'pengampu_mk_id' => 'required',
        'hari' => 'required',
        'shift_id' => 'required',
        'ruangan_id' => 'required',
        ]);

        Jadwal::create($validated);

        return redirect()->back()->with('success','Jadwal berhasil ditambahkan');
    }
}
