<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use App\Models\Dekan;
use App\Models\Fakultas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DekanController extends Controller
{
    public function index(Request $request)
    {
        $query = Dekan::with(['user.biodata', 'fakultas']);

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
        $dekan = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $dekan->appends($request->all());
        $fakultas = Fakultas::where("status","=","AKTIF")->get();
        return view('dekan', compact('dekan','fakultas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required',
            'jenis_kelamin'        => 'nullable',
            'agama'        => 'nullable',
            'tempat_lahir'        => 'nullable',
            'tanggal_lahir'        => 'nullable',
            'alamat'        => 'nullable',
            'kelurahan'        => 'nullable',
            'kec_id'        => 'nullable',
            'kab_id'        => 'nullable',
            'prov_id'        => 'nullable',
            'kodepos'        => 'nullable',
            'email'       => 'required|email|unique:user,email',
            'password'    => 'required',
            'fakultas_id' => 'required|exists:fakultas,id',
            'foto_profil'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        DB::transaction(function () use ($request) {

            // 1. CREATE USER
            $user = User::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. ATTACH ROLE DEKAN
            $roleDekan = Role::where('nama','dekan')->first();
            $user->roles()->attach($roleDekan->id);

            // 3. CREATE DEKAN
            Dekan::create([
                'user_id'    => $user->id,
                'fakultas_id'=> $request->fakultas_id,
            ]);

            $photoPath = null;
            if ($request->hasFile('foto_profil')) {
                $photoPath = $request->file('foto_profil')
                    ->store('profil','public');
            }

            Biodata::create(([
                'nama' => $request->nama,
                'jenis_kelamin'        => $request->jenis_kelamin,
                'agama'        => $request->agama,
                'tempat_lahir'        => $request->tempat_lahir,
                'tanggal_lahir'        => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'kelurahan'        => $request->kelurahan,
                'kec_id'        => $request->kec_id,
                'kab_id'        => $request->kab_id,
                'prov_id'        => $request->prov_id,
                'kodepos'        => $request->kodepos,
                'foto_profil'       => $photoPath,
            ]));
        });

        return redirect('/dekan')->with('success','Dekan berhasil dibuat');
    }

    public function update(Request $request, $id)
    {
        $dekan = Dekan::findOrFail($id);
        $user = $dekan->user;
        $biodata = $dekan->user->biodata;

        $request->validate([
            'nama'        => 'required',
            'jenis_kelamin'        => 'nullable',
            'agama'        => 'nullable',
            'tempat_lahir'        => 'nullable',
            'tanggal_lahir'        => 'nullable',
            'alamat'        => 'nullable',
            'kelurahan'        => 'nullable',
            'kec_id'        => 'nullable',
            'kab_id'        => 'nullable',
            'prov_id'        => 'nullable',
            'kodepos'        => 'nullable',
            'email'       => 'required|email|unique:user,email,'.$id,
            'password'    => 'nullable',
            'fakultas_id' => 'required|exists:fakultas,id',
            'foto_profil'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // update password jika diisi
        if ($request->filled('password')) {
            $request['password'] = Hash::make($request->password);
        }else{
            $request['password'] = $user->password;
        }
        if ($request->hasFile('foto_profil')) {
            // hapus foto lama
            if ($biodata->foto_profil && Storage::disk('public')->exists($biodata->foto_profil)) {
                Storage::disk('public')->delete($biodata->foto_profil);
            }

            $photoPath = $request->file('foto_profil')
                ->store('profil', 'public');

            $biodata->foto_profil = $photoPath;
        }
        $user->email = $request->email;
        $user->password = $request->password;
        $user->update();

        $biodata->jenis_kelamin = $request->jenis_kelamin;
        $biodata->agama = $request->agama;
        $biodata->tempat_lahir = $request->tempat_lahir;
        $biodata->tanggal_lahir = $request->tanggal_lahir;
        $biodata->alamat = $request->alamat;
        $biodata->kelurahan = $request->kelurahan;
        $biodata->kec_id = $request->kec_id;
        $biodata->kab_id = $request->kab_id;
        $biodata->prov_id = $request->prov_id;
        $biodata->kodepos = $request->kodepos;
        $biodata->update();

        $dekan->fakultas_id = $request->fakultas_id;
        $dekan->update();
    }

    public function destroy( $id)
    {
        $dekan = Dekan::findOrFail($id);
        $dekan->status = "NONAKTIF";
        $dekan->update();

        return redirect('/dekan')->with('success', 'dekan ' . $dekan->nama . ' berhasil dihapus!');
    }
}
