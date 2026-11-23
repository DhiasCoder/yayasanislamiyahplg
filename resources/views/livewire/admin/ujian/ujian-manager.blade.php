{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Manajemen Ujian</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Kelola jadwal ujian, bank soal, dan pantau hasil pengerjaan siswa secara terpusat.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                        <i class="fa-solid fa-file-signature"></i>
                        {{ $ujians->total() }} ujian terdaftar
                    </span>

                    @if ($ujianTerpilih)
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/20 px-3.5 py-1.5 text-xs font-semibold backdrop-blur">
                        <i class="fa-solid fa-bookmark"></i>
                        Aktif: {{ \Illuminate\Support\Str::limit($ujianTerpilih->judul, 35) }}
                    </span>
                    @endif
                </div>
            </div>

            @if ($tab === 'ujian')
            <button type="button" wire:click="resetUjianForm"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Ujian
            </button>
            @elseif ($tab === 'soal' && $ujianTerpilih)
            <button type="button" wire:click="resetQuestionForm"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Soal
            </button>
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

    @if (session()->has('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" @click="show = false" class="text-red-400 transition hover:text-red-600" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    {{-- ================= NAVIGASI TAB ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-2 shadow-sm">
        <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-3">
            <button type="button" wire:click="gantiTab('ujian')"
                @class([ 'flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition' , 'bg-brand-600 text-white shadow-sm shadow-brand-500/20'=> $tab === 'ujian',
                'text-slate-600 hover:bg-brand-50 hover:text-brand-700' => $tab !== 'ujian',
                ])>
                <i class="fa-solid fa-file-lines text-xs"></i>
                <span>Daftar Ujian</span>
            </button>

            <button type="button" wire:click="gantiTab('soal')"
                @class([ 'flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition' , 'bg-brand-600 text-white shadow-sm shadow-brand-500/20'=> $tab === 'soal',
                'text-slate-600 hover:bg-brand-50 hover:text-brand-700' => $tab !== 'soal',
                ])>
                <i class="fa-solid fa-list-check text-xs"></i>
                <span>Kelola Soal</span>
                @if ($ujianTerpilih)
                <span @class([ 'rounded-full px-2 py-0.5 text-[11px] font-extrabold' , 'bg-white/20 text-white'=> $tab === 'soal',
                    'bg-brand-100 text-brand-700' => $tab !== 'soal',
                    ])>
                    {{ $soals->count() }}
                </span>
                @endif
            </button>

            <button type="button" wire:click="gantiTab('hasil')"
                @class([ 'flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition' , 'bg-brand-600 text-white shadow-sm shadow-brand-500/20'=> $tab === 'hasil',
                'text-slate-600 hover:bg-brand-50 hover:text-brand-700' => $tab !== 'hasil',
                ])>
                <i class="fa-solid fa-chart-column text-xs"></i>
                <span>Hasil Siswa</span>
            </button>
        </div>
    </div>

    {{-- ================= TAB 1: DAFTAR UJIAN ================= --}}
    @if ($tab === 'ujian')
    <div class="space-y-5">
        {{-- Filter Pencarian --}}
        <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="relative sm:max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Cari judul ujian atau mata pelajaran..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                               placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>
        </div>

        {{-- Tabel Ujian --}}
        <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[960px] text-left text-sm">
                    <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                        <tr>
                            <th class="px-5 py-3.5">Ujian</th>
                            <th class="px-5 py-3.5">Mata Pelajaran</th>
                            <th class="px-5 py-3.5">Kelas</th>
                            <th class="px-5 py-3.5 text-center">Soal</th>
                            <th class="px-5 py-3.5 text-center">Hasil</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @forelse ($ujians as $ujian)
                        <tr wire:key="ujian-{{ $ujian->id }}" class="transition hover:bg-brand-50/40">
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                                        <i class="fa-solid fa-file-pen"></i>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800">
                                            {{ $ujian->judul }}
                                        </p>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                            <span class="rounded-md bg-slate-100 px-2 py-0.5 font-semibold uppercase text-slate-700">
                                                {{ $ujian->jenis }}
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fa-regular fa-clock text-brand-500"></i>
                                                {{ $ujian->durasi }} menit
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-700">
                                {{ $ujian->pengajaran?->mataPelajaran?->nama ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($ujian->pengajaran?->kelas)
                                <span class="inline-block rounded-lg border border-brand-200 bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">
                                    {{ $ujian->pengajaran->kelas->nama_kelas }}
                                </span>
                                @else
                                <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">
                                    {{ $ujian->questions_count }} Soal
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700">
                                    {{ $ujian->results_count }} Siswa
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                @if ($ujian->status === 'published')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                                    Published
                                </span>
                                @elseif ($ujian->status === 'closed')
                                <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Closed
                                </span>
                                @else
                                <span class="inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    {{ ucfirst($ujian->status) }}
                                </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" wire:click="pilihUjian({{ $ujian->id }}, 'soal')"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-brand-50/70 px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-100">
                                        <i class="fa-solid fa-list-check text-[11px]"></i>
                                        Soal
                                    </button>

                                    <button type="button" wire:click="pilihUjian({{ $ujian->id }}, 'hasil')"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50/70 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">
                                        <i class="fa-solid fa-chart-simple text-[11px]"></i>
                                        Hasil
                                    </button>

                                    <button type="button" wire:click="editUjian({{ $ujian->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-white px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-50">
                                        <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                        Edit
                                    </button>

                                    <button type="button"
                                        wire:click="hapusUjian({{ $ujian->id }})"
                                        wire:confirm="Yakin ingin menghapus ujian ini?"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                    <i class="fa-solid fa-file-signature"></i>
                                </span>
                                <p class="mt-4 font-bold text-brand-900">Belum ada data ujian</p>
                                <p class="mt-1 text-sm text-slate-500">Klik tombol "Tambah Ujian" untuk membuat jadwal ujian baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $ujians->links() }}</div>
    </div>
    @endif

    {{-- ================= TAB 2: KELOLA SOAL ================= --}}
    @if ($tab === 'soal')
    <div>
        @if ($ujianTerpilih)
        {{-- Info Ujian Terpilih --}}
        <div class="mb-5 flex flex-col justify-between gap-4 rounded-2xl border border-brand-200 bg-gradient-to-r from-brand-50 to-white p-5 shadow-sm sm:flex-row sm:items-center">
            <div class="flex items-start gap-3.5">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-700">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-brand-600">Bank Soal Ujian</p>
                    <h2 class="text-lg font-extrabold text-slate-800">
                        {{ $ujianTerpilih->judul }}
                    </h2>
                    <p class="mt-1 text-xs font-medium text-slate-500">
                        {{ $ujianTerpilih->pengajaran?->mataPelajaran?->nama ?? '-' }}
                        <span class="mx-1.5 text-slate-300">·</span>
                        Kelas {{ $ujianTerpilih->pengajaran?->kelas?->nama_kelas ?? '-' }}
                        <span class="mx-1.5 text-slate-300">·</span>
                        <strong class="text-brand-700">{{ $soals->count() }} soal</strong>
                    </p>
                </div>
            </div>

            <button type="button" wire:click="resetQuestionForm"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-xl bg-brand-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 sm:self-auto">
                <i class="fa-solid fa-plus"></i>
                Tambah Soal Baru
            </button>
        </div>

        {{-- Daftar Soal --}}
        <div class="space-y-4">
            @forelse ($soals as $index => $soal)
            <div wire:key="soal-{{ $soal->id }}"
                class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm transition hover:border-brand-200">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-2.5 py-1 text-xs font-extrabold text-white">
                                Soal #{{ $index + 1 }}
                            </span>

                            <span @class([ 'rounded-full px-2.5 py-1 text-xs font-semibold' , 'bg-sky-100 text-sky-700'=> $soal->tipe_soal === 'essay',
                                'bg-brand-100 text-brand-700' => $soal->tipe_soal !== 'essay',
                                ])>
                                {{ $soal->tipe_soal === 'essay' ? 'Essay' : 'Pilihan Ganda' }}
                            </span>

                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                Bobot: {{ $soal->bobot }}
                            </span>
                        </div>

                        <p class="mt-3 whitespace-pre-line text-sm font-semibold leading-relaxed text-slate-800 sm:text-base">
                            {{ $soal->pertanyaan }}
                        </p>

                        @if ($soal->tipe_soal === 'pilihan_ganda')
                        <div class="mt-4 grid gap-2.5 text-sm sm:grid-cols-2">
                            @foreach (['A' => $soal->opsi_a, 'B' => $soal->opsi_b, 'C' => $soal->opsi_c, 'D' => $soal->opsi_d] as $huruf => $teksOpsi)
                            @php
                            $isBenar = strtoupper((string) $soal->jawaban_benar) === $huruf;
                            @endphp
                            <div @class([ 'flex items-start gap-2.5 rounded-xl border px-3.5 py-2.5 text-sm' , 'border-brand-300 bg-brand-50/80 font-semibold text-brand-900'=> $isBenar,
                                'border-slate-200 bg-slate-50/50 text-slate-600' => !$isBenar,
                                ])>
                                <span @class([ 'grid h-6 w-6 shrink-0 place-items-center rounded-lg text-xs font-extrabold' , 'bg-brand-600 text-white'=> $isBenar,
                                    'bg-white text-slate-600 ring-1 ring-slate-200' => !$isBenar,
                                    ])>
                                    {{ $huruf }}
                                </span>
                                <span class="flex-1 pt-0.5">{{ $teksOpsi }}</span>
                                @if ($isBenar)
                                <i class="fa-solid fa-circle-check mt-1 text-brand-600" title="Kunci Jawaban"></i>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <p class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700">
                            <i class="fa-solid fa-key text-[11px]"></i>
                            Kunci jawaban: {{ $soal->jawaban_benar }}
                        </p>
                        @endif
                    </div>

                    <div class="flex shrink-0 items-center gap-2 self-end sm:self-start">
                        <button type="button" wire:click="editQuestion({{ $soal->id }})"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-white px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-50">
                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                            Edit
                        </button>
                        <button type="button" wire:click="hapusQuestion({{ $soal->id }})"
                            wire:confirm="Yakin ingin menghapus soal ini?"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-16 text-center">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                <p class="mt-4 font-bold text-brand-900">Belum ada soal untuk ujian ini</p>
                <p class="mt-1 text-sm text-slate-500">Mulai tambahkan soal pilihan ganda atau essay sekarang.</p>
                <button type="button" wire:click="resetQuestionForm"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Soal
                </button>
            </div>
            @endforelse
        </div>
        @else
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-16 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-hand-pointer"></i>
            </span>
            <p class="mt-4 font-bold text-brand-900">Pilih ujian terlebih dahulu</p>
            <p class="mt-1 text-sm text-slate-500">
                Buka tab Daftar Ujian, lalu klik tombol <strong>Soal</strong> pada ujian yang ingin dikelola.
            </p>
            <button type="button" wire:click="gantiTab('ujian')"
                class="mt-5 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Buka Daftar Ujian
            </button>
        </div>
        @endif
    </div>
    @endif

    {{-- ================= TAB 3: HASIL SISWA ================= --}}
    @if ($tab === 'hasil')
    <div>
        @if ($ujianTerpilih && $hasil)
        {{-- Info Hasil Ujian --}}
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-sky-200 bg-gradient-to-r from-sky-50 to-white p-5 shadow-sm">
            <div class="flex items-start gap-3.5">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-sky-100 text-lg text-sky-700">
                    <i class="fa-solid fa-chart-column"></i>
                </span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Rekapitulasi Nilai</p>
                    <h2 class="text-lg font-extrabold text-slate-800">
                        Hasil: {{ $ujianTerpilih->judul }}
                    </h2>
                    <p class="mt-1 text-xs font-medium text-slate-500">
                        Kelas {{ $ujianTerpilih->pengajaran?->kelas?->nama_kelas ?? '-' }}
                        @if (!is_null($ujianTerpilih->nilai_minimal))
                        <span class="mx-1.5 text-slate-300">·</span>
                        KKM / Nilai Minimal: <strong class="text-slate-700">{{ $ujianTerpilih->nilai_minimal }}</strong>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Tabel Hasil Siswa --}}
        <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[780px] text-left text-sm">
                    <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                        <tr>
                            <th class="px-5 py-3.5">Nama Siswa</th>
                            <th class="px-5 py-3.5 text-center">Skor</th>
                            <th class="px-5 py-3.5">Mulai</th>
                            <th class="px-5 py-3.5">Selesai</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-50">
                        @forelse ($hasil as $result)
                        @php
                        $namaSiswa = $result->siswa?->nama_lengkap ?? 'Siswa tidak ditemukan';
                        $skorNilai = (float) $result->skor;
                        $lulus = is_null($ujianTerpilih->nilai_minimal) || $skorNilai >= (float) $ujianTerpilih->nilai_minimal;
                        @endphp
                        <tr wire:key="hasil-{{ $result->id }}" class="transition hover:bg-brand-50/40">
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-extrabold text-brand-700">
                                        {{ strtoupper(mb_substr($namaSiswa, 0, 1)) }}
                                    </span>
                                    <span class="font-semibold text-slate-800">
                                        {{ $namaSiswa }}
                                    </span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span @class([ 'inline-block rounded-lg px-2.5 py-1 font-mono text-sm font-extrabold' , 'bg-brand-100 text-brand-700'=> $lulus,
                                    'bg-red-100 text-red-700' => !$lulus,
                                    ])>
                                    {{ number_format($skorNilai, 2) }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-500">
                                {{ $result->tanggal_mulai?->translatedFormat('d M Y, H:i') ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-500">
                                {{ $result->tanggal_selesai?->translatedFormat('d M Y, H:i') ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-center">
                                <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    {{ str_replace('_', ' ', ucfirst($result->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </span>
                                <p class="mt-4 font-bold text-brand-900">Belum ada hasil pengerjaan siswa</p>
                                <p class="mt-1 text-sm text-slate-500">Nilai akan muncul secara otomatis setelah siswa menyelesaikan ujian.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $hasil->links() }}</div>
        @else
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-16 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-chart-column"></i>
            </span>
            <p class="mt-4 font-bold text-brand-900">Pilih ujian terlebih dahulu</p>
            <p class="mt-1 text-sm text-slate-500">
                Pilih ujian pada tab Daftar Ujian lalu klik tombol <strong>Hasil</strong> untuk melihat nilai siswa.
            </p>
            <button type="button" wire:click="gantiTab('ujian')"
                class="mt-5 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Buka Daftar Ujian
            </button>
        </div>
        @endif
    </div>
    @endif

    {{-- ================= MODAL FORM UJIAN ================= --}}
    @if ($showUjianForm)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showUjianForm', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showUjianForm', false)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <form wire:submit="simpanUjian" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $ujianId ? 'fa-pen-to-square' : 'fa-file-circle-plus' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $ujianId ? 'Perbarui Jadwal Ujian' : 'Evaluasi Pembelajaran' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $ujianId ? 'Edit Ujian' : 'Tambah Ujian' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showUjianForm', false)" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 overflow-y-auto p-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Pengajaran / Kelas <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="pengajaran_id"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih pengajaran</option>
                            @foreach ($pengajarans as $pengajaran)
                            <option value="{{ $pengajaran->id }}">
                                {{ $pengajaran->mataPelajaran?->nama ?? 'Mapel' }}
                                — {{ $pengajaran->kelas?->nama_kelas ?? 'Kelas' }}
                                — {{ $pengajaran->guru?->nama_lengkap ?? 'Guru' }}
                            </option>
                            @endforeach
                        </select>
                        @error('pengajaran_id')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Judul Ujian <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="judul" type="text"
                            placeholder="Contoh: Ujian Tengah Semester Ganjil"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                        @error('judul')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Deskripsi
                        </label>
                        <textarea wire:model="deskripsi" rows="3"
                            placeholder="Tuliskan petunjuk atau aturan pengerjaan ujian..."
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></textarea>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Jenis
                            </label>
                            <select wire:model="jenis"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                <option value="latihan">Latihan</option>
                                <option value="kuis">Kuis</option>
                                <option value="UTS">UTS</option>
                                <option value="UAS">UAS</option>
                                <option value="ujian">Ujian</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Durasi (menit) <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="durasi" type="number" min="1"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            @error('durasi')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Tanggal Mulai
                            </label>
                            <input wire:model="tanggal_mulai" type="datetime-local"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            @error('tanggal_mulai')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Tanggal Selesai
                            </label>
                            <input wire:model="tanggal_selesai" type="datetime-local"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            @error('tanggal_selesai')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Nilai Minimal (KKM)
                            </label>
                            <input wire:model="nilai_minimal" type="number" min="0" max="100" step="0.01"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            @error('nilai_minimal')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Status
                            </label>
                            <select wire:model="status"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50/40 p-3.5 transition hover:bg-brand-50/80 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                            <input wire:model="acak_soal" type="checkbox"
                                class="mt-0.5 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">
                            <span>
                                <span class="block text-sm font-semibold text-slate-800">Acak soal</span>
                                <span class="mt-0.5 block text-xs text-slate-500">Urutan soal diacak untuk setiap siswa.</span>
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50/40 p-3.5 transition hover:bg-brand-50/80 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                            <input wire:model="acak_jawaban" type="checkbox"
                                class="mt-0.5 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">
                            <span>
                                <span class="block text-sm font-semibold text-slate-800">Acak jawaban</span>
                                <span class="mt-0.5 block text-xs text-slate-500">Opsi pilihan ganda diacak otomatis.</span>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="$set('showUjianForm', false)"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="simpanUjian"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="simpanUjian" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Simpan Ujian
                        </span>
                        <span wire:loading wire:target="simpanUjian" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ================= MODAL FORM SOAL ================= --}}
    @if ($showQuestionForm)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showQuestionForm', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showQuestionForm', false)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <form wire:submit="simpanQuestion" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $questionId ? 'fa-pen-to-square' : 'fa-circle-question' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $questionId ? 'Perbarui Butir Soal' : 'Butir Soal Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $questionId ? 'Edit Soal' : 'Tambah Soal' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showQuestionForm', false)" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 overflow-y-auto p-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="pertanyaan" rows="4"
                            placeholder="Tuliskan teks pertanyaan di sini..."
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></textarea>
                        @error('pertanyaan')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Tipe Soal
                            </label>
                            <select wire:model.live="tipe_soal"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Bobot <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="bobot" type="number" min="0.01" step="0.01"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            @error('bobot')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    @if ($tipe_soal === 'pilihan_ganda')
                    <div class="space-y-3">
                        @foreach (['a', 'b', 'c', 'd'] as $opsi)
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Opsi {{ strtoupper($opsi) }} <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="opsi_{{ $opsi }}" rows="2"
                                placeholder="Isi pilihan jawaban {{ strtoupper($opsi) }}..."
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                               placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></textarea>
                            @error('opsi_'.$opsi)
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Jawaban Benar <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="jawaban_benar"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih jawaban benar</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                        @error('jawaban_benar')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>
                    @else
                    <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3.5 text-xs text-amber-800">
                        <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>
                        <p>Soal essay memerlukan penilaian jawaban oleh guru atau mekanisme penilaian terpisah.</p>
                    </div>
                    @endif
                </div>

                {{-- Footer Modal --}}
                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="$set('showQuestionForm', false)"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="simpanQuestion"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="simpanQuestion" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Simpan Soal
                        </span>
                        <span wire:loading wire:target="simpanQuestion" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>