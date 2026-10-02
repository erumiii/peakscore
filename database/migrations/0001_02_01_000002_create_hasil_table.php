<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwalId')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('userId')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('skorVerbal')->nullable();
            $table->unsignedTinyInteger('skorNumerik')->nullable();
            $table->unsignedTinyInteger('skorLogika')->nullable();
            $table->unsignedTinyInteger('skorSpasial')->nullable();
            // null = transkrip belum diterbitkan penyelenggara
            $table->timestamp('diterbitkanPada')->nullable();
            $table->timestamps();

            $table->unique(['userId', 'jadwalId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil');
    }
};
