<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_pembayarans', function (Blueprint $table) {
            $table->id();

            // Relasi ke pendaftar PPDB
            $table->foreignId('pendaftar_id')
                ->constrained('ppdb_pendaftars')
                ->cascadeOnDelete();

            // Informasi pembayaran
            $table->string('jenis_pembayaran');
            $table->decimal('nominal', 15, 2);

            $table->dateTime('tanggal_bayar')->nullable();

            // Pembayaran langsung ke sekolah
            $table->enum('metode_pembayaran', [
                'tunai',
                'transfer_manual',
            ])->nullable();

            $table->string('nomor_kuitansi')->nullable()->unique();

            $table->string('bukti_pembayaran')->nullable();

            // Petugas sekolah yang mencatat pembayaran
            $table->foreignId('dicatat_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Status verifikasi pembayaran
            $table->enum('status', [
                'menunggu',
                'diverifikasi',
                'ditolak',
            ])->default('menunggu');

            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Mempermudah pencarian pembayaran berdasarkan pendaftar
            $table->index(['pendaftar_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_pembayarans');
    }
};
