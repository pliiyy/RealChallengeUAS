<?php

namespace App\Http\Controllers;

use App\Models\Surat_tugas;
use App\Models\User;
use Illuminate\Http\Request;

class SuratTugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Surat_tugas::with('dekan.user.biodata','dosen.user.biodata');

        if ($request->filled('search')) {
            $query->where('nomor_sk', 'like', '%'.$request->search.'%')->orWhere("nomor_surat", 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where("status","!=","AKTIF");
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Pagination, misal 10 data per halaman
        $surat = $query->orderBy('id', 'desc')->paginate(10);

        // Biar query string tetap terbawa saat paginate link
        $surat->appends($request->all());
        $user = User::with(['biodata'])
                ->whereHas('role', function ($query) {
                        $query->where('nama', 'dekan')->orWhere("nama","dosen"); // Ganti 'name' dengan nama kolom role di tabel Anda
                })
                ->get();

        return view('surat', compact('surat','user'));
    }
}
