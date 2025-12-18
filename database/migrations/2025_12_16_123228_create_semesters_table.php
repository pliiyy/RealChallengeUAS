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
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_akademik');
            $table->enum('jenis',['Ganjil','Genap']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status',["AKTIF","NONAKTIF"])->default("AKTIF");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};
 