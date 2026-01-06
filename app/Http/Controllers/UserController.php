<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Pindah_jadwal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function dashboard(){
        $user = User::where("status","AKTIF")->count();
        $pindah = Pindah_jadwal::count();
        $total = $pindah;

        return view('dashboard',compact('user','total'));
    
    }
    public function editPassword(Request $request){
        $user = User::findOrFail(Auth::user()->id);
        
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'password_baru' => ['required', 'string'],
        'password_lama' => ['required', 'string', 'current_password'],
        ]);
        
        $user->update([
            "email" => $validated["email"],
            "password" => Hash::make($validated["password_baru"])
        ]);
        $bio = $user->biodata;
        $bio->update([
            "nama" => $validated['nama'],
        ]);

        return redirect('/settings')->with('success', 'Pengguna ' . $user->biodata->nama . ' berhasil diperbarui!');
    }
    public function editProfil(Request $request){
        $user = User::findOrFail(Auth::user()->id);
        
        $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'agama' => ['nullable', 'string'],
        'alamat' => ['nullable', 'string'],
        ]);
        
        $user->update([
            "email" => $validated["email"],
        ]);
        $bio = $user->biodata;
        $bio->update([
            "nama" => $validated['nama'],
            "agama" => $validated['agama'],
            "alamat" => $validated['alamat'],
        ]);

        return redirect()->back()->with('success', 'Pengguna ' . $user->biodata->nama . ' berhasil diperbarui!');
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Mengambil model Biodata terkait
        $biodata = auth()->user()->Biodata;

        // 1. Hapus foto lama jika ada
        if ($biodata->foto_profil) {
            // Menggunakan facade Storage untuk menghapus file dari disk 'public'
            // Jalur di dalam disk 'public' adalah 'foto_profil/{filename}'
            Storage::disk('public')->delete('foto_profil/' . $biodata->foto_profil);
        }

        // 2. Simpan foto baru
        $filename = time() . '.' . $request->foto->extension();
        
        // Menggunakan facade Storage untuk menyimpan file ke folder 'foto_profil'
        // di dalam disk 'public'. Ini konsisten dengan langkah penghapusan.
        $request->file('foto')->storeAs('foto_profil', $filename, 'public');

        // 3. Perbarui nama file di database
        $biodata->foto_profil = $filename;
        $biodata->save();

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function jadwal()
    {
       $jadwal = Jadwal::with(['pengampu_mk.matakuliah','shift'])->get();

        $data = $jadwal->map(function ($event) {
            $hariArray = [
                "Senin" => 1,
                "Selasa" => 2,
                "Rabu" => 3,
                "Kamis" => 4,
                "Jumat" => 5,
            ];

            $date = Carbon::now()->startOfWeek()->addDays($hariArray[$event->hari] - 1);

            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $event->shift->jam_mulai);
            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $event->shift->jam_selesai);

            return [
                'id' => $event->id,
                'title' => $event->pengampu_mk->matakuliah->nama,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
            ];
        });

        return response()->json($data);
    }
}
