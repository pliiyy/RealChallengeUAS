<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Jadwal;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Models\Shift;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        // Ambil fakultas dan semester yang dipilih
        $fakultas = Fakultas::with(['prodi.kelas'])->where("status", "AKTIF")->get();
        $selectedFakultas = $request->filled('fakultas_id') ? $fakultas->findOrFail($request->fakultas_id) : $fakultas->first();
        $semester = Semester::where("status", "AKTIF")->get();
        $selectedSemester = $request->filled('semester_id')
            ? $semester->findOrFail($request->semester_id)
            : $semester->first();

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Query untuk shift dengan jadwal yang relevan
        $shift = Shift::orderBy('jam_mulai')->with(['jadwal.pengampu_mk.surat_tugas.semester', 'jadwal.pengampu_mk.kelas','jadwal.pengampu_mk.matakuliah','jadwal.pengampu_mk.surat_tugas.dosen','jadwal.ruangan'])->get();

        $ruangan = Ruangan::where("status", "AKTIF")->get();
        $jadwal = Jadwal::with([
            'pengampu_mk.surat_tugas',
            'pengampu_mk.matakuliah.prodi'
        ])
        ->whereHas('pengampu_mk', function ($q) use ($selectedFakultas, $selectedSemester) {
            $q->whereHas('surat_tugas', function ($q) use ($selectedSemester) {
                $q->where('dosen_id', Auth::user()->dosen->id)
                ->where('semester_id', $selectedSemester->id);
            })
            ->whereHas('matakuliah.prodi', function ($q) use ($selectedFakultas) {
                $q->where('fakultas_id', $selectedFakultas->id);
            });
        })
        ->get();

        return view('jadwal', compact(
            'fakultas', 
            'selectedFakultas', 
            'semester', 
            'selectedSemester', 
            'shift', 
            'hari', 
            'ruangan','jadwal'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pengampu_mk_id' => 'required|exists:pengampu_mk,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'shift_id' => 'required|exists:shift,id',
            'ruangan_id' => 'required|exists:ruangan,id',
        ]);

        // Cek konflik jadwal lebih komprehensif
        $conflict = Jadwal::where('hari', $request->hari)
            ->where('shift_id', $request->shift_id)
            ->where('ruangan_id', $request->ruangan_id)
            ->whereHas('pengampu_mk.surat_tugas', function ($q) use ($validated) {
                $q->where('semester_id', function ($sub) use ($validated) {
                    $sub->select('semester_id')
                        ->from('pengampu_mk')
                        ->where('id', $validated['pengampu_mk_id'])
                        ->limit(1);
                });
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()->with('error', 'Jadwal tidak dapat ditambahkan karena bentrok dengan jadwal lain!');
        }

        Jadwal::create($validated);

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function download(Request $request)
    {
        // Ambil fakultas dan semester yang dipilih
        $fakultas = Fakultas::with(['prodi.kelas'])->where("status", "AKTIF")->get();
        $selectedFakultas = $request->filled('fakultas_id') ? $fakultas->findOrFail($request->fakultas_id) : $fakultas->first();
        $semester = Semester::where("status", "AKTIF")->get();
        $selectedSemester = $request->filled('semester_id')
            ? $semester->findOrFail($request->semester_id)
            : $semester->first();

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $kelasFilter = $request->kelas ?? "R";

        // Query untuk shift dengan jadwal yang relevan
        $shift = Shift::orderBy('jam_mulai')->with(['jadwal.pengampu_mk.surat_tugas.semester', 'jadwal.pengampu_mk.kelas','jadwal.pengampu_mk.matakuliah','jadwal.pengampu_mk.surat_tugas.dosen','jadwal.ruangan'])->get();

        $ruangan = Ruangan::where("status", "AKTIF")->get();

        $pdf = Pdf::loadView('exportjadwal', compact(
            'fakultas', 
            'selectedFakultas', 
            'semester', 
            'selectedSemester', 
            'shift', 
            'hari', 
            'ruangan'
        ))->setOption('isHtml5ParserEnabled', true);
        return $pdf->download('jadwal.pdf');
    }
    public function ruang(Request $request)
    {
        // Ambil fakultas dan semester yang dipilih
        $fakultas = Fakultas::with(['prodi.kelas'])->where("status", "AKTIF")->get();
        $selectedFakultas = $request->filled('fakultas_id') ? $fakultas->findOrFail($request->fakultas_id) : $fakultas->first();
        $semester = Semester::where("status", "AKTIF")->get();
        $selectedSemester = $request->filled('semester_id')
            ? $semester->findOrFail($request->semester_id)
            : $semester->first();


        // Query untuk shift dengan jadwal yang relevan
        $shift = Shift::orderBy('jam_mulai')->with(['jadwal.pengampu_mk.surat_tugas.semester', 'jadwal.pengampu_mk.kelas','jadwal.pengampu_mk.matakuliah','jadwal.pengampu_mk.surat_tugas.dosen','jadwal.ruangan'])->get();

        $ruangan = Ruangan::where("status", "AKTIF")->get();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        return view('ruang', compact(
            'fakultas', 
            'selectedFakultas', 
            'semester', 
            'selectedSemester', 
            'shift', 
            'ruangan',
            'hari'
        ));
    }
    public function ruangDownload(Request $request)
    {
        // Ambil fakultas dan semester yang dipilih
        $fakultas = Fakultas::with(['prodi.kelas'])->where("status", "AKTIF")->get();
        $selectedFakultas = $request->filled('fakultas_id') ? $fakultas->findOrFail($request->fakultas_id) : $fakultas->first();
        $semester = Semester::where("status", "AKTIF")->get();
        $selectedSemester = $request->filled('semester_id')
            ? $semester->findOrFail($request->semester_id)
            : $semester->first();


        // Query untuk shift dengan jadwal yang relevan
        $shift = Shift::orderBy('jam_mulai')->with(['jadwal.pengampu_mk.surat_tugas.semester', 'jadwal.pengampu_mk.kelas','jadwal.pengampu_mk.matakuliah','jadwal.pengampu_mk.surat_tugas.dosen','jadwal.ruangan'])->get();

        $ruangan = Ruangan::where("status", "AKTIF")->get();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $pdf = Pdf::loadView('exportruang', compact(
            'fakultas', 
            'selectedFakultas', 
            'semester', 
            'selectedSemester', 
            'shift', 
            'ruangan',
            'hari'
        ))->setOption('isHtml5ParserEnabled', true);
        return $pdf->download('jadwalruang.pdf');
    }
}