<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwalId')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('userId')->constrained('users')->cascadeOnDelete();
            // soal.soalId adalah increments (unsigned int 4-byte) — tipe FK harus cocok
            $table->unsignedInteger('soalId');
            $table->foreign('soalId')->references('soalId')->on('soal')->cascadeOnDelete();
            $table->char('opsiDipilih', 1);
            $table->timestamps();

            $table->unique(['userId', 'jadwalId', 'soalId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban');
    }
};
