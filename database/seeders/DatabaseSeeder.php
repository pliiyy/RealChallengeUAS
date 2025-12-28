<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use App\Models\Biodata;
use App\Models\Dekan;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Matakuliah;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Role::insert([
            ['nama'=>'dosen'],
            ['nama'=>'dekan'],
            ['nama'=>'kaprodi'],
            ['nama'=>'sekprodi'],
            ['nama'=>'mahasiswa'],
            ['nama'=>'kosma'],
        ]);

        $fakultas = Fakultas::firstOrCreate([
            'nama' => 'Ilmu Komputer',
            'status' => "AKTIF"
        ]);

        $user = User::firstOrCreate(
            ['email' => 'master@kampus.ac.id'],
            [
                'password' => Hash::make('master01')
            ]
        );
        Biodata::create([
            'nama' => "Master User",
            'user_id' => $user->id
        ]);
        $roleDekan = Role::where('nama', 'dekan')->first();
        $roleDosen = Role::where('nama', 'dosen')->first();

        $user->role()->syncWithoutDetaching([$roleDekan->id]);
        $user->role()->syncWithoutDetaching([$roleDosen->id]);

        // 4️⃣ Model Dekan
        Dekan::firstOrCreate([
            'user_id' => $user->id,
            'fakultas_id' => $fakultas->id,
            'periode_mulai' => Carbon::now(),
            'periode_selesai' => Carbon::now(),
        ]);
        Dosen::firstOrCreate([
            'user_id' => $user->id,
            'nidn' => 'N001',
        ]);
        $prodi = Prodi::create([
            'nama' => 'Sistem Informasi',
            'kode' => 'SI',
            'jenjang' => 'S1',
            'fakultas_id' => $fakultas->id
        ]);
        Matakuliah::create([
            'nama' => 'Pemrograman Javascript',
            'kode' => 'JS01',
            'sks' => 2,
            'prodi_id' => $prodi->id
        ]);
        Semester::create([
            'semester' => '1',
            'tahun_akademik' => '2025/2026',
            'jenis' => 'Ganjil',
            'tanggal_mulai' => Carbon::now(),
            'tanggal_selesai' => Carbon::now(),
        ]);
        Angkatan::create(['tahun' => '2025']);
        Ruangan::create([
            'nama' => "Lab Jaringan",
            'kode' => "LAB01",
            'kapasitas' => 40
        ]);
    }
    
}
