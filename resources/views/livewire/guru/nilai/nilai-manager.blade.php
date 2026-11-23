@php
// Rapikan angka desimal dari database: 85.00 -> 85, 70.50 -> 70,5
$angka = fn ($n) => is_numeric($n)
? rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',')
: $n;

$avatar = fn ($nama) => 'https://ui-avatars.com/api/?name=' . urlencode($nama ?: 'Siswa') . '&background=e1f4e6&color=23653c&bold=true';

$tabs = [
'tugas' => ['Tugas', 'fa-list-check'],
'ujian' => ['Kuis & Ujian', 'fa-file-pen'],
'praktik' => ['Praktik', 'fa-flask'],
'rekap' => ['Rekap Nilai', 'fa-chart-column'],
];

$pengajaranDipilih = $pengajaran_id
? collect($pengajaranList)->firstWhere('id', $pengajaran_id)
: null;

$gayaStatusUjian = [
'selesai' => 'bg-brand-100 text-brand-700',
'sedang_mengerjakan' => 'bg-sky-100 text-sky-700',
];
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <h1 class="text-2xl font-extrabold sm:text-3xl">Manajemen Nilai</h1>
            <p class="mt-2 max-w-xl text-sm text-brand-50">
                Kelola nilai tugas, kuis, ujian, praktik, dan rekap nilai siswa.
            </p>

            @if ($pengajaranDipilih)
            <div class="mt-4 inline-flex flex-wrap items-center gap-x-3 gap-y-1 rounded-full bg-white/15 px-4 py-1.5 text-sm backdrop-blur">
                <span class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-school text-xs"></i>
                    {{ $pengajaranDipilih->kelas->nama_kelas }}
                </span>
                <span class="text-brand-100">·</span>
                <span class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-book text-xs"></i>
                    {{ $pengajaranDipilih->mataPelajaran->nama }}
                </span>
            </div>
            @endif
        </div>
    </section>

    {{-- ================= NOTIFIKASI ================= --}}
    @if (session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="flex items-center gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
        <i class="fa-solid fa-circle-check text-brand-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" @click="show = false" class="text-brand-400 transition hover:text-brand-600" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    {{-- ================= PILIH PENGAJARAN ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-brand-100 text-brand-600">
                <i class="fa-solid fa-chalkboard"></i>
            </span>

            <div class="min-w-0 flex-1">
                <label for="pengajaran_id" class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Pilih Kelas dan Mata Pelajaran
                </label>

                <select id="pengajaran_id" wire:model.live="pengajaran_id"
                    class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition md:max-w-md
                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                    <option value="">Pilih pengajaran</option>

                    @foreach ($pengajaranList as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->kelas->nama_kelas }}
                        - {{ $item->mataPelajaran->nama }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div wire:loading wire:target="pengajaran_id" class="flex items-center gap-2 text-sm text-brand-600">
                <i class="fa-solid fa-circle-notch fa-spin"></i> Memuat...
            </div>
        </div>
    </div>

    {{-- ================= TAB ================= --}}
    <div class="inline-flex w-full flex-wrap gap-1 rounded-2xl bg-brand-100/60 p-1.5 sm:w-auto">
        @foreach ($tabs as $key => [$label, $ikon])
        <button
            wire:click="$set('tabAktif', '{{ $key }}')"
            class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition sm:flex-none
                    {{ $tabAktif === $key
                        ? 'bg-white text-brand-700 shadow-sm'
                        : 'text-slate-500 hover:text-brand-700' }}">
            <i class="fa-solid {{ $ikon }} text-xs"></i>
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if (! $pengajaran_id)

    <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
        <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
            <i class="fa-solid fa-chalkboard"></i>
        </span>
        <p class="mt-4 font-semibold text-brand-900">Pilih kelas terlebih dahulu</p>
        <p class="mt-1 text-sm text-slate-500">
            Pilih kelas dan mata pelajaran untuk melihat data penilaian.
        </p>
    </div>

    @else

    {{-- ================= TAB TUGAS ================= --}}
    @if ($tabAktif === 'tugas')
    <div class="space-y-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">

        <div class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-100 text-brand-600">
                <i class="fa-solid fa-list-check"></i>
            </span>
            <div>
                <h2 class="text-lg font-extrabold text-brand-900">Penilaian Tugas</h2>
                <p class="text-sm text-slate-500">Input nilai dan feedback berdasarkan pengumpulan siswa.</p>
            </div>
        </div>

        <div>
            <label for="tugas_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Pilih Tugas</label>

            <select id="tugas_id" wire:model.live="tugas_id"
                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition md:max-w-md
                               focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                <option value="">Pilih tugas</option>

                @foreach ($tugasList as $tugas)
                <option value="{{ $tugas->id }}">{{ $tugas->judul }}</option>
                @endforeach
            </select>
        </div>

        @if ($tugas_id)
        @php
        $tugasDipilih = $tugasList->firstWhere('id', $tugas_id);
        $maksNilai = $tugasDipilih?->nilai_maksimal;
        $totalKumpul = $pengumpulan->count();
        $sudahDinilai = $pengumpulan->filter(fn ($p) => $p->nilai !== null)->count();
        $rataNilai = $pengumpulan->whereNotNull('nilai')->avg('nilai');
        @endphp

        {{-- Ringkasan --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl bg-brand-50 px-4 py-3">
                <p class="text-2xl font-extrabold text-brand-700">{{ $totalKumpul }}</p>
                <p class="text-xs font-medium text-brand-600">Pengumpulan</p>
            </div>
            <div class="rounded-xl bg-brand-50 px-4 py-3">
                <p class="text-2xl font-extrabold text-brand-700">{{ $sudahDinilai }}<span class="text-sm font-semibold text-slate-400">/{{ $totalKumpul }}</span></p>
                <p class="text-xs font-medium text-brand-600">Sudah dinilai</p>
            </div>
            <div class="rounded-xl bg-brand-50 px-4 py-3">
                <p class="text-2xl font-extrabold text-brand-700">{{ $rataNilai !== null ? $angka(round($rataNilai, 2)) : '-' }}</p>
                <p class="text-xs font-medium text-brand-600">Rata-rata nilai</p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
                <p class="text-2xl font-extrabold text-slate-700">{{ $maksNilai !== null ? $angka($maksNilai) : '-' }}</p>
                <p class="text-xs font-medium text-slate-500">Nilai maksimal</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-brand-100">
            <table class="w-full text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="p-3 text-left">Siswa</th>
                        <th class="p-3 text-left">Pengumpulan</th>
                        <th class="p-3 text-left">Nilai</th>
                        <th class="p-3 text-left">Feedback</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($pengumpulan as $item)
                    @php
                    $namaSiswa = $item->siswa?->nama_lengkap
                    ?? $item->siswa?->user?->name
                    ?? 'Siswa tidak ditemukan';
                    $terlambat = $tugasDipilih?->deadline
                    && $item->tanggal_pengumpulan
                    && \Carbon\Carbon::parse($item->tanggal_pengumpulan)
                    ->gt(\Carbon\Carbon::parse($tugasDipilih->deadline));
                    @endphp

                    <tr wire:key="nilai-tugas-{{ $item->id }}" class="transition hover:bg-brand-50/40">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $avatar($namaSiswa) }}" alt="{{ $namaSiswa }}"
                                    class="h-9 w-9 shrink-0 rounded-full ring-2 ring-brand-100">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800">{{ $namaSiswa }}</p>
                                    @if ($item->nilai !== null)
                                    <p class="text-xs font-medium text-brand-600">
                                        <i class="fa-solid fa-circle-check"></i> Nilai tersimpan: {{ $angka($item->nilai) }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="p-3">
                            @if ($item->file_jawaban)
                            <a href="{{ Storage::disk('public')->url($item->file_jawaban) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-100">
                                <i class="fa-solid fa-file-arrow-down"></i>
                                Lihat Jawaban
                            </a>

                            @if ($item->tanggal_pengumpulan)
                            <p class="mt-1 text-xs text-slate-400">
                                {{ \Carbon\Carbon::parse($item->tanggal_pengumpulan)->translatedFormat('d M Y, H:i') }}
                                @if ($terlambat)
                                <span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-600">Terlambat</span>
                                @endif
                            </p>
                            @endif
                            @else
                            <span class="text-slate-400">Tidak ada file</span>
                            @endif
                        </td>

                        <td class="min-w-36 p-3">
                            <div class="flex items-center gap-2">
                                <input type="number" min="0" max="{{ $maksNilai }}"
                                    wire:model="nilaiInputs.{{ $item->id }}"
                                    placeholder="Nilai"
                                    class="w-24 rounded-lg border border-brand-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-brand-400 focus:ring-2 focus:ring-brand-200">

                                @if ($maksNilai !== null)
                                <span class="text-xs font-medium text-slate-400">/ {{ $angka($maksNilai) }}</span>
                                @endif
                            </div>

                            @error("nilaiInputs.{$item->id}")
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </td>

                        <td class="min-w-56 p-3">
                            <input type="text"
                                wire:model="feedbackInputs.{{ $item->id }}"
                                placeholder="Feedback guru"
                                class="w-full rounded-lg border border-brand-200 bg-white px-3 py-2 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-regular fa-folder-open"></i>
                            </span>
                            <p class="mt-3 font-semibold text-slate-700">Belum ada pengumpulan tugas</p>
                            <p class="mt-1 text-sm text-slate-500">Pengumpulan siswa akan tampil di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengumpulan->isNotEmpty())
        <div class="sticky bottom-0 -mx-5 -mb-5 flex items-center justify-between gap-3 rounded-b-2xl border-t border-brand-100 bg-white/95 px-5 py-4 backdrop-blur sm:-mx-6 sm:-mb-6 sm:px-6">
            <p class="hidden text-sm text-slate-500 sm:block">
                <span class="font-bold text-brand-700">{{ $sudahDinilai }}</span> dari {{ $totalKumpul }} pengumpulan sudah dinilai
            </p>

            <button wire:click="simpanNilaiTugas" wire:loading.attr="disabled" wire:target="simpanNilaiTugas"
                class="ml-auto inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                                       hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                <span wire:loading.remove wire:target="simpanNilaiTugas" class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Nilai
                </span>
                <span wire:loading wire:target="simpanNilaiTugas" class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                    Menyimpan...
                </span>
            </button>
        </div>
        @endif
        @endif
    </div>
    @endif

    {{-- ================= TAB KUIS DAN UJIAN ================= --}}
    @if ($tabAktif === 'ujian')
    <div class="space-y-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">

        <div class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-100 text-brand-600">
                <i class="fa-solid fa-file-pen"></i>
            </span>
            <div>
                <h2 class="text-lg font-extrabold text-brand-900">Nilai Kuis &amp; Ujian</h2>
                <p class="text-sm text-slate-500">Nilai pilihan ganda dihitung otomatis berdasarkan bobot soal.</p>
            </div>
        </div>

        <div>
            <label for="ujian_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Pilih Ujian</label>

            <select id="ujian_id" wire:model.live="ujian_id"
                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition md:max-w-md
                               focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                <option value="">Pilih kuis atau ujian</option>

                @foreach ($ujianList as $ujian)
                <option value="{{ $ujian->id }}">{{ $ujian->judul }}</option>
                @endforeach
            </select>
        </div>

        @if ($ujian_id)
        @php
        $totalHasil = $hasilUjian->count();
        $jmlSelesai = $hasilUjian->where('status', 'selesai')->count();
        $rataSkor = $hasilUjian->where('status', 'selesai')->whereNotNull('skor')->avg('skor');
        $skorTertinggi = $hasilUjian->where('status', 'selesai')->whereNotNull('skor')->max('skor');
        @endphp

        {{-- Ringkasan --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl bg-brand-50 px-4 py-3">
                <p class="text-2xl font-extrabold text-brand-700">{{ $totalHasil }}</p>
                <p class="text-xs font-medium text-brand-600">Peserta</p>
            </div>
            <div class="rounded-xl bg-brand-50 px-4 py-3">
                <p class="text-2xl font-extrabold text-brand-700">{{ $jmlSelesai }}<span class="text-sm font-semibold text-slate-400">/{{ $totalHasil }}</span></p>
                <p class="text-xs font-medium text-brand-600">Selesai</p>
            </div>
            <div class="rounded-xl bg-brand-50 px-4 py-3">
                <p class="text-2xl font-extrabold text-brand-700">{{ $rataSkor !== null ? $angka(round($rataSkor, 2)) : '-' }}</p>
                <p class="text-xs font-medium text-brand-600">Rata-rata skor</p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3">
                <p class="text-2xl font-extrabold text-slate-700">{{ $skorTertinggi !== null ? $angka($skorTertinggi) : '-' }}</p>
                <p class="text-xs font-medium text-slate-500">Skor tertinggi</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-brand-100">
            <table class="w-full text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="p-3 text-left">Siswa</th>
                        <th class="p-3 text-left">Skor</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Waktu Selesai</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($hasilUjian as $hasil)
                    @php
                    $namaSiswa = $hasil->siswa?->nama_lengkap
                    ?? $hasil->siswa?->user?->name
                    ?? 'Siswa tidak ditemukan';

                    $kelasStatus = $gayaStatusUjian[$hasil->status]
                    ?? 'bg-slate-100 text-slate-600';
                    @endphp

                    <tr wire:key="hasil-ujian-{{ $hasil->id }}" class="transition hover:bg-brand-50/40">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $avatar($namaSiswa) }}" alt="{{ $namaSiswa }}"
                                    class="h-9 w-9 shrink-0 rounded-full ring-2 ring-brand-100">
                                <span class="font-semibold text-slate-800">{{ $namaSiswa }}</span>
                            </div>
                        </td>

                        <td class="p-3 text-base font-extrabold text-brand-700">
                            {{ $hasil->skor !== null ? $angka($hasil->skor) : '-' }}
                        </td>

                        <td class="p-3">
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $kelasStatus }}">
                                {{ ucfirst(str_replace('_', ' ', $hasil->status ?? '-')) }}
                            </span>
                        </td>

                        <td class="p-3 text-slate-600">
                            {{ $hasil->tanggal_selesai ? \Carbon\Carbon::parse($hasil->tanggal_selesai)->translatedFormat('d M Y, H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-regular fa-folder-open"></i>
                            </span>
                            <p class="mt-3 font-semibold text-slate-700">Belum ada siswa yang mengerjakan ujian</p>
                            <p class="mt-1 text-sm text-slate-500">Hasil ujian siswa akan tampil di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    {{-- ================= TAB PRAKTIK ================= --}}
    @if ($tabAktif === 'praktik')
    <div class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-100 text-brand-600">
                <i class="fa-solid fa-flask"></i>
            </span>
            <div>
                <h2 class="text-lg font-extrabold text-brand-900">Penilaian Praktik</h2>
                <p class="text-sm text-slate-500">Penilaian praktik dilakukan secara manual oleh guru.</p>
            </div>
        </div>

        <div class="mt-5 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <i class="fa-solid fa-hammer mt-0.5 text-amber-500"></i>
            <p class="text-sm text-amber-800">
                Form input nilai praktik akan diaktifkan setelah struktur
                penyimpanan nilai praktik ditentukan.
            </p>
        </div>
    </div>
    @endif

    {{-- ================= TAB REKAP ================= --}}
    @if ($tabAktif === 'rekap')
    <div class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-100 text-brand-600">
                <i class="fa-solid fa-chart-column"></i>
            </span>
            <div>
                <h2 class="text-lg font-extrabold text-brand-900">Rekap Nilai</h2>
                <p class="text-sm text-slate-500">Rekap gabungan nilai tugas, kuis, ujian, dan praktik siswa.</p>
            </div>
        </div>

        <div class="mt-5 flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 p-4">
            <i class="fa-solid fa-circle-info mt-0.5 text-sky-500"></i>
            <p class="text-sm text-sky-800">
                Rekap keseluruhan akan dibuat setelah struktur nilai praktik
                dan aturan penggabungan nilai ditentukan.
            </p>
        </div>
    </div>
    @endif

    @endif
</div>