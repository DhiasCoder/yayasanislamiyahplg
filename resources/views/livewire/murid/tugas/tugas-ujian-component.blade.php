@php
$fmt = fn ($tgl, $format = 'd M Y, H:i') => $tgl ? \Carbon\Carbon::parse($tgl)->translatedFormat($format) : '-';

// Rapikan angka desimal dari database: 85.00 -> 85, 70.50 -> 70,5
$angka = fn ($n) => is_numeric($n)
? rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',')
: $n;

// Gaya status tugas: [kelas badge, ikon, warna aksen kartu]
$gayaTugas = [
'Dinilai' => ['bg-brand-100 text-brand-700', 'fa-circle-check', 'bg-brand-500'],
'Sudah Dikumpulkan' => ['bg-sky-100 text-sky-700', 'fa-paper-plane', 'bg-sky-400'],
'Ditutup' => ['bg-red-100 text-red-700', 'fa-lock', 'bg-red-400'],
'Belum Dikumpulkan' => ['bg-amber-100 text-amber-700', 'fa-hourglass-half', 'bg-amber-400'],
];

// Gaya status ujian: [kelas badge, ikon, warna aksen kartu]
$gayaUjian = [
'Selesai Dikerjakan' => ['bg-brand-100 text-brand-700', 'fa-circle-check', 'bg-brand-500'],
'Sedang Mengerjakan' => ['bg-sky-100 text-sky-700', 'fa-pen', 'bg-sky-400'],
'Belum Dimulai' => ['bg-slate-100 text-slate-600', 'fa-clock', 'bg-slate-300'],
'Waktu Berakhir' => ['bg-red-100 text-red-700', 'fa-hourglass-end', 'bg-red-400'],
'Jadwal Berlangsung' => ['bg-amber-100 text-amber-700', 'fa-bolt', 'bg-amber-400'],
];

$statusUjian = function ($hasil, $belumMulai, $sudahSelesai) {
if ($hasil && $hasil->status === 'selesai') return 'Selesai Dikerjakan';
if ($hasil && $hasil->status === 'sedang_mengerjakan') return 'Sedang Mengerjakan';
if ($belumMulai) return 'Belum Dimulai';
if ($sudahSelesai) return 'Waktu Berakhir';
return 'Jadwal Berlangsung';
};

// Ringkasan untuk banner & tab
$perluDikerjakan = $tugas->filter(function ($t) {
$tutup = $t->deadline && now()->gt($t->deadline) && ! $t->boleh_terlambat;
return $t->pengumpulan->isEmpty() && ! $tutup;
})->count();

$ujianTersedia = $ujians->filter(function ($u) {
$mulai = ! $u->tanggal_mulai || now()->gte($u->tanggal_mulai);
$belumHabis = ! $u->tanggal_selesai || now()->lte($u->tanggal_selesai);
return $u->results->isEmpty() && $mulai && $belumHabis;
})->count();

// Gaya baris rincian jawaban
$gayaRincian = [
'benar' => ['bg-brand-500 text-white', 'bg-brand-100 text-brand-700', 'Benar', 'fa-check'],
'salah' => ['bg-red-500 text-white', 'bg-red-100 text-red-700', 'Salah', 'fa-xmark'],
'dijawab' => ['bg-sky-500 text-white', 'bg-sky-100 text-sky-700', 'Terjawab', 'fa-pen'],
'kosong' => ['bg-slate-300 text-white', 'bg-slate-100 text-slate-500', 'Tidak dijawab', 'fa-minus'],
];
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Tugas &amp; Ujian</h1>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Kerjakan tugas dan ujian sesuai jadwal yang ditetapkan guru.
                </p>
            </div>

            <div class="flex gap-3">
                <div class="rounded-2xl bg-white/15 px-5 py-3 backdrop-blur">
                    <p class="text-2xl font-extrabold">{{ $perluDikerjakan }}</p>
                    <p class="text-xs text-brand-50">Tugas perlu dikerjakan</p>
                </div>
                <div class="rounded-2xl bg-white/15 px-5 py-3 backdrop-blur">
                    <p class="text-2xl font-extrabold">{{ $ujianTersedia }}</p>
                    <p class="text-xs text-brand-50">Ujian tersedia</p>
                </div>
            </div>
        </div>
    </section>

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

    {{-- ================= TAB ================= --}}
    <div class="inline-flex w-full rounded-2xl bg-brand-100/60 p-1.5 sm:w-auto">
        <button wire:click="pilihTab('tugas')"
            class="flex flex-1 items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition sm:flex-none
                {{ $tab === 'tugas' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-brand-700' }}">
            <i class="fa-solid fa-list-check"></i>
            Tugas
            <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $perluDikerjakan > 0 ? 'bg-amber-100 text-amber-700' : 'bg-brand-100 text-brand-700' }}">
                {{ $perluDikerjakan > 0 ? $perluDikerjakan : $tugas->count() }}
            </span>
        </button>

        <button wire:click="pilihTab('ujian')"
            class="flex flex-1 items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition sm:flex-none
                {{ $tab === 'ujian' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-brand-700' }}">
            <i class="fa-solid fa-file-pen"></i>
            Ujian
            <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $ujianTersedia > 0 ? 'bg-amber-100 text-amber-700' : 'bg-brand-100 text-brand-700' }}">
                {{ $ujianTersedia > 0 ? $ujianTersedia : $ujians->count() }}
            </span>
        </button>
    </div>

    {{-- ================= TAB TUGAS ================= --}}
    @if ($tab === 'tugas')

    <div class="space-y-4">

        @forelse ($tugas as $item)

        @php
        $pengumpulan = $item->pengumpulan->first();
        $deadline = $item->deadline ? \Carbon\Carbon::parse($item->deadline) : null;
        $terlambat = $deadline && now()->gt($deadline);

        $status = $pengumpulan
        ? ($pengumpulan->nilai !== null ? 'Dinilai' : 'Sudah Dikumpulkan')
        : (($terlambat && ! $item->boleh_terlambat) ? 'Ditutup' : 'Belum Dikumpulkan');

        [$kelasStatus, $ikonStatus, $aksen] = $gayaTugas[$status];
        @endphp

        <div wire:key="tugas-{{ $item->id }}"
            class="relative overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm transition hover:border-brand-300 hover:shadow-md hover:shadow-brand-100">
            <span class="absolute inset-y-0 left-0 w-1.5 {{ $aksen }}"></span>

            <div class="p-5 pl-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row">

                    <div class="min-w-0 space-y-2">
                        <h3 class="font-bold text-brand-900">{{ $item->judul }}</h3>

                        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-book text-xs text-brand-400"></i>
                                {{ $item->pengajaran->mataPelajaran->nama ?? 'Mata pelajaran' }}
                            </span>
                            <span class="text-brand-300">·</span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-chalkboard-user text-xs text-brand-400"></i>
                                {{ $item->pengajaran->guru->nama_lengkap ?? 'Guru' }}
                            </span>
                        </p>

                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-400">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar"></i>
                                Mulai: {{ $fmt($item->tanggal_mulai) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 {{ $status === 'Belum Dikumpulkan' && $deadline ? 'font-semibold text-amber-600' : '' }}">
                                <i class="fa-regular fa-clock"></i>
                                Deadline: {{ $fmt($item->deadline) }}
                                @if ($deadline && ! $pengumpulan)
                                <span>({{ $deadline->diffForHumans() }})</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-row items-center justify-between gap-3 sm:flex-col sm:items-end sm:justify-start">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $kelasStatus }}">
                            <i class="fa-solid {{ $ikonStatus }} text-[10px]"></i>
                            {{ $status }}
                        </span>

                        <button wire:click="lihatTugas({{ $item->id }})"
                            class="inline-flex items-center gap-2 rounded-xl bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-100">
                            Lihat Detail
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </div>

                @if ($pengumpulan && $pengumpulan->nilai !== null)
                <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-xl bg-brand-50 p-4">
                    <div>
                        <p class="text-xs font-semibold text-brand-600">Nilai tugas</p>
                        <p class="text-2xl font-extrabold text-brand-700">
                            {{ $pengumpulan->nilai }}
                            <span class="text-xs font-medium text-brand-600">/ {{ $item->nilai_maksimal }}</span>
                        </p>
                    </div>

                    @if ($pengumpulan->feedback_guru)
                    <p class="min-w-0 flex-1 text-sm text-brand-800">
                        <span class="font-semibold">Feedback:</span> {{ $pengumpulan->feedback_guru }}
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        @empty
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
                <i class="fa-solid fa-list-check"></i>
            </span>
            <p class="mt-4 font-semibold text-brand-900">Belum ada tugas untuk kelasmu</p>
            <p class="mt-1 text-sm text-slate-500">Tugas dari guru akan tampil di sini.</p>
        </div>
        @endforelse

    </div>

    @endif

    {{-- ================= TAB UJIAN ================= --}}
    @if ($tab === 'ujian')

    <div class="space-y-4">

        @forelse ($ujians as $ujian)

        @php
        $hasil = $ujian->results->first();
        $belumMulai = $ujian->tanggal_mulai && now()->lt($ujian->tanggal_mulai);
        $sudahSelesai = $ujian->tanggal_selesai && now()->gt($ujian->tanggal_selesai);

        $statusU = $statusUjian($hasil, $belumMulai, $sudahSelesai);
        [$kelasStatusU, $ikonStatusU, $aksenU] = $gayaUjian[$statusU];
        @endphp

        <div wire:key="ujian-{{ $ujian->id }}"
            class="relative overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm transition hover:border-brand-300 hover:shadow-md hover:shadow-brand-100">
            <span class="absolute inset-y-0 left-0 w-1.5 {{ $aksenU }}"></span>

            <div class="p-5 pl-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row">

                    <div class="min-w-0 space-y-2">
                        <h3 class="font-bold text-brand-900">{{ $ujian->judul }}</h3>

                        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-book text-xs text-brand-400"></i>
                                {{ $ujian->pengajaran->mataPelajaran->nama ?? 'Mata pelajaran' }}
                            </span>
                            <span class="text-brand-300">·</span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-chalkboard-user text-xs text-brand-400"></i>
                                {{ $ujian->pengajaran->guru->nama_lengkap ?? 'Guru' }}
                            </span>
                        </p>

                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full bg-brand-50 px-2.5 py-1 font-semibold text-brand-700">{{ strtoupper($ujian->jenis) }}</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-600">
                                <i class="fa-regular fa-clock"></i> {{ $ujian->durasi }} menit
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-400">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar"></i>
                                Mulai: {{ $fmt($ujian->tanggal_mulai) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar-check"></i>
                                Selesai: {{ $fmt($ujian->tanggal_selesai) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-row items-center justify-between gap-3 sm:flex-col sm:items-end sm:justify-start">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $kelasStatusU }}">
                            <i class="fa-solid {{ $ikonStatusU }} text-[10px]"></i>
                            {{ $statusU }}
                        </span>

                        <button wire:click="lihatUjian({{ $ujian->id }})"
                            class="inline-flex items-center gap-2 rounded-xl bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-100">
                            Lihat Detail
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </div>

                @if ($hasil && $hasil->status === 'selesai')
                <div class="mt-4 flex items-center gap-4 rounded-xl bg-brand-50 p-4">
                    <div>
                        <p class="text-xs font-semibold text-brand-600">Nilai ujian</p>
                        <p class="text-2xl font-extrabold text-brand-700">{{ $hasil->skor !== null ? $angka($hasil->skor) : 'Belum dinilai' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @empty
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-14 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
                <i class="fa-solid fa-file-pen"></i>
            </span>
            <p class="mt-4 font-semibold text-brand-900">Belum ada ujian untuk kelasmu</p>
            <p class="mt-1 text-sm text-slate-500">Jadwal ujian dari guru akan tampil di sini.</p>
        </div>
        @endforelse

    </div>

    @endif


    {{-- ================= MODAL DETAIL TUGAS ================= --}}
    @if ($tugasDetail)

    @php
    $pengumpulanDetail = $tugasDetail->pengumpulan->first();

    $tugasDitutup = $tugasDetail->deadline
    && now()->gt($tugasDetail->deadline)
    && ! $tugasDetail->boleh_terlambat;

    $tugasBelumMulai = $tugasDetail->tanggal_mulai
    && now()->lt($tugasDetail->tanggal_mulai);
    @endphp

    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.tutupDetail()"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="tutupDetail"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50 to-white px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                        <i class="fa-solid fa-list-check"></i>
                    </span>
                    <h2 class="text-lg font-extrabold text-brand-900">Detail Tugas</h2>
                </div>

                <button wire:click="tutupDetail" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-slate-400 transition hover:bg-brand-50 hover:text-brand-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Isi --}}
            <div class="space-y-5 overflow-y-auto p-6">

                <div>
                    <h3 class="text-xl font-extrabold text-brand-900">{{ $tugasDetail->judul }}</h3>
                    <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                        <i class="fa-solid fa-book text-xs text-brand-400"></i>
                        {{ $tugasDetail->pengajaran->mataPelajaran->nama ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-brand-50/60 p-4">
                    <p class="mb-2 text-sm font-bold text-brand-800">Deskripsi</p>
                    <p class="whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $tugasDetail->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                    <div class="rounded-xl border border-brand-100 p-3">
                        <p class="text-xs text-slate-400">Mulai</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $fmt($tugasDetail->tanggal_mulai) }}</p>
                    </div>

                    <div class="rounded-xl border border-brand-100 p-3">
                        <p class="text-xs text-slate-400">Deadline</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $fmt($tugasDetail->deadline) }}</p>
                    </div>

                    <div class="col-span-2 rounded-xl border border-brand-100 p-3 sm:col-span-1">
                        <p class="text-xs text-slate-400">Nilai Maksimal</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $tugasDetail->nilai_maksimal }}</p>
                    </div>
                </div>

                @if ($tugasDetail->file_soal)
                <a href="{{ route('murid.tugas.file-soal', $tugasDetail->id) }}" target="_blank"
                    class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    Lihat / Unduh File Soal
                </a>
                @endif

                <div class="border-t border-brand-100 pt-5">
                    <h4 class="mb-3 flex items-center gap-2 font-extrabold text-brand-900">
                        <i class="fa-solid fa-paper-plane text-brand-500"></i>
                        Pengumpulan Jawaban
                    </h4>

                    @if ($pengumpulanDetail)
                    <div class="mb-4 rounded-2xl border border-brand-100 bg-brand-50/50 p-4 text-sm">
                        <p class="flex items-center gap-2 font-bold text-brand-800">
                            <i class="fa-solid fa-circle-check text-brand-500"></i>
                            Jawaban sudah dikumpulkan
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $fmt($pengumpulanDetail->tanggal_pengumpulan) }}
                        </p>

                        <p class="mt-2 text-slate-700">Status: {{ ucfirst($pengumpulanDetail->status) }}</p>

                        @if ($pengumpulanDetail->nilai !== null)
                        <p class="mt-2 font-extrabold text-brand-700">Nilai: {{ $pengumpulanDetail->nilai }}</p>
                        @endif

                        @if ($pengumpulanDetail->feedback_guru)
                        <p class="mt-2 text-slate-600">Feedback: {{ $pengumpulanDetail->feedback_guru }}</p>
                        @endif
                    </div>
                    @endif

                    @if (! $pengumpulanDetail || $pengumpulanDetail->nilai === null)

                    @if (! $tugasDitutup && ! $tugasBelumMulai)

                    <form wire:submit="kumpulkanTugas" class="space-y-4">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">File Jawaban</label>

                            <input type="file" wire:model="fileJawaban"
                                accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png"
                                class="w-full rounded-xl border border-dashed border-brand-300 bg-brand-50/40 p-2.5 text-sm text-slate-600
                                                   file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-brand-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-600">

                            <p class="mt-1 text-xs text-slate-400">PDF, DOC, DOCX, ZIP, JPG, atau PNG · maksimal 10 MB.</p>

                            @error('fileJawaban')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror

                            <div wire:loading wire:target="fileJawaban" class="mt-1 flex items-center gap-2 text-xs text-brand-600">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> Mengunggah file...
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Catatan untuk Guru (Opsional)</label>

                            <textarea wire:model="catatanSiswa" rows="3"
                                placeholder="Tambahkan catatan..."
                                class="w-full resize-none rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></textarea>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="kumpulkanTugas,fileJawaban"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60">
                            <span wire:loading.remove wire:target="kumpulkanTugas">
                                <i class="fa-solid fa-paper-plane mr-1 text-xs"></i>
                                {{ $pengumpulanDetail ? 'Kirim Ulang Jawaban' : 'Kumpulkan Tugas' }}
                            </span>
                            <span wire:loading wire:target="kumpulkanTugas">
                                <i class="fa-solid fa-circle-notch fa-spin mr-1 text-xs"></i> Mengirim...
                            </span>
                        </button>

                    </form>

                    @elseif ($tugasBelumMulai)
                    <p class="flex items-center gap-2 rounded-xl bg-sky-50 px-4 py-3 text-sm text-sky-700">
                        <i class="fa-solid fa-clock"></i>
                        Tugas belum dapat dikumpulkan karena jadwal belum dimulai.
                    </p>
                    @else
                    <p class="flex items-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600">
                        <i class="fa-solid fa-lock"></i>
                        Batas pengumpulan tugas sudah berakhir.
                    </p>
                    @endif

                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex shrink-0 justify-end border-t border-brand-100 bg-white px-6 py-4">
                <button wire:click="tutupDetail"
                    class="rounded-xl border border-brand-200 px-5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= MODAL DETAIL UJIAN ================= --}}
    @if ($ujianDetail)

    @php
    $hasilUjian = $ujianDetail->results->first();

    $belumMulai = $ujianDetail->tanggal_mulai && now()->lt($ujianDetail->tanggal_mulai);
    $sudahSelesai = $ujianDetail->tanggal_selesai && now()->gt($ujianDetail->tanggal_selesai);

    $jumlahSoal = $ujianDetail->questions->count();
    $selesaiUjian = $hasilUjian && $hasilUjian->status === 'selesai';

    $statusD = $statusUjian($hasilUjian, $belumMulai, $sudahSelesai);
    [$kelasStatusD, $ikonStatusD] = $gayaUjian[$statusD];

    // Tuntas / belum tuntas terhadap nilai minimal
    $skor = $hasilUjian?->skor;
    $tuntas = ($selesaiUjian && is_numeric($skor) && is_numeric($ujianDetail->nilai_minimal))
    ? $skor >= $ujianDetail->nilai_minimal
    : null;
    @endphp

    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.tutupDetail()"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="tutupDetail"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-3xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">

            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50 to-white px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                        <i class="fa-solid fa-file-pen"></i>
                    </span>
                    <div>
                        <h2 class="text-lg font-extrabold text-brand-900">Detail Ujian</h2>
                        <p class="text-xs text-slate-500">Informasi, jadwal, dan hasil ujian.</p>
                    </div>
                </div>

                <button wire:click="tutupDetail" aria-label="Tutup"
                    class="grid h-9 w-9 place-items-center rounded-lg text-slate-400 transition hover:bg-brand-50 hover:text-brand-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Isi --}}
            <div class="space-y-5 overflow-y-auto p-6">

                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-xl font-extrabold text-brand-900">{{ $ujianDetail->judul }}</h3>
                        <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                            <i class="fa-solid fa-book text-xs text-brand-400"></i>
                            {{ $ujianDetail->pengajaran->mataPelajaran->nama ?? 'Mata Pelajaran' }}
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $kelasStatusD }}">
                        <i class="fa-solid {{ $ikonStatusD }} text-[10px]"></i>
                        {{ $statusD }}
                    </span>
                </div>

                <div class="rounded-2xl bg-brand-50/60 p-4">
                    <p class="mb-2 text-sm font-bold text-brand-800">Deskripsi Ujian</p>
                    <p class="whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $ujianDetail->deskripsi ?: 'Tidak ada deskripsi ujian.' }}</p>
                </div>

                {{-- Info jadwal --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-brand-100 p-4">
                        <p class="text-xs text-slate-400">Jenis Ujian</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ strtoupper($ujianDetail->jenis) }}</p>
                    </div>

                    <div class="rounded-xl border border-brand-100 p-4">
                        <p class="text-xs text-slate-400">Durasi</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $ujianDetail->durasi }} Menit</p>
                    </div>

                    <div class="rounded-xl border border-brand-100 p-4">
                        <p class="text-xs text-slate-400">Jumlah Soal</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $jumlahSoal }} Soal</p>
                    </div>

                    <div class="rounded-xl border border-brand-100 p-4">
                        <p class="text-xs text-slate-400">Tanggal Mulai</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $fmt($ujianDetail->tanggal_mulai) }}</p>
                    </div>

                    <div class="rounded-xl border border-brand-100 p-4">
                        <p class="text-xs text-slate-400">Tanggal Selesai</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $fmt($ujianDetail->tanggal_selesai) }}</p>
                    </div>

                    <div class="rounded-xl border border-brand-100 p-4">
                        <p class="text-xs text-slate-400">Nilai Minimal</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $angka($ujianDetail->nilai_minimal) }}</p>
                    </div>
                </div>

                {{-- ============ HASIL UJIAN ============ --}}
                @if ($selesaiUjian)
                <div class="overflow-hidden rounded-2xl border border-brand-200">

                    <div class="flex flex-wrap items-center justify-between gap-4 bg-gradient-to-br from-brand-500 to-brand-600 p-5 text-white">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-brand-100">Nilai yang diperoleh</p>
                            <p class="mt-1 text-4xl font-extrabold">{{ $skor !== null ? $angka($skor) : '-' }}</p>
                        </div>

                        @if ($tuntas !== null)
                        <div class="rounded-2xl bg-white/15 px-4 py-2.5 text-center backdrop-blur">
                            <p class="flex items-center justify-center gap-2 font-extrabold">
                                <i class="fa-solid {{ $tuntas ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                                {{ $tuntas ? 'Tuntas' : 'Belum Tuntas' }}
                            </p>
                            <p class="text-xs text-brand-50">Nilai minimal {{ $angka($ujianDetail->nilai_minimal) }}</p>
                        </div>
                        @endif
                    </div>

                    @if ($rincian && ($rincian['tersedia'] ?? false))
                    @php $r = $rincian['ringkasan']; @endphp

                    {{-- Ringkasan jawaban --}}
                    <div class="grid grid-cols-2 gap-px bg-brand-100 sm:grid-cols-{{ $rincian['adaPenilaian'] ? 4 : 3 }}">
                        <div class="bg-white px-4 py-3 text-center">
                            <p class="text-xl font-extrabold text-brand-700">{{ $r['dijawab'] }}<span class="text-sm font-semibold text-slate-400">/{{ $r['total'] }}</span></p>
                            <p class="text-[11px] font-medium text-slate-500">Soal dijawab</p>
                        </div>

                        <div class="bg-white px-4 py-3 text-center">
                            <p class="text-xl font-extrabold {{ $r['kosong'] > 0 ? 'text-amber-600' : 'text-slate-400' }}">{{ $r['kosong'] }}</p>
                            <p class="text-[11px] font-medium text-slate-500">Tidak dijawab</p>
                        </div>

                        @if ($rincian['adaPenilaian'])
                        <div class="bg-white px-4 py-3 text-center">
                            <p class="text-xl font-extrabold text-brand-600">{{ $r['benar'] }}</p>
                            <p class="text-[11px] font-medium text-slate-500">Jawaban benar</p>
                        </div>

                        <div class="bg-white px-4 py-3 text-center">
                            <p class="text-xl font-extrabold text-red-500">{{ $r['salah'] }}</p>
                            <p class="text-[11px] font-medium text-slate-500">Jawaban salah</p>
                        </div>
                        @else
                        <div class="col-span-2 bg-white px-4 py-3 text-center sm:col-span-1">
                            <p class="text-xl font-extrabold text-slate-700">{{ $r['total'] }}</p>
                            <p class="text-[11px] font-medium text-slate-500">Total soal</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- ============ RINCIAN JAWABAN ============ --}}
                @if ($rincian && ($rincian['tersedia'] ?? false))
                <div>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h4 class="flex items-center gap-2 font-extrabold text-brand-900">
                            <i class="fa-solid fa-clipboard-list text-brand-500"></i>
                            Rincian Jawaban
                        </h4>
                        <span class="text-xs text-slate-400">Klik soal untuk melihat detail</span>
                    </div>

                    <div class="space-y-2">
                        @foreach ($rincian['soal'] as $s)
                        @php [$kelasNo, $kelasLabel, $labelStatus, $ikonRincian] = $gayaRincian[$s['status']]; @endphp

                        <details wire:key="rincian-{{ $s['no'] }}"
                            class="group overflow-hidden rounded-xl border border-brand-100 bg-white transition open:border-brand-300 open:shadow-sm">
                            <summary class="flex cursor-pointer list-none items-center gap-3 p-3.5 transition hover:bg-brand-50/50 [&::-webkit-details-marker]:hidden">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-sm font-extrabold {{ $kelasNo }}">
                                    {{ $s['no'] }}
                                </span>

                                <span class="min-w-0 flex-1 truncate text-sm font-medium text-slate-700">{{ $s['pertanyaan'] }}</span>

                                @if ($s['skor'] !== null && $s['bobot'] !== null)
                                <span class="hidden shrink-0 text-xs font-bold text-slate-500 md:inline">
                                    {{ $angka($s['skor']) }}<span class="font-medium text-slate-400">/{{ $angka($s['bobot']) }}</span>
                                </span>
                                @endif

                                <span class="hidden shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold sm:inline-flex {{ $kelasLabel }}">
                                    <i class="fa-solid {{ $ikonRincian }} text-[9px]"></i>
                                    {{ $labelStatus }}
                                </span>

                                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition group-open:rotate-180"></i>
                            </summary>

                            <div class="space-y-3 border-t border-brand-100 bg-brand-50/30 p-4 text-sm">
                                {{-- Status untuk layar kecil --}}
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold sm:hidden {{ $kelasLabel }}">
                                    <i class="fa-solid {{ $ikonRincian }} text-[9px]"></i>
                                    {{ $labelStatus }}
                                </span>

                                <p class="whitespace-pre-line font-medium leading-relaxed text-slate-800">{{ $s['pertanyaan'] }}</p>

                                {{-- Poin soal --}}
                                @if ($s['bobot'] !== null)
                                <p class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-1.5 text-xs text-slate-500 ring-1 ring-brand-100">
                                    <i class="fa-solid fa-star text-amber-400"></i>
                                    @if ($s['skor'] !== null)
                                    Poin:
                                    <span class="font-extrabold text-brand-700">{{ $angka($s['skor']) }}</span>
                                    dari {{ $angka($s['bobot']) }}
                                    @elseif ($s['status'] === 'dijawab')
                                    Bobot {{ $angka($s['bobot']) }} poin · menunggu penilaian guru
                                    @else
                                    Bobot {{ $angka($s['bobot']) }} poin
                                    @endif
                                </p>
                                @endif

                                @if ($s['tipe'] === 'pilihan_ganda' && count($s['opsi']))
                                <div class="space-y-2">
                                    @foreach ($s['opsi'] as $huruf => $teksOpsi)
                                    @php
                                    $dipilih = $s['huruf'] === $huruf;
                                    $adalahKunci = $s['kunciHuruf'] === $huruf;

                                    $kelasOpsi = match (true) {
                                    $dipilih && $s['status'] === 'benar' => 'border-brand-400 bg-brand-100/70',
                                    $dipilih && $s['status'] === 'salah' => 'border-red-300 bg-red-50',
                                    $dipilih => 'border-sky-300 bg-sky-50',
                                    $adalahKunci => 'border-brand-300 bg-brand-50',
                                    default => 'border-slate-200 bg-white',
                                    };

                                    $kelasHuruf = match (true) {
                                    $dipilih && $s['status'] === 'benar' => 'bg-brand-500 text-white',
                                    $dipilih && $s['status'] === 'salah' => 'bg-red-500 text-white',
                                    $dipilih => 'bg-sky-500 text-white',
                                    $adalahKunci => 'bg-brand-500 text-white',
                                    default => 'bg-slate-100 text-slate-500',
                                    };
                                    @endphp

                                    <div class="flex items-start gap-3 rounded-lg border p-3 {{ $kelasOpsi }}">
                                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md text-xs font-extrabold uppercase {{ $kelasHuruf }}">{{ $huruf }}</span>
                                        <span class="flex-1 pt-0.5 text-slate-700">{{ $teksOpsi }}</span>

                                        @if ($dipilih)
                                        <span class="shrink-0 rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">Jawabanmu</span>
                                        @elseif ($adalahKunci)
                                        <span class="shrink-0 rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-brand-700 ring-1 ring-brand-200">Kunci</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>

                                {{-- Jawaban tersimpan tetapi tidak cocok dengan opsi yang ada --}}
                                @if ($s['jawaban'] && ! $s['huruf'])
                                <p class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-slate-700">
                                    <span class="font-semibold">Jawabanmu:</span> {{ $s['jawaban'] }}
                                </p>
                                @endif
                                @else
                                <div>
                                    <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">Jawabanmu</p>
                                    @if ($s['jawaban'])
                                    <p class="whitespace-pre-line rounded-lg border border-sky-200 bg-white px-3 py-2.5 leading-relaxed text-slate-700">{{ $s['jawaban'] }}</p>
                                    @else
                                    <p class="rounded-lg border border-dashed border-slate-200 bg-white px-3 py-2.5 text-slate-400">Tidak dijawab.</p>
                                    @endif
                                </div>
                                @endif

                                @if ($s['status'] === 'kosong' && $s['tipe'] === 'pilihan_ganda')
                                <p class="text-xs text-slate-400">Kamu tidak memilih jawaban untuk soal ini.</p>
                                @endif
                            </div>
                        </details>
                        @endforeach
                    </div>
                </div>
                @elseif ($rincian && ! ($rincian['tersedia'] ?? true))
                <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                    <i class="fa-solid fa-circle-info mt-0.5 text-slate-400"></i>
                    Rincian jawaban belum tersedia untuk ujian ini.
                </div>
                @endif

                @elseif ($hasilUjian)
                <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
                    <i class="fa-solid fa-pen"></i>
                    Kamu sedang mengerjakan ujian ini. Nilai dan rincian jawaban tampil setelah ujian selesai.
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex shrink-0 flex-wrap justify-end gap-3 border-t border-brand-100 bg-white px-6 py-4">
                <button wire:click="tutupDetail"
                    class="rounded-xl border border-brand-200 px-5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                    Tutup
                </button>

                @if (! $hasilUjian && ! $belumMulai && ! $sudahSelesai)
                <a href="{{ route('murid.ujian.kerjakan', $ujianDetail->id) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:from-brand-600 hover:to-brand-700">
                    <i class="fa-solid fa-play text-xs"></i>
                    Mulai Ujian
                </a>
                @elseif ($hasilUjian && $hasilUjian->status === 'sedang_mengerjakan')
                <a href="{{ route('murid.ujian.kerjakan', $ujianDetail->id) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:from-brand-600 hover:to-brand-700">
                    <i class="fa-solid fa-forward text-xs"></i>
                    Lanjutkan Ujian
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>