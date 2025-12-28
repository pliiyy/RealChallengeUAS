<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\Sekprodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SekprodiController extends Controller
{
    public function index(Request $request)
    {
        $query = Sekprodi::with(['user.biodata', 'prodi']);

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
        $sekprodi = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $sekprodi->appends($request->all());
        $fakultas = Prodi::where("status","=","AKTIF")->get();
        $users = User::whereDoesntHave('role', function ($query) {
            $query->where('nama', 'sekprodi');
        })->get();
        return view('sekprodi', compact('sekprodi','fakultas','users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => [
                'nullable', 
                Rule::exists('user', 'id'),
            ],
            'exs_periode_mulai' => ['nullable'],
            'exs_periode_selesai' => ['nullable'],
            'exs_fakultas_id' => ['nullable'],
            'periode_mulai' => ['nullable'],
            'periode_selesai' => ['nullable'],
            'fakultas_id' => ['nullable'],
            'user_name' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika user_id kosong
                'string', 
                'max:255',
            ],
            'user_email' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika user_id kosong
                'email', 
                'unique:user,email', // Pastikan email unik jika membuat baru
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
            $roleDekan = Role::where('nama','sekprodi')->first();
            $dUser->role()->attach($roleDekan->id);
            
            Sekprodi::create([
                    "user_id" => $dUser->id,
                    "periode_mulai" => empty($validatedData['user_id']) ? $validatedData['periode_mulai'] : $validatedData['exs_periode_mulai'],
                    "periode_selesai" => empty($validatedData['user_id'])?$validatedData['periode_selesai']:$validatedData['exs_periode_selesai'],
                    "prodi_id" => empty($validatedData['user_id'])?$validatedData['fakultas_id']:$validatedData['exs_fakultas_id'],
                ]);

            DB::commit();

            $message = 'User berhasil dtambahkan!';
            
            return redirect('/dosen')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error untuk debug
            
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. ' . $e->getMessage());
        }
        return redirect('/sekprodi')->with('success', 'Data berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $dekan = Sekprodi::findOrFail($id);
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
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'periode_mulai' => 'nullable',
            'periode_selesai' => 'nullable',
        ]);

        $dekan->update([
            'periode_mulai'=> $$request->periode_mulai ?? $dekan->periode_mulai,
            'periode_selesai'=> $$request->periode_selesai ?? $dekan->periode_selesai,
            'prodi_id'=> $$request->fakultas_id ?? $dekan->prodi_id,
        ]);

        // update password jika diisi
        if ($request->filled('password')) {
            $request['password'] = Hash::make($request->password);
        }else{
            $request['password'] = $user->password;
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

        return redirect('/sekprodi')->with('success', 'Sekprodi berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $roleDekan = Role::where('nama', 'sekprodi')->first();
        $dekan = Sekprodi::findOrFail($id);
        
        $dekan->user()->role()->detach($roleDekan->id);
        $dekan->status = "NONAKTIF";
        $dekan->update();

        return redirect('/sekprodi')->with('success', 'Sekprodi ' . $dekan->nama . ' berhasil dihapus!');
    }
}
