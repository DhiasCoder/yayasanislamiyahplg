<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapor_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rapor_id')
                ->constrained('rapors')
                ->cascadeOnDelete();

            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')
                ->cascadeOnDelete();

            $table->decimal('nilai', 5, 2);

            $table->string('predikat')->nullable();

            $table->text('deskripsi')->nullable();

            $table->timestamps();

            $table->unique([
                'rapor_id',
                'mata_pelajaran_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_details');
    }
};
