<?php

namespace App\Livewire\Ppdb;

use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Models\PpdbPeriods;
use App\Models\PpdbPendaftar;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PendaftaranForm extends Component
{
    public $periode;
    public $step = 1;
    public $setuju = false;

    /*
    | Mode lihat: pendaftar yang sudah terdaftar boleh membuka formulirnya,
    | tetapi terkunci (hanya untuk dilihat). #[Locked] mencegah nilai ini
    | diubah dari browser.
    */
    #[Locked]
    public bool $terkunci = false;

    #[Locked]
    public ?string $nomorPendaftaran = null;

    #[Locked]
    public ?string $statusPendaftaran = null;

    public function nextStep()
    {
        // Mode lihat: pindah langkah tanpa validasi
        if ($this->terkunci) {
            if ($this->step < 5) {
                $this->step++;
            }

            return;
        }

        $rules = match ($this->step) {
            1 => [
                'form.nama_lengkap' => 'required|string|max:255',
                'form.nik' => 'nullable|digits:16',
                'form.nisn' => 'nullable|digits:10',
                'form.jenis_kelamin' => 'nullable|in:L,P',
                'form.tempat_lahir' => 'nullable|string|max:255',
                'form.tanggal_lahir' => 'nullable|date',
                'form.agama' => 'nullable|string|max:100',
                'form.kewarganegaraan' => 'nullable|string|max:100',
                'form.anak_ke' => 'nullable|integer|min:1',
            ],
            2 => [
                'form.alamat' => 'nullable|string',
                'form.status_tempat_tinggal' => 'nullable|in:orang_tua,menumpang,kos,asrama',
                'form.jarak_rumah' => 'nullable|string|max:255',
                'form.no_telepon' => 'nullable|string|max:30',
                'form.no_hp' => 'required|string|max:30',
                'form.email' => 'nullable|email|max:255',
            ],
            3 => [
                'form.asal_sekolah' => 'nullable|string|max:255',
                'form.nomor_ijazah' => 'nullable|string|max:255',
                'form.tanggal_ijazah' => 'nullable|date',
                'form.jumlah_skhun' => 'nullable|numeric|min:0',
                'form.masuk_kelas' => 'nullable|string|max:100',
            ],
            4 => [
                'form.nama_ayah' => 'nullable|string|max:255',
                'form.nama_ibu' => 'nullable|string|max:255',
                'form.nama_wali' => 'nullable|string|max:255',
                'form.hubungan_wali' => 'nullable|string|max:255',
                'form.alamat_orang_tua_wali' => 'nullable|string',
                'form.penghasilan_ayah' => 'nullable|numeric|min:0',
                'form.penghasilan_ibu' => 'nullable|numeric|min:0',
                'form.penghasilan_wali' => 'nullable|numeric|min:0',
            ],
            default => [],
        };

        $this->validate($rules);

        if ($this->step < 5) {
            $this->step++;
        }

        $this->saveDraft();
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }

        $this->saveDraft();
    }

    public function goToStep($step)
    {
        $step = (int) $step;

        // Mode lihat boleh membuka semua langkah; mode isi hanya langkah yang sudah dilewati
        $batas = $this->terkunci ? 5 : $this->step;

        if ($step >= 1 && $step <= $batas) {
            $this->step = $step;
        }

        $this->saveDraft();
    }

    public $form = [
        'nama_lengkap' => '',
        'nik' => '',
        'nisn' => '',
        'jenis_kelamin' => '',
        'tempat_lahir' => '',
        'tanggal_lahir' => '',
        'agama' => '',
        'kewarganegaraan' => 'Indonesia',
        'anak_ke' => '',
        'status_orang_tua' => '',
        'bakat_hobi' => '',
        'alamat' => '',
        'status_tempat_tinggal' => '',
        'jarak_rumah' => '',
        'no_telepon' => '',
        'no_hp' => '',
        'email' => '',
        'berat_badan' => '',
        'tinggi_badan' => '',
        'golongan_darah' => '',
        'asal_sekolah' => '',
        'nomor_ijazah' => '',
        'tanggal_ijazah' => '',
        'jumlah_skhun' => '',
        'nama_ayah' => '',
        'nama_ibu' => '',
        'nama_wali' => '',
        'hubungan_wali' => '',
        'alamat_orang_tua_wali' => '',
        'tempat_lahir_ayah' => '',
        'tanggal_lahir_ayah' => '',
        'tempat_lahir_ibu' => '',
        'tanggal_lahir_ibu' => '',
        'pekerjaan_ayah' => '',
        'pekerjaan_ibu' => '',
        'pekerjaan_wali' => '',
        'penghasilan_ayah' => '',
        'penghasilan_ibu' => '',
        'penghasilan_wali' => '',
        'pendidikan_ayah' => '',
        'pendidikan_ibu' => '',
        'pendidikan_wali' => '',
        'kewarganegaraan_orang_tua' => '',
        'masuk_kelas' => '',
    ];

    public function mount($periode)
    {
        // Pendaftar yang sudah terdaftar (session dari cek status / setelah mendaftar)
        // tetap boleh membuka formulirnya, tetapi dalam mode lihat (terkunci).
        if ($this->bukaModeLihat($periode)) {
            return;
        }

        $this->periode = PpdbPeriods::with('sekolah')
            ->where('status', 'dibuka')
            ->whereDate('tanggal_mulai', '<=', now()->toDateString())
            ->whereDate('tanggal_selesai', '>=', now()->toDateString())
            ->findOrFail($periode);

        // Pulihkan draf (isian + langkah terakhir) jika halaman dimuat ulang
        $this->restoreDraft();
    }

    /*
    |--------------------------------------------------------------------------
    | Draf pendaftaran (disimpan di session per periode)
    |--------------------------------------------------------------------------
    */

    /**
     * Mode lihat: isi form dengan data pendaftar yang tersimpan, lalu kunci.
     * Periode tidak harus masih dibuka, karena pendaftar hanya melihat datanya.
     */
    protected function bukaModeLihat($periodeId): bool
    {
        $pendaftarId = session('ppdb_pendaftar_id');

        if (! $pendaftarId) {
            return false;
        }

        $pendaftar = PpdbPendaftar::with('periode.sekolah')
            ->whereKey($pendaftarId)
            ->where('ppdb_period_id', $periodeId)
            ->first();

        // Session milik periode lain / tidak ditemukan: tampilkan form pendaftaran biasa
        if (! $pendaftar) {
            return false;
        }

        $this->periode = $pendaftar->periode;

        foreach (array_keys($this->form) as $key) {
            $nilai = $pendaftar->{$key} ?? null;

            if ($nilai instanceof \DateTimeInterface) {
                $nilai = $nilai->format('Y-m-d');
            }

            $this->form[$key] = $nilai ?? '';
        }

        $this->terkunci = true;
        $this->nomorPendaftaran = $pendaftar->nomor_pendaftaran;
        $this->statusPendaftaran = $pendaftar->status;
        $this->step = 1;

        return true;
    }

    protected function draftKey(): string
    {
        return 'ppdb_draft_' . $this->periode->id;
    }

    protected function saveDraft(): void
    {
        // Mode lihat tidak menyimpan draf
        if ($this->terkunci) {
            return;
        }

        session([
            $this->draftKey() => [
                'form' => $this->form,
                'step' => $this->step,
            ],
        ]);
    }

    protected function restoreDraft(): void
    {
        $draft = session($this->draftKey());

        if (! is_array($draft)) {
            return;
        }

        // Hanya ambil key yang memang ada di form, sisanya diabaikan
        if (is_array($draft['form'] ?? null)) {
            $this->form = array_merge(
                $this->form,
                array_intersect_key($draft['form'], $this->form)
            );
        }

        $this->step = min(max((int) ($draft['step'] ?? 1), 1), 5);
    }

    // Simpan draf setiap kali satu isian berubah (wire:model.blur di view)
    public function updated($name): void
    {
        if (str_starts_with($name, 'form.')) {
            $this->saveDraft();
        }
    }

    protected function rules(): array
    {
        return [
            'form.nama_lengkap' => 'required|string|max:255',
            'form.nik' => 'nullable|digits:16',
            'form.nisn' => 'nullable|digits:10',
            'form.jenis_kelamin' => 'nullable|in:L,P',
            'form.tempat_lahir' => 'nullable|string|max:255',
            'form.tanggal_lahir' => 'nullable|date',
            'form.agama' => 'nullable|string|max:100',
            'form.kewarganegaraan' => 'nullable|string|max:100',
            'form.anak_ke' => 'nullable|integer|min:1',
            'form.status_orang_tua' => 'nullable|in:lengkap,yatim,piatu,yatim_piatu',
            'form.bakat_hobi' => 'nullable|string|max:255',
            'form.alamat' => 'nullable|string',
            'form.status_tempat_tinggal' => 'nullable|in:orang_tua,menumpang,kos,asrama',
            'form.jarak_rumah' => 'nullable|string|max:255',
            'form.no_telepon' => 'nullable|string|max:30',
            'form.no_hp' => 'required|string|max:30',
            'form.email' => 'nullable|email|max:255',
            'form.berat_badan' => 'nullable|numeric|min:0',
            'form.tinggi_badan' => 'nullable|numeric|min:0',
            'form.golongan_darah' => 'nullable|in:A,B,AB,O',
            'form.asal_sekolah' => 'nullable|string|max:255',
            'form.nomor_ijazah' => 'nullable|string|max:255',
            'form.tanggal_ijazah' => 'nullable|date',
            'form.jumlah_skhun' => 'nullable|numeric|min:0',
            'form.nama_ayah' => 'nullable|string|max:255',
            'form.nama_ibu' => 'nullable|string|max:255',
            'form.nama_wali' => 'nullable|string|max:255',
            'form.hubungan_wali' => 'nullable|string|max:255',
            'form.alamat_orang_tua_wali' => 'nullable|string',
            'form.tempat_lahir_ayah' => 'nullable|string|max:255',
            'form.tanggal_lahir_ayah' => 'nullable|date',
            'form.tempat_lahir_ibu' => 'nullable|string|max:255',
            'form.tanggal_lahir_ibu' => 'nullable|date',
            'form.pekerjaan_ayah' => 'nullable|string|max:255',
            'form.pekerjaan_ibu' => 'nullable|string|max:255',
            'form.pekerjaan_wali' => 'nullable|string|max:255',
            'form.penghasilan_ayah' => 'nullable|numeric|min:0',
            'form.penghasilan_ibu' => 'nullable|numeric|min:0',
            'form.penghasilan_wali' => 'nullable|numeric|min:0',
            'form.pendidikan_ayah' => 'nullable|string|max:255',
            'form.pendidikan_ibu' => 'nullable|string|max:255',
            'form.pendidikan_wali' => 'nullable|string|max:255',
            'form.kewarganegaraan_orang_tua' => 'nullable|string|max:255',
            'form.masuk_kelas' => 'nullable|string|max:100',
        ];
    }

    // Nama field yang lebih enak dibaca di pesan error (bukan "form.nama_lengkap")
    protected function validationAttributes(): array
    {
        $khusus = [
            'nik' => 'NIK',
            'nisn' => 'NISN',
            'no_hp' => 'nomor HP',
            'no_telepon' => 'nomor telepon',
            'jumlah_skhun' => 'jumlah nilai SKHUN',
            'alamat_orang_tua_wali' => 'alamat orang tua / wali',
        ];

        $attributes = ['setuju' => 'pernyataan persetujuan'];

        foreach (array_keys($this->form) as $key) {
            $attributes['form.' . $key] = $khusus[$key] ?? str_replace('_', ' ', $key);
        }

        return $attributes;
    }

    public function simpan()
    {
        // Formulir terkunci tidak boleh dikirim ulang
        abort_if($this->terkunci, 403, 'Formulir terkunci.');

        $this->validate();
        $data = $this->form;
        $this->validate([
            'setuju' => 'accepted',
        ]);
        foreach (
            [
                'anak_ke',
                'berat_badan',
                'tinggi_badan',
                'jumlah_skhun',
                'penghasilan_ayah',
                'penghasilan_ibu',
                'penghasilan_wali',
            ] as $field
        ) {
            $data[$field] = filled($data[$field] ?? null)
                ? $data[$field]
                : null;
        }
        $periode = PpdbPeriods::whereKey($this->periode->id)
            ->where('status', 'dibuka')
            ->whereDate('tanggal_mulai', '<=', now()->toDateString())
            ->whereDate('tanggal_selesai', '>=', now()->toDateString())
            ->firstOrFail();

        $pendaftar = DB::transaction(function () use ($periode, $data) {
            do {
                $nomor = 'PPDB-' . now()->format('Y') . '-' .
                    strtoupper(Str::random(8));
            } while (PpdbPendaftar::where('nomor_pendaftaran', $nomor)->exists());

            $kodeAkses = strtoupper(Str::random(10));

            return PpdbPendaftar::create(array_merge(
                $data,
                [
                    'ppdb_period_id' => $periode->id,
                    'nomor_pendaftaran' => $nomor,
                    'kode_akses' => $kodeAkses,
                    'status' => 'terdaftar',
                ]
            ));
        });

        // Pendaftaran berhasil: draf tidak diperlukan lagi
        session()->forget($this->draftKey());

        session([
            'ppdb_pendaftar_id' => $pendaftar->id,
        ]);

        session([
            'ppdb_kredensial' => [
                'nomor_pendaftaran' => $pendaftar->nomor_pendaftaran,
                'kode_akses' => $pendaftar->kode_akses,
            ],
        ]);

        return redirect()->route('ppdb.dokumen');
    }

    public function render()
    {
        return view('livewire.ppdb.pendaftaran-form')
            ->layout('user.layouts.app');
    }
}
