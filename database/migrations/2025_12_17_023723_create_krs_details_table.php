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
        Schema::create('krs_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained("krs")->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained("kelas")->cascadeOnDelete();
            $table->foreignId('matakuliah_id')->constrained("matakuliah")->cascadeOnDelete();
            $table->foreignId('pengampu_mk_id')->constrained("pengampu_mk")->cascadeOnDelete();
            $table->unsignedTinyInteger('sks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs_detail');
    }
};
