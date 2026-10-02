<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->increments('soalId');
            $table->text('isiSoal')->nullable();
            $table->string('opsiA', 255)->nullable();
            $table->string('opsiB', 255)->nullable();
            $table->string('opsiC', 255)->nullable();
            $table->string('opsiD', 255)->nullable();
            $table->string('gambarSoal', 500)->nullable();
            $table->string('gambarOpsiA', 500)->nullable();
            $table->string('gambarOpsiB', 500)->nullable();
            $table->string('gambarOpsiC', 500)->nullable();
            $table->string('gambarOpsiD', 500)->nullable();
            $table->char('jawabanBenar', 1);
            $table->enum('kategori', ['Verbal', 'Numeric', 'Logic', 'Spatial']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
