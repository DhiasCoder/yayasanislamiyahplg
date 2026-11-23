{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Kelas Mengajar</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Daftar kelas dan mata pelajaran sesuai penugasan mengajar kamu.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    {{ $pengajarans->count() }} kelas terdaftar
                </span>
            </div>
        </div>
    </section>

    {{-- ================= FILTER PENCARIAN ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="relative sm:max-w-md">
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari kelas atau mata pelajaran..."
                class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm text-slate-700 outline-none transition
                       placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

            <i wire:loading wire:target="search"
                class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
        </div>
    </div>

    {{-- ================= DAFTAR KELAS ================= --}}
    @if ($pengajarans->isNotEmpty())
    <div wire:loading.class="pointer-events-none opacity-60"
        class="grid grid-cols-1 gap-5 transition-opacity duration-200 sm:grid-cols-2 lg:grid-cols-3">

        @foreach ($pengajarans as $pengajaran)
        @php
        $jumlahSiswa = $pengajaran->kelas?->siswa?->count() ?? 0;
        $namaKelas = $pengajaran->kelas?->nama_kelas ?? 'Kelas tidak tersedia';
        $namaMapel = $pengajaran->mataPelajaran?->nama ?? 'Mata pelajaran tidak tersedia';
        @endphp

        <div wire:key="pengajaran-{{ $pengajaran->id }}"
            class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-brand-200 hover:shadow-md">

            {{-- Card Header --}}
            <div class="border-b border-brand-100 bg-gradient-to-r from-brand-50/80 to-white p-5">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-700 transition-colors duration-200 group-hover:bg-brand-600 group-hover:text-white">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </span>

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                        Aktif
                    </span>
                </div>

                <h3 class="mt-4 line-clamp-1 text-lg font-extrabold text-slate-800"
                    title="{{ $namaKelas }}">
                    {{ $namaKelas }}
                </h3>

                <p class="mt-1 line-clamp-2 text-xs font-semibold text-brand-600 sm:text-sm"
                    title="{{ $namaMapel }}">
                    {{ $namaMapel }}
                </p>
            </div>

            {{-- Card Body & Action --}}
            <div class="flex flex-1 flex-col justify-between p-5">
                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2 text-slate-500">
                            <i class="fa-solid fa-layer-group w-4 text-center text-brand-500"></i>
                            Tingkat
                        </span>
                        <span class="font-semibold text-slate-700">
                            {{ $pengajaran->kelas?->tingkat ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2 text-slate-500">
                            <i class="fa-solid fa-calendar-days w-4 text-center text-brand-500"></i>
                            Tahun Ajaran
                        </span>
                        <span class="truncate font-semibold text-slate-700">
                            {{ $pengajaran->tahunAjaran?->nama ?? $pengajaran->tahun_ajaran_id ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2 text-slate-500">
                            <i class="fa-solid fa-users w-4 text-center text-brand-500"></i>
                            Jumlah Siswa
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-bold text-sky-700">
                            {{ $jumlahSiswa }} Siswa
                        </span>
                    </div>
                </div>

                <a href="{{ route('guru.kelas.detail', $pengajaran->id) }}"
                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 active:scale-[0.99]">
                    <span>Lihat Detail Kelas</span>
                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
        @endforeach

    </div>
    @else
    {{-- ================= EMPTY STATE ================= --}}
    <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-16 text-center">
        <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
            <i class="fa-solid fa-chalkboard"></i>
        </span>

        <h3 class="mt-4 font-bold text-brand-900">
            {{ filled($search ?? null) ? 'Kelas tidak ditemukan' : 'Belum ada kelas mengajar' }}
        </h3>

        <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
            @if (filled($search ?? null))
            Tidak ada kelas atau mata pelajaran yang cocok dengan kata kunci "<span class="font-semibold text-slate-700">{{ $search }}</span>".
            @else
            Belum ada penugasan kelas yang sesuai dengan akun guru kamu.
            @endif
        </p>

        @if (filled($search ?? null))
        <button type="button"
            wire:click="$set('search', '')"
            class="mt-5 inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-4 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-100">
            <i class="fa-solid fa-rotate-left text-xs"></i>
            Reset Pencarian
        </button>
        @endif
    </div>
    @endif

</div>