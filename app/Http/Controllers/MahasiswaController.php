<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Models\Biodata;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['user.biodata']);

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
        $mahasiswa = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $mahasiswa->appends($request->all());
        $users = User::whereDoesntHave('role', function ($query) {
            $query->where('nama', 'mahasiswa');
        })->get();
        $fakultas = Kelas::where("status","AKTIF")->get();
        $angkatan = Angkatan::where("status","AKTIF")->get();

        return view('mahasiswa', compact('mahasiswa','users','fakultas', 'angkatan'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => [
                'nullable', 
                Rule::exists('user', 'id'),
            ],
            'exs_fakultas_id' => ['nullable'],
            'fakultas_id' => ['nullable'],
            'ext_nidn' => ['nullable'],
            'nidn' => ['nullable'],
            'ext_angkatan_id' => ['nullable'],
            'ext_kelas_id' => ['nullable'],
            'angkatan_id' => ['nullable'],
            'kelas_id' => ['nullable'],
            'user_name' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika user_id kosong
                'string', 
                'max:255',
            ],
            'user_email' => [
                'nullable', // Pastikan email unik jika membuat baru
            ],
            'user_password' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string', 
                'min:6',
            ],
            'jenis_kelamin' => [
                "nullable", // Wajib jika membuat baru
                'string',
            ],
            'agama' => [
                "nullable", // Wajib jika membuat baru
                'string', 
            ],
            'tempat_lahir' => [
                "nullable", // Wajib jika membuat baru
                'string', 
            ],
            'tanggal_lahir' => [
                "nullable", // Wajib jika membuat baru
                'string', 
            ],
            'alamat' => [
                "nullable", // Wajib jika membuat baru
                'string', 
            ],
            'kelurahan' => [
                "nullable", // Wajib jika membuat baru
                'string', 
            ],
            'kec_id' => [
                "nullable", // Wajib jika membuat baru
                'string', 
            ],
            'kab_id' => [
                "nullable", // Wajib jika membuat baru
                'string', 
            ],
            'prov_id' => [
                "nullable", // Wajib jika membuat baru
                'string', 
            ],
            
        ]);
        // Kita gunakan Transaction untuk memastikan semua operasi berhasil atau gagal bersamaan
        DB::beginTransaction();

        try {
            $dUser = null;
            
            // ======================================================
            // A. FASE 1: GET OR CREATE USER
            // ======================================================
            if (!empty($validatedData['user_id'])) {
                // Kasus 1: User sudah ada dan dipilih
                $dUser = User::find($validatedData['user_id']);
            } else {
                // Kasus 2: Membuat User baru (data user_name, email, password harus ada)
                $dUser = User::create([
                    'email' => $validatedData['user_email'],
                    'password' => bcrypt($validatedData['user_password']),
                    'status'=>'AKTIF'
                    // Tambahkan field user lain yang relevan (misal: role)
                ]);
                Biodata::create([
                    'nama' => $validatedData['user_name'],
                    'jenis_kelamin' => $validatedData['jenis_kelamin'],
                    'tempat_lahir' => $validatedData['tempat_lahir'],
                    'tanggal_lahir' => $validatedData['tanggal_lahir'],
                    'agama' => $validatedData['agama'],
                    'alamat' => $validatedData['alamat'],
                    'kelurahan' => $validatedData['kelurahan'],
                    'kec_id' => $validatedData['kec_id'],
                    'kab_id' => $validatedData['kab_id'],
                    'prov_id' => $validatedData['prov_id'],
                    'user_id' => $dUser->id,
                ]);
                
            }

            if (!$dUser) {
                throw new \Exception("Gagal menemukan atau membuat data User.");
            }
            $roleDekan = Role::where('nama','mahasiswa')->first();
            $dUser->role()->attach($roleDekan->id);
            
            Mahasiswa::create([
                    "user_id" => $dUser->id,
                    "nim" => $validatedData['ext_nidn'] ?? $validatedData["nidn"],
                    "kelas_id" => $validatedData['fakultas_id'],
                    "angkatan_id" => $validatedData['angkatan_id'],
                ]);

            DB::commit();

            $message = 'User Mahasiswa berhasil dtambahkan!';
            
            return redirect('/mahasiswa')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error untuk debug
            
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data Dosen. ' . $e->getMessage());
        }
        return redirect('/mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $d = Mahasiswa::with(['user.biodata'])->findOrFail($id);
        
        $validated = $request->validate([
        'user_name' => ['string', 'max:255'],
        'user_email' => ['email', 'unique:user,email,'.$d->user->id],
        'nidn' => ["nullable"],
        'jenis_kelamin' => ['nullable','string', 'max:1'],
        'agama' => ['nullable','string'],
        'tempat_lahir' => ['nullable','string'],
        'tanggal_lahir' => ['nullable','string'],
        'alamat' => ['nullable','string'],
        'kelurahan' => ['nullable','string'],
        'kec_id' => ['nullable','string'],
        'kab_id' => ['nullable','string'],
        'prov_id' => ['nullable','string'],
        'nidn' => ['string', 'unique:dosen,nidn,'.$id],
        'angkatan_id' => ['string'],
        'fakultas_id' => ['string'],
        ]);

        $user = User::findOrFail($d->user->id);
        $d->update([
            'nim' => $request->nidn ?? $d->nidn,
            'angkatan_id' => $request->angkatan_id ?? $d->angkatan_id,
            'kelas_id' => $request->fakultas_id ?? $d->kelas_id,
        ]);

        $user->update([
            'email' => $validated['user_email'],
            'status'=>'AKTIF'
        ]);
        $user->biodata()->update([
        'nama' => $validated['user_name'] ?? null,
        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
        'tempat_lahir' => $validated['tempat_lahir'] ?? null,
        'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
        'agama' => $validated['agama'] ?? null,
        'alamat' => $validated['alamat'] ?? null,
        'kelurahan' => $validated['kelurahan'] ?? null,
        'kec_id' => $validated['kec_id'] ?? null,
        'kab_id' => $validated['kab_id'] ?? null,
        'prov_id' => $validated['prov_id'] ?? null,
        'kelas_id' => $validated['kelas_id'] ?? null,
        'angkatan_id' => $validated['angkatan_id'] ?? null,
    ]);

        return redirect('/mahasiswa')->with('success', 'Mahasiswa berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $d = Mahasiswa::findOrFail($id);
        $role = Role::where('nama', 'mahasiswa')->first();
        $d->user()->role()->detach($role->id);
        $dName = $d->user->biodata->nama;
        $d->status = "NONAKTIF";
        $d->update();

        return redirect('/mahasiswa')->with('success', 'Mahasiswa '.$dName.' berhasil dihapus!');
    }
}
