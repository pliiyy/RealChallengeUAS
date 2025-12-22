<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['biodata','role','dekan.fakultas','dosen','kaprodi.prodi','sekprodi.prodi','mahasiswa.kosma.kelas']);

        if ($request->filled('search')) {
            $query->where('biodata.nama', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%')->orWhere('semester', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('role_id')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('role.id', $request->role_id);
            });
        }

        // Pagination, misal 10 data per halaman
        $user = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $user->appends($request->all());
        $fakultas = Fakultas::where("status",'AKTIF')->get();
        $prodi = Prodi::where("status",'AKTIF')->get();
        $kelas = Kelas::where("status",'AKTIF')->get();
        $role = Role::all();

        return view('users', compact('user','fakultas','prodi','kelas','role'));
    }
}
