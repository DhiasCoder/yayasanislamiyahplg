<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajaran_id')
                ->constrained('pengajarans')
                ->cascadeOnDelete();

            $table->foreignId('materi_id')
            ->nullable()
            ->constrained('materis')
            ->nullOnDelete();

            $table->date('tanggal');

            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();

            $table->string('kode_absensi')->nullable();
            $table->enum('status', ['dibuka', 'ditutup'])
                ->default('dibuka');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
