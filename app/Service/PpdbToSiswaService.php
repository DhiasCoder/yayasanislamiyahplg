<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\PpdbPendaftar;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PpdbToSiswaService
{
    /**
     * Konversi data PpdbPendaftar menjadi akun User + Role + data Siswa.
     *
     * @return array{siswa: Siswa, user: User, generated_email: string, default_password: ?string, is_new: bool}
     */
    public function convert(PpdbPendaftar $pendaftar, ?int $fallbackSekolahId = null): array
    {
        $pendaftar->loadMissing('periode');

        $sekolahId = $pendaftar->periode?->sekolah_id ?? $fallbackSekolahId;

        if (! $sekolahId) {
            throw new RuntimeException('Sekolah ID tidak ditemukan pada periode PPDB pendaftar.');
        }

        return DB::transaction(function () use ($pendaftar, $sekolahId) {
            // Cek apakah pendaftar ini sudah pernah dikonversi sebelumnya
            $existingSiswa = Siswa::query()
                ->where('ppdb_pendaftar_id', $pendaftar->id)
                ->orWhere(function ($q) use ($pendaftar, $sekolahId) {
                    $q->where('sekolah_id', $sekolahId)
                        ->where(function ($sub) use ($pendaftar) {
                            if (filled($pendaftar->nisn)) {
                                $sub->orWhere('nisn', $pendaftar->nisn);
                            }
                            if (filled($pendaftar->nik)) {
                                $sub->orWhere('nik', $pendaftar->nik);
                            }
                        });
                })
                ->first();

            $isNew = $existingSiswa === null;
            $defaultPassword = null;

            if ($existingSiswa?->user) {
                $user = $existingSiswa->user;
            } else {
                $email = $this->resolveUniqueEmail($pendaftar);
                $defaultPassword = $this->resolveDefaultPassword($pendaftar);

                $user = User::create([
                    'name' => $pendaftar->nama_lengkap,
                    'email' => $email,
                    'password' => $defaultPassword,
                ]);
            }

            // Pastikan user memiliki role siswa/murid untuk sekolah terkait
            $role = Role::whereIn('nama_role', ['siswa', 'murid'])->first();

            if ($role) {
                UserRole::firstOrCreate([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'sekolah_id' => $sekolahId,
                ]);
            }

            $payloadSiswa = [
                'user_id' => $user->id,
                'sekolah_id' => $sekolahId,
                'ppdb_pendaftar_id' => $pendaftar->id,
                'nisn' => $pendaftar->nisn,
                'nik' => $pendaftar->nik,
                'nama_lengkap' => $pendaftar->nama_lengkap,
                'jenis_kelamin' => $this->normalizeJenisKelamin($pendaftar->jenis_kelamin),
                'tempat_lahir' => $pendaftar->tempat_lahir,
                'tanggal_lahir' => $pendaftar->tanggal_lahir,
                'agama' => $pendaftar->agama,
                'kewarganegaraan' => $pendaftar->kewarganegaraan ?: 'Indonesia',
                'anak_ke' => $pendaftar->anak_ke,
                'status_orang_tua' => $pendaftar->status_orang_tua,
                'bakat_hobi' => $pendaftar->bakat_hobi,
                'alamat' => $pendaftar->alamat,
                'status_tempat_tinggal' => $pendaftar->status_tempat_tinggal,
                'jarak_rumah' => $pendaftar->jarak_rumah,
                'no_telepon' => $pendaftar->no_telepon,
                'no_hp' => $pendaftar->no_hp,
                'email' => $pendaftar->email ?: $user->email,
                'berat_badan' => $pendaftar->berat_badan,
                'tinggi_badan' => $pendaftar->tinggi_badan,
                'golongan_darah' => $pendaftar->golongan_darah,
                'asal_sekolah' => $pendaftar->asal_sekolah,
                'nomor_ijazah' => $pendaftar->nomor_ijazah,
                'tanggal_ijazah' => $pendaftar->tanggal_ijazah,
                'jumlah_skhun' => $pendaftar->jumlah_skhun,
                'nama_ayah' => $pendaftar->nama_ayah,
                'tempat_lahir_ayah' => $pendaftar->tempat_lahir_ayah,
                'tanggal_lahir_ayah' => $pendaftar->tanggal_lahir_ayah,
                'pendidikan_ayah' => $pendaftar->pendidikan_ayah,
                'pekerjaan_ayah' => $pendaftar->pekerjaan_ayah,
                'penghasilan_ayah' => $pendaftar->penghasilan_ayah,
                'nama_ibu' => $pendaftar->nama_ibu,
                'tempat_lahir_ibu' => $pendaftar->tempat_lahir_ibu,
                'tanggal_lahir_ibu' => $pendaftar->tanggal_lahir_ibu,
                'pendidikan_ibu' => $pendaftar->pendidikan_ibu,
                'pekerjaan_ibu' => $pendaftar->pekerjaan_ibu,
                'penghasilan_ibu' => $pendaftar->penghasilan_ibu,
                'nama_wali' => $pendaftar->nama_wali,
                'hubungan_wali' => $pendaftar->hubungan_wali,
                'pendidikan_wali' => $pendaftar->pendidikan_wali,
                'pekerjaan_wali' => $pendaftar->pekerjaan_wali,
                'penghasilan_wali' => $pendaftar->penghasilan_wali,
                'alamat_orang_tua_wali' => $pendaftar->alamat_orang_tua_wali,
                'kewarganegaraan_orang_tua' => $pendaftar->kewarganegaraan_orang_tua,
                'no_hp_orang_tua' => $pendaftar->no_hp ?: $pendaftar->no_telepon,
                'tahun_masuk' => $existingSiswa?->tahun_masuk ?? now()->year,
                'status' => $existingSiswa?->status ?? 'aktif',
            ];

            if ($existingSiswa) {
                $existingSiswa->update($payloadSiswa);
                $siswa = $existingSiswa->fresh();
            } else {
                $siswa = Siswa::create($payloadSiswa);
            }

            // Opsional: otomatis tempatkan ke kelas jika kolom masuk_kelas cocok dengan nama_kelas
            $this->attachKelasIfMatched($siswa, $pendaftar, $sekolahId);

            return [
                'siswa' => $siswa,
                'user' => $user,
                'generated_email' => $user->email,
                'default_password' => $defaultPassword,
                'is_new' => $isNew,
            ];
        });
    }

    protected function resolveUniqueEmail(PpdbPendaftar $pendaftar): string
    {
        if (filled($pendaftar->email) && ! User::where('email', $pendaftar->email)->exists()) {
            return strtolower(trim($pendaftar->email));
        }

        $baseIdentifier = filled($pendaftar->nisn)
            ? preg_replace('/[^0-9a-zA-Z]/', '', $pendaftar->nisn)
            : Str::slug($pendaftar->nomor_pendaftaran ?: 'siswa-' . $pendaftar->id, '');

        $candidate = strtolower($baseIdentifier) . '@siswa.sekolah.id';
        $counter = 1;

        while (User::where('email', $candidate)->exists()) {
            $candidate = strtolower($baseIdentifier) . $counter . '@siswa.sekolah.id';
            $counter++;
        }

        return $candidate;
    }

    protected function resolveDefaultPassword(PpdbPendaftar $pendaftar): string
    {
        if ($pendaftar->tanggal_lahir) {
            return $pendaftar->tanggal_lahir->format('dmY'); // Contoh: 17082010
        }

        if (filled($pendaftar->kode_akses)) {
            return $pendaftar->kode_akses;
        }

        return 'siswa12345';
    }

    protected function normalizeJenisKelamin(?string $value): ?string
    {
        return match (strtoupper(trim((string) $value))) {
            'L', 'LAKI-LAKI', 'LAKI - LAKI' => 'L',
            'P', 'PEREMPUAN' => 'P',
            default => null,
        };
    }

    protected function attachKelasIfMatched(Siswa $siswa, PpdbPendaftar $pendaftar, int $sekolahId): void
    {
        if (blank($pendaftar->masuk_kelas)) {
            return;
        }

        $tahunAjaranId = $pendaftar->periode?->tahun_ajaran_id
            ?? TahunAjaran::where('sekolah_id', $sekolahId)
            ->where('is_active', true)
            ->value('id');

        if (! $tahunAjaranId) {
            return;
        }

        $kelas = Kelas::where('sekolah_id', $sekolahId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('nama_kelas', trim($pendaftar->masuk_kelas))
            ->first();

        if ($kelas) {
            $siswa->kelas()->syncWithoutDetaching([
                $kelas->id => ['tahun_ajaran_id' => $tahunAjaranId],
            ]);
        }
    }
}
