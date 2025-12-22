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
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dekan_id')->constrained("dekan")->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained("dosen")->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained("semester")->cascadeOnDelete();
            $table->string('nomor_surat');
            $table->string('nomor_sk');
            $table->date('tanggal');
            $table->string('file')->nullable();
            $table->enum('status',['AKTIF','NONAKTIF',"APPROVED","REJECTED"])->default('AKTIF');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tugas');
    }
};
