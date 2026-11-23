<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_periods', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sekolah_id')
                ->constrained('sekolahs')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            $table->string('nama');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->enum('status', [
                'draft',
                'dibuka',
                'ditutup'
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_periods');
    }
};
