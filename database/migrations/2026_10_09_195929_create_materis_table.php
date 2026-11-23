<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajaran_id')
                ->constrained('pengajarans')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();

            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};
