<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('yayasan_id')
                ->constrained('yayasans')
                ->cascadeOnDelete();

            $table->string('nama_sekolah');
            $table->string('kode_sekolah')->unique();

            $table->enum('jenjang', [
                'SMP',
                'SMA',
                'SMK'
            ]);

            $table->string('alamat')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('logo')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
