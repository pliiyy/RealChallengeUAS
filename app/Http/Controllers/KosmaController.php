<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Kosma;
use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class KosmaController extends Controller
{
    public function index(Request $request)
    {
        $query = Kosma::with(['mahasiswa.kelas','mahasiswa.user.biodata','kelas']);

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('biodata', function ($b) use ($request) {
                    $b->where('nama', 'like', '%'.$request->search.'%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","AKTIF");
        }

        // Pagination, misal 10 data per halaman
        $kosma = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $kosma->appends($request->all());
        
        $mahasiswa = Mahasiswa::where("status","AKTIF")->get();
        $kelas = Kelas::where("status","AKTIF")->get();

        return view('kosma', compact('kosma','mahasiswa','kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
        'mahasiswa_id' => 'required|string|max:255',
        'kelas_id' => 'required|string|max:255',
        ]);

        $validated["status"] = "AKTIF";
        Kosma::create($validated);
        $find = Mahasiswa::findOrFail($validated['mahasiswa_id']);
        $user = User::findOrFail($find->user_id);
        $roleDekan = Role::where('nama','kosma')->first();
        $user->role()->attach($roleDekan->id);
        return redirect('/kosma')->with('success', 'Kosma berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $fakultas = Kosma::findOrFail($id);
        
        $validated = $request->validate([
        'mahasiswa_id' => 'required|string|max:255',
        'kelas_id' => 'required|string|max:255',
        ]);

        
        $validated['status'] = "AKTIF";

        $fakultas->update($validated);

        return redirect('/kosma')->with('success', 'Kosma berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $d = Kosma::findOrFail($id);
        $role = Role::where('nama', 'kosma')->first();
        $d->user()->role()->detach($role->id);
        $dName = $d->user->biodata->nama;
        $d->status = "NONAKTIF";
        $d->update();

        return redirect('/kosma')->with('success', 'Kosma '.$dName.' berhasil dihapus!');
    }
}
