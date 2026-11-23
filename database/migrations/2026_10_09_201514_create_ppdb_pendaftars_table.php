
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_pendaftars', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ppdb_period_id')
                ->constrained('ppdb_periods')
                ->cascadeOnDelete();

            $table->string('nomor_pendaftaran')->unique();

            // A. Keterangan pribadi
            $table->string('nama_lengkap');
            $table->string('nik', 16)->nullable();
            $table->string('nisn', 10)->nullable();

            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->unsignedTinyInteger('anak_ke')->nullable();

            $table->enum('status_orang_tua', [
                'lengkap',
                'yatim',
                'piatu',
                'yatim_piatu',
            ])->nullable();

            $table->string('bakat_hobi')->nullable();

            // B. Keterangan tempat tinggal
            $table->text('alamat')->nullable();

            $table->enum('status_tempat_tinggal', [
                'orang_tua',
                'menumpang',
                'kos',
                'asrama',
            ])->nullable();

            $table->string('jarak_rumah')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // C. Tinggi dan berat badan
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();

            $table->enum('golongan_darah', [
                'A', 'B', 'AB', 'O',
            ])->nullable();

            // D. Pendidikan sebelumnya
            $table->string('asal_sekolah')->nullable();

            $table->string('nomor_ijazah')->nullable();
            $table->date('tanggal_ijazah')->nullable();
            $table->decimal('jumlah_skhun', 6, 2)->nullable();

            // E. Keterangan orang tua/wali
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('hubungan_wali')->nullable();

            $table->text('alamat_orang_tua_wali')->nullable();

            $table->string('tempat_lahir_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();

            $table->string('tempat_lahir_ibu')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();

            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('pekerjaan_wali')->nullable();

            $table->decimal('penghasilan_ayah', 15, 2)->nullable();
            $table->decimal('penghasilan_ibu', 15, 2)->nullable();
            $table->decimal('penghasilan_wali', 15, 2)->nullable();

            $table->string('pendidikan_ayah')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pendidikan_wali')->nullable();

            $table->string('kewarganegaraan_orang_tua')->nullable();

            // Data administrasi PPDB
            $table->string('masuk_kelas')->nullable();

            $table->enum('status', [
                'draft',
                'terdaftar',
                'diverifikasi',
                'diterima',
                'ditolak',
                'daftar_ulang',
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_pendaftars');
    }
};
