<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diskusi_materis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('materi_id')
                ->constrained('materis')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('diskusi_materis')
                ->cascadeOnDelete();

            $table->text('isi');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskusi_materis');
    }
};
