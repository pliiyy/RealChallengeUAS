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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("user")->cascadeOnDelete();
            $table->foreignId('angkatan_id')->constrained("angkatan")->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained("kelas")->cascadeOnDelete();
            $table->string('nim')->unique();
            $table->enum('status',['AKTIF','NONAKTIF','LULUS'])->default('AKTIF');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
