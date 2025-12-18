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
        Schema::create('biodata', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_kelamin',["L","P"])->nullable();
            $table->enum('agama',["ISLAM","PROTESTAN","KATOLIK","HINDU","BUDHA","KONGHUCU"])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kec_id')->nullable();
            $table->string('kab_id')->nullable();
            $table->string('prov_id')->nullable();
            $table->string('kodepos')->nullable();
            $table->string('foto_profil')->nullable();
            $table->foreignId('user_id')->constrained("user")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodata');
    }
};
