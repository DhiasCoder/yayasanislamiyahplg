
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_dokumens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pendaftar_id')
                ->constrained('ppdb_pendaftars')
                ->cascadeOnDelete();

            $table->enum('jenis_dokumen', [
                'kartu_keluarga',
                'akta_kelahiran',
                'surat_keterangan_lulus',
                'ktp_orang_tua',
                'foto_3x4',
                'foto_2x3',
                'ijazah',
                'dokumen_lainnya',
            ]);

            $table->string('nama_file')->nullable();
            $table->string('file_path');

            // Nomor foto dalam satu jenis, jika diperlukan
            $table->unsignedTinyInteger('nomor_urut')->nullable();

            $table->enum('status_verifikasi', [
                'menunggu',
                'diterima',
                'ditolak',
            ])->default('menunggu');

            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index(['pendaftar_id', 'jenis_dokumen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_dokumens');
    }
};
