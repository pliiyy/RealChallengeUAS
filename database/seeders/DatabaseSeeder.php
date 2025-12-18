<?php

namespace Database\Seeders;

use App\Models\Biodata;
use App\Models\Dekan;
use App\Models\Fakultas;
use App\Models\Role;
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

        $user->roles()->syncWithoutDetaching([$roleDekan->id]);

        // 4️⃣ Model Dekan
        Dekan::firstOrCreate([
            'user_id' => $user->id,
            'fakultas_id' => $fakultas->id,
            'periode_mulai' => Carbon::now(),
            'periode_selesai' => Carbon::now(),
        ]);
    }
    
}
