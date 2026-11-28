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

            $table->foreignId('ppdb_pendaftar_id')
                ->nullable()
                ->unique()
                ->constrained('ppdb_pendaftars')
                ->nullOnDelete();

            // Identitas Utama
            $table->string('nis')->nullable();
            $table->string('nisn')->nullable();
            $table->string('nik')->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('kewarganegaraan')->nullable()->default('Indonesia');
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->string('status_orang_tua')->nullable();
            $table->string('bakat_hobi')->nullable();

            // Alamat & Kontak
            $table->text('alamat')->nullable();
            $table->string('status_tempat_tinggal')->nullable();
            $table->string('jarak_rumah')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // Data Kesehatan
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->string('golongan_darah', 5)->nullable();

            // Riwayat Sekolah Sebelumnya
            $table->string('asal_sekolah')->nullable();
            $table->string('nomor_ijazah')->nullable();
            $table->date('tanggal_ijazah')->nullable();
            $table->decimal('jumlah_skhun', 6, 2)->nullable();

            // Data Orang Tua & Wali
            $table->string('nama_ayah')->nullable();
            $table->string('tempat_lahir_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->decimal('penghasilan_ayah', 15, 2)->nullable();

            $table->string('nama_ibu')->nullable();
            $table->string('tempat_lahir_ibu')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->decimal('penghasilan_ibu', 15, 2)->nullable();

            $table->string('nama_wali')->nullable();
            $table->string('hubungan_wali')->nullable();
            $table->string('pendidikan_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->decimal('penghasilan_wali', 15, 2)->nullable();

            $table->text('alamat_orang_tua_wali')->nullable();
            $table->string('kewarganegaraan_orang_tua')->nullable();
            $table->string('no_hp_orang_tua')->nullable();

            // Akademik
            $table->year('tahun_masuk')->nullable();
            $table->enum('status', [
                'aktif',
                'lulus',
                'pindah',
                'keluar',
            ])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
