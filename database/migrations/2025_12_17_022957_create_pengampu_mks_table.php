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
        Schema::create('pengampu_mk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_tugas_id')->constrained("surat_tugas")->cascadeOnDelete();
            $table->foreignId('matakuliah_id')->constrained("matakuliah")->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained("prodi")->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained("kelas")->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained("semester")->cascadeOnDelete();
            $table->unsignedTinyInteger('sks');
            $table->enum('status',['AKTIF','NONAKTIF'])->default('AKTIF');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengampu_mk');
    }
};
