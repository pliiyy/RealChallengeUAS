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
        Schema::create('khs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_detail_id')->constrained("krs_detail")->cascadeOnDelete();
            $table->enum('nilai_huruf',['A','AB','B','BC','C','D','E'])->nullable();
            $table->decimal('nilai_angka',5,2)->nullable();
            $table->decimal('bobot',3,2)->nullable();
            $table->unsignedTinyInteger('sks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khs');
    }
};
