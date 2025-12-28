<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use App\Models\Dosen;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::with(['user.biodata']);

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
        $dosen = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $dosen->appends($request->all());
        $users = User::whereDoesntHave('role', function ($query) {
            $query->where('nama', 'dosen');
        })->get();
        return view('dosen', compact('dosen','users'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => [
                'nullable', 
                Rule::exists('user', 'id'),
            ],
            'nidn' => ['required','string', 'unique:dosen,nidn'],
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
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string',
            ],
            'agama' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string', 
            ],
            'tempat_lahir' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string', 
            ],
            'tanggal_lahir' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string', 
            ],
            'alamat' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string', 
            ],
            'kelurahan' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string', 
            ],
            'kec_id' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string', 
            ],
            'kab_id' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
                'string', 
            ],
            'prov_id' => [
                Rule::requiredIf(is_null($request->user_id)), // Wajib jika membuat baru
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
            $roleDekan = Role::where('nama','dosen')->first();
            $dUser->role()->attach($roleDekan->id);
            
            Dosen::create([
                    "user_id" => $dUser->id,
                    "nidn" => $validatedData['nidn'],
                ]);

            DB::commit();

            $message = 'User Dosen berhasil dtambahkan!';
            
            return redirect('/dosen')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error untuk debug
            
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data Dosen. ' . $e->getMessage());
        }
        return redirect('/dosen')->with('success', 'Dosen berhasil ditambahkan!');
    }

    public function update(Request $request,  $id)
    {
        $d = Dosen::with(['user.biodata'])->findOrFail($id);
        
        $validated = $request->validate([
        'user_name' => ['string', 'max:255'],
        'user_email' => ['email', 'unique:user,email,'.$d->user->id],
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
        ]);

        $user = User::findOrFail($d->user->id);
        $d->update([
            'nidn' => $request->nidn ?? $d->nidn
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
    ]);

        return redirect('/dosen')->with('success', 'Dosen berhasil diperbarui!');
    }

    public function destroy( $id)
    {
        $d = Dosen::findOrFail($id);
        $role = Role::where('nama', 'dosen')->first();
        $d->user()->role()->detach($role->id);
        $dName = $d->user->biodata->nama;
        $d->status = "NONAKTIF";
        $d->update();

        return redirect('/dosen')->with('success', 'Dosen '.$dName.' berhasil dihapus!');
    }
}
