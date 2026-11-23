<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ujian_id')
                ->constrained('ujians')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->decimal('skor', 5, 2)->default(0);

            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();

            $table->enum('status', [
                'belum_mulai',
                'sedang_mengerjakan',
                'selesai'
            ])->default('belum_mulai');

            $table->timestamps();

            $table->unique([
                'ujian_id',
                'siswa_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_results');
    }
};
