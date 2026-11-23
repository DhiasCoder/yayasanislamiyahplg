@php
$input = 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2';
$normal = 'border-brand-200 focus:border-brand-400 focus:ring-brand-200';
$salah = 'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-100';

$jenisUjian = [
'kuis' => ['Kuis', 'Penilaian singkat', 'fa-bolt'],
'uts' => ['UTS', 'Ujian tengah semester', 'fa-file-pen'],
'uas' => ['UAS', 'Ujian akhir semester', 'fa-graduation-cap'],
];
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="mx-auto max-w-4xl space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <a href="{{ route('guru.tugas-ujian') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-brand-100 transition hover:text-white">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Tugas &amp; Ujian
            </a>

            <h1 class="mt-3 text-2xl font-extrabold sm:text-3xl">Edit Ujian</h1>
            <p class="mt-2 max-w-lg text-sm text-brand-50">
                Perbarui informasi dan jadwal ujian.
            </p>
        </div>
    </section>

    <form wire:submit="update" class="space-y-5">

        {{-- ================= INFORMASI UJIAN ================= --}}
        <section class="space-y-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="fa-solid fa-file-pen"></i>
                </span>
                <h2 class="font-extrabold text-brand-900">Informasi Ujian</h2>
            </div>

            <div>
                <label for="pengajaran_id" class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Kelas dan Mata Pelajaran
                </label>

                <select id="pengajaran_id" wire:model="pengajaran_id"
                    class="{{ $input }} {{ $errors->has('pengajaran_id') ? $salah : $normal }}">
                    <option value="">Pilih pengajaran</option>

                    @foreach ($pengajarans as $pengajaran)
                    <option value="{{ $pengajaran->id }}">
                        {{ $pengajaran->kelas?->nama_kelas ?? '-' }}
                        — {{ $pengajaran->mataPelajaran?->nama ?? '-' }}
                    </option>
                    @endforeach
                </select>

                @error('pengajaran_id')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="judul" class="mb-1.5 block text-sm font-semibold text-slate-700">Judul Ujian</label>

                <input id="judul" type="text" wire:model="judul"
                    class="{{ $input }} {{ $errors->has('judul') ? $salah : $normal }}">

                @error('judul')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi</label>

                <textarea id="deskripsi" wire:model="deskripsi" rows="4"
                    class="{{ $input }} resize-y {{ $errors->has('deskripsi') ? $salah : $normal }}"></textarea>

                <div class="mt-1.5 flex items-start justify-between gap-3">
                    <div>
                        @error('deskripsi')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <span class="shrink-0 text-xs text-slate-400" x-data
                        x-text="(($wire.deskripsi || '').length) + ' karakter'"></span>
                </div>
            </div>

            {{-- Jenis ujian --}}
            <div>
                <span class="mb-1.5 block text-sm font-semibold text-slate-700">Jenis Ujian</span>

                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach ($jenisUjian as $nilai => [$judulJenis, $deskJenis, $ikonJenis])
                    <label class="relative block cursor-pointer">
                        <input type="radio" wire:model="jenis" value="{{ $nilai }}" class="peer sr-only">

                        <div class="flex items-start gap-3 rounded-2xl border-2 border-slate-200 bg-white p-4 pr-11 transition
                                        hover:border-brand-300 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-300">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-600">
                                <i class="fa-solid {{ $ikonJenis }}"></i>
                            </span>
                            <span>
                                <span class="block text-sm font-bold text-slate-800">{{ $judulJenis }}</span>
                                <span class="mt-0.5 block text-xs text-slate-500">{{ $deskJenis }}</span>
                            </span>
                        </div>

                        <span class="pointer-events-none absolute right-4 top-4 h-5 w-5 rounded-full border-2 border-slate-300 bg-white transition
                                         peer-checked:border-brand-500 peer-checked:bg-brand-500 peer-checked:shadow-[inset_0_0_0_3px_#fff]"></span>
                    </label>
                    @endforeach
                </div>

                @error('jenis')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- ================= JADWAL & DURASI ================= --}}
        <section class="space-y-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6"
            x-data="{
                get terbalik() {
                    return this.$wire.tanggal_mulai && this.$wire.tanggal_selesai
                        && this.$wire.tanggal_selesai < this.$wire.tanggal_mulai;
                },
                get rentang() {
                    if (! this.$wire.tanggal_mulai || ! this.$wire.tanggal_selesai || this.terbalik) return null;
                    return Math.round((new Date(this.$wire.tanggal_selesai) - new Date(this.$wire.tanggal_mulai)) / 60000);
                },
                get durasiLebih() {
                    return this.rentang !== null && this.$wire.durasi && Number(this.$wire.durasi) > this.rentang;
                }
            }">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="fa-regular fa-calendar-days"></i>
                </span>
                <h2 class="font-extrabold text-brand-900">Jadwal &amp; Durasi</h2>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="tanggal_mulai" class="mb-1.5 block text-sm font-semibold text-slate-700">Mulai Ujian</label>

                    <input id="tanggal_mulai" type="datetime-local" wire:model="tanggal_mulai"
                        class="{{ $input }} {{ $errors->has('tanggal_mulai') ? $salah : $normal }}">

                    @error('tanggal_mulai')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_selesai" class="mb-1.5 block text-sm font-semibold text-slate-700">Selesai Ujian</label>

                    <input id="tanggal_selesai" type="datetime-local" wire:model="tanggal_selesai"
                        class="{{ $input }} {{ $errors->has('tanggal_selesai') ? $salah : $normal }}">

                    @error('tanggal_selesai')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="max-w-xs">
                <label for="durasi" class="mb-1.5 block text-sm font-semibold text-slate-700">Durasi (menit)</label>

                <div class="relative">
                    <input id="durasi" type="number" wire:model="durasi" min="1" inputmode="numeric"
                        class="{{ $input }} pr-16 {{ $errors->has('durasi') ? $salah : $normal }}">
                    <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">menit</span>
                </div>

                @error('durasi')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Informasi & peringatan jadwal --}}
            <div x-show="rentang !== null && ! durasiLebih" x-cloak
                class="flex items-start gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
                <i class="fa-regular fa-clock mt-0.5 text-brand-500"></i>
                <p>
                    Siswa dapat memulai ujian dalam rentang
                    <strong x-text="rentang"></strong> menit.
                </p>
            </div>

            <div x-show="terbalik" x-cloak
                class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <p>Waktu selesai lebih awal dari waktu mulai. Periksa kembali jadwal ujian.</p>
            </div>

            <div x-show="durasiLebih" x-cloak
                class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <p>
                    Durasi pengerjaan lebih panjang dari rentang jadwal
                    (<strong x-text="rentang"></strong> menit). Perpanjang waktu selesai atau kurangi durasi.
                </p>
            </div>
        </section>

        {{-- ================= PENILAIAN & PENGATURAN ================= --}}
        <section class="space-y-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="fa-solid fa-sliders"></i>
                </span>
                <h2 class="font-extrabold text-brand-900">Penilaian &amp; Pengaturan</h2>
            </div>

            <div class="max-w-xs">
                <label for="nilai_minimal" class="mb-1.5 block text-sm font-semibold text-slate-700">Nilai Minimal</label>

                <input id="nilai_minimal" type="number" wire:model="nilai_minimal" min="0" max="100" inputmode="numeric"
                    class="{{ $input }} {{ $errors->has('nilai_minimal') ? $salah : $normal }}">

                <p class="mt-1.5 text-xs text-slate-400">Batas nilai agar siswa dinyatakan tuntas (0–100).</p>

                @error('nilai_minimal')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-3">
                @foreach ([
                'acak_soal' => ['Acak urutan soal', 'Setiap siswa mendapat urutan soal yang berbeda.'],
                'acak_jawaban' => ['Acak pilihan jawaban', 'Urutan opsi A–D diacak untuk mengurangi kemungkinan menyontek.'],
                ] as $model => [$judulToggle, $deskToggle])
                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-brand-100 bg-brand-50/40 p-4 transition hover:bg-brand-50">
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">{{ $judulToggle }}</span>
                        <span class="mt-0.5 block text-xs text-slate-500">{{ $deskToggle }}</span>
                    </span>

                    <input type="checkbox" wire:model="{{ $model }}" class="peer sr-only">

                    <span class="relative h-6 w-11 shrink-0 rounded-full bg-slate-300 transition
                                     after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition
                                     peer-checked:bg-brand-500 peer-checked:after:translate-x-5
                                     peer-focus-visible:ring-2 peer-focus-visible:ring-brand-300 peer-focus-visible:ring-offset-2"></span>
                </label>
                @endforeach
            </div>
        </section>

        {{-- ================= AKSI ================= --}}
        <div class="sticky bottom-0 z-10 flex items-center justify-end gap-3 rounded-2xl border border-brand-100 bg-white/95 px-5 py-4 shadow-lg shadow-brand-100/50 backdrop-blur">
            <a href="{{ route('guru.tugas-ujian') }}"
                class="rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                Batal
            </a>

            <button type="submit" wire:loading.attr="disabled" wire:target="update"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                       hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                <span wire:loading.remove wire:target="update" class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Perubahan
                </span>

                <span wire:loading wire:target="update" class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                    Menyimpan...
                </span>
            </button>
        </div>

    </form>
</div>