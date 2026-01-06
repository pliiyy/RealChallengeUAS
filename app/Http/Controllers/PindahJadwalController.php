<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Pindah_jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PindahJadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Pindah_jadwal::with(['jadwal_asal','jadwal_tujuan','shift','ruangan']);

        if ($request->filled('status')) {
            $query->where('status_jadwal', $request->status);
        }

        $pindah_jadwal = $query->orderBy('id', 'desc')->paginate(10);

        $pindah_jadwal->appends($request->all());
        
        return view('pindahjadwal', compact('pindah_jadwal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'shift_id' => 'nullable|exists:shift,id',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'jadwal_asal_id' => 'required|exists:jadwal,id',
            'jadwal_tujuan_id' => 'nullable|exists:jadwal,id',
            'status_jadwal' => 'required',
        ]);
        $conflict = Jadwal::where('hari', $request->hari)
        ->where('shift_id', $request->shift_id)
        ->where('ruangan_id', $request->ruangan_id)
        ->exists();
        if ($conflict) {
            return redirect()->back()->with('error', 'Gagal pindah jadwal karena bentrok dengan jadwal lain!');
        }
        $validated["status"] = "ANTRI";

        Pindah_jadwal::create($validated);
        return redirect()->back()->with('success', 'Permohonan pindah jadwal berhasil diajukan. Tunggu kosma melakukan approve!');
    }

    public function update(Request $request, $id)
    {
         $pindahJadwal = Pindah_jadwal::findOrFail($id);
        
        $validated = $request->validate([
            'id' => 'required|string',
            'shift_id' => 'nullable|string',
            'ruangan_id' => 'nullable|string',
            'hari' => 'nullable|string',
            'status' => 'required|string',
        ]);
        if($validated['status']==="APPROVED"){
            try{
                DB::beginTransaction();
                $temp = $pindahJadwal->jadwal_asal->replicate();
                if($pindahJadwal->jadwal_tujuan_id){
                    $jadwal = $pindahJadwal->jadwal_asal;
                    $jadwal->shift_id = $pindahJadwal->jadwal_tujuan->shift_id;
                    $jadwal->ruangan_id = $pindahJadwal->jadwal_tujuan->ruangan_id;
                    $jadwal->hari = $pindahJadwal->jadwal_tujuan->hari;
                    $jadwal->save();
                    $jadwalTujuan = $pindahJadwal->jadwal_tujuan;
                    $jadwalTujuan->shift_id = $temp->shift_id;
                    $jadwalTujuan->ruangan_id = $temp->ruangan_id;
                    $jadwalTujuan->hari = $temp->hari;
                    $jadwalTujuan->save();
                }else{
                    $jadwal = $pindahJadwal->jadwal_asal;
                    $jadwal->shift_id = $validated['shift_id'];
                    $jadwal->ruangan_id = $validated['ruangan_id'];
                    $jadwal->hari = $validated['hari'];
                    $jadwal->save();
                }
                $pindahJadwal->update([
                    'status'=> "APPROVED",
                    'kosma_id' => Auth::user()->mahasiswa?->kosma?->id
                ]);
                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
                return redirect()->back()->with('error', 'Jadwal  gagal diperbarui!');
            }
        }else{
            $validated["kosma_id"] = Auth::user()->kosma->id;
            $pindahJadwal->update($validated);
            $pindahJadwal->update($validated);
        }
    

        return redirect()->back()->with('success', 'Jadwal  berhasil diperbarui!');
    }
}
