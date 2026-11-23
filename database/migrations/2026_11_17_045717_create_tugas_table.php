<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajaran_id')
                ->constrained('pengajarans')
                ->cascadeOnDelete();

            $table->foreignId('guru_id')
                ->constrained('gurus')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();

            // File soal/instruksi dari guru
            $table->string('file_soal')->nullable();

            $table->dateTime('tanggal_mulai');
            $table->dateTime('deadline');

            $table->decimal('nilai_maksimal', 5, 2)->default(100);

            $table->boolean('boleh_terlambat')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
