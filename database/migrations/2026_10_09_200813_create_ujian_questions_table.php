<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ujian_id')
                ->constrained('ujians')
                ->cascadeOnDelete();

            $table->text('pertanyaan');

            $table->enum('tipe_soal', [
                'pilihan_ganda',
                'essay'
            ])->default('pilihan_ganda');

            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();

            $table->string('jawaban_benar')->nullable();

            $table->decimal('bobot', 5, 2)->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_questions');
    }
};
