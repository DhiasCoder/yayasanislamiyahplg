<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tugas_id')
                ->constrained('tugas')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            // File hasil pengerjaan siswa
            $table->string('file_jawaban');

            $table->text('catatan_siswa')->nullable();

            $table->decimal('nilai', 5, 2)->nullable();

            $table->text('feedback_guru')->nullable();

            $table->enum('status', [
                'dikumpulkan',
                'dinilai',
            ])->default('dikumpulkan');

            $table->dateTime('tanggal_pengumpulan');

            $table->timestamps();

            // Satu pengumpulan aktif per siswa untuk setiap tugas
            $table->unique(['tugas_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
