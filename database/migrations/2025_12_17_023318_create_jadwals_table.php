<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengampu_mk_id')->constrained("pengampu_mk")->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained("dosen")->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained("kelas")->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained("ruangan")->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained("shift")->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained("semester")->cascadeOnDelete();
            $table->enum('hari',['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']);
            $table->enum('status',['AKTIF','PINDAH','BARTER'])->default('AKTIF');
            $table->timestamps();
            $table->unique([
                'ruangan_id',
                'hari',
                'shift_id',
                'semester_id'
            ], 'jadwal_ruangan_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
