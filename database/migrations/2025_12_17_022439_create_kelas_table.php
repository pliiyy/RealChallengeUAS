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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prodi_id')->constrained("prodi")->cascadeOnDelete();
            $table->foreignId('angkatan_id')->constrained("angkatan")->cascadeOnDelete();
            $table->string('kode');
            $table->string('nama');
            $table->unsignedTinyInteger('semester');
            $table->unsignedSmallInteger('kapasitas')->default(40);
            $table->enum('tipe',['R','NR'])->default('R');
            $table->enum('status',['AKTIF','NONAKTIF'])->default('AKTIF');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
