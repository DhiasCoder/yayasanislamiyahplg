<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TutupAbsensiKedaluwarsa extends Command
{
    protected $signature = 'absensi:tutup-kedaluwarsa';

    protected $description = 'Menutup absensi yang sudah melewati waktu selesai dan menetapkan siswa alpa';

    public function handle()
    {
        $absensis = Absensi::with([
            'pengajaran.kelas.siswa'
        ])
            ->where('status', 'dibuka')
            ->get();

        foreach ($absensis as $absensi) {

            // Gabungkan tanggal dan jam selesai
            $waktuSelesai = Carbon::parse(
                Carbon::parse($absensi->tanggal)->format('Y-m-d')
                    . ' ' . $absensi->jam_selesai
            );

            // Lewati jika waktu absensi belum selesai
            if (now()->lt($waktuSelesai)) {
                continue;
            }

            DB::transaction(function () use ($absensi) {

                // Ambil semua siswa dari kelas pengajaran
                $siswaList = $absensi->pengajaran
                    ->kelas
                    ->siswa;

                foreach ($siswaList as $siswa) {

                    // Buat status alpa hanya jika belum ada catatan
                    AbsensiSiswa::firstOrCreate(
                        [
                            'absensi_id' => $absensi->id,
                            'siswa_id' => $siswa->id,
                        ],
                        [
                            'status' => 'alpa',
                            'waktu_absen' => null,
                            'keterangan' => 'Tidak melakukan absensi sampai waktu berakhir',
                        ]
                    );
                }

                // Tutup sesi absensi
                $absensi->update([
                    'status' => 'ditutup',
                ]);
            });

            $this->info(
                "Absensi ID {$absensi->id} berhasil ditutup."
            );
        }

        return self::SUCCESS;
    }
}
