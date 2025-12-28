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
            $table->foreignId('ruangan_id')->constrained("ruangan")->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained("shift")->cascadeOnDelete();
            $table->enum('hari',['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']);
            $table->enum('status',['AKTIF','PINDAH','BARTER'])->default('AKTIF');
            $table->timestamps();
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
 