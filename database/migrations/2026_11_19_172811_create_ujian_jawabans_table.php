
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian_jawabans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ujian_result_id')
                ->constrained('ujian_results')
                ->cascadeOnDelete();

            $table->foreignId('ujian_question_id')
                ->constrained('ujian_questions')
                ->cascadeOnDelete();

            $table->text('jawaban')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('skor', 8, 2)->default(0);

            $table->timestamps();

            $table->unique(
                ['ujian_result_id', 'ujian_question_id'],
                'ujian_jawaban_result_question_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_jawabans');
    }
};
