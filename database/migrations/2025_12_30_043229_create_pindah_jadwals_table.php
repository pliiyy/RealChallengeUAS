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
        Schema::create('pindah_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_asal_id')->nullable()->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('jadwal_tujuan_id')->nullable()->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangan')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shift')->cascadeOnDelete();
            $table->string('alasan')->nullable();
            $table->enum('hari',['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'])->nullable();
            $table->enum('status_jadwal',['AKTIF','NONAKTIF','PINDAH','BARTER'])->default('AKTIF');
            $table->enum('status',['ANTRI','APPROVED','REJECTED'])->default('ANTRI');
            $table->foreignId('kosma_id')->nullable()->constrained('kosma')->cascadeOnDelete();
            $table->timestamps();
        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pindah_jadwal');
    }
};
