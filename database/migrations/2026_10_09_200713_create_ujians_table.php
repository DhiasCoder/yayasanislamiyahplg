<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajaran_id')
                ->constrained('pengajarans')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();

            $table->enum('jenis', [
                'latihan',
                'kuis',
                'UTS',
                'UAS',
                'ujian'
            ])->default('ujian');

            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();

            $table->integer('durasi')->nullable();

            $table->boolean('acak_soal')->default(false);
            $table->boolean('acak_jawaban')->default(false);

            $table->decimal('nilai_minimal', 5, 2)->nullable();

            $table->enum('status', [
                'draft',
                'published',
                'closed'
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
