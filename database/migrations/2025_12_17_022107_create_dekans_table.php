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
        Schema::create('dekan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("user")->cascadeOnDelete();
            $table->foreignId('fakultas_id')->constrained("fakultas")->cascadeOnDelete();
            $table->enum('status',['AKTIF','NONAKTIF'])->default('AKTIF');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dekan');
    }
};
