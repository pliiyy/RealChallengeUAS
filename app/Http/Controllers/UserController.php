<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function dashboard(){
        $user = User::where("status","AKTIF")->count();
        $pindah = 0;
        $barter = 0;
        $total = $pindah+$barter;

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
