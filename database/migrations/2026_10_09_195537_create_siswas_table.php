<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('sekolah_id')
                ->constrained('sekolahs')
                ->cascadeOnDelete();

            $table->string('nis')->nullable();
            $table->string('nisn')->nullable();
            $table->string('nik')->nullable();

            $table->string('nama_lengkap');

            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            $table->text('alamat')->nullable();
            $table->string('agama')->nullable();
            $table->string('no_hp')->nullable();

            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('no_hp_orang_tua')->nullable();

            $table->year('tahun_masuk')->nullable();

            $table->enum('status', [
                'aktif',
                'lulus',
                'pindah',
                'keluar'
            ])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
