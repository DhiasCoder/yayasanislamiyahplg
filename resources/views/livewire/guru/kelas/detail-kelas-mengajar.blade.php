{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <a href="{{ route('guru.kelas-manager') }}"
                class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/25">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                Kembali ke Kelas Mengajar
            </a>

            <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold sm:text-3xl">Detail Kelas</h1>
                    <p class="mt-1.5 max-w-lg text-sm text-brand-50">
                        Informasi lengkap kelas dan daftar siswa yang terdaftar pada penugasan mengajar kamu.
                    </p>
                </div>

                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-semibold backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                    Penugasan Aktif
                </span>
            </div>
        </div>
    </section>

    {{-- ================= INFORMASI KELAS ================= --}}
    <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-brand-100 text-xl text-brand-700">
                <i class="fa-solid fa-chalkboard-user"></i>
            </span>

            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold uppercase tracking-wider text-brand-600">
                    Kelas &amp; Mata Pelajaran
                </p>
                <h2 class="mt-0.5 text-xl font-extrabold text-slate-800">
                    {{ $pengajaran->kelas->nama_kelas }}
                </h2>
                <p class="mt-0.5 text-sm font-semibold text-brand-600">
                    {{ $pengajaran->mataPelajaran->nama ?? '-' }}
                </p>
            </div>

            <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                Penugasan Aktif
            </span>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 border-t border-brand-50 pt-5 sm:grid-cols-3">
            <div class="flex items-center gap-3.5 rounded-xl border border-brand-100/80 bg-brand-50/40 p-3.5">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                    <i class="fa-solid fa-layer-group text-sm"></i>
                </span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tingkat</p>
                    <p class="mt-0.5 text-sm font-extrabold text-slate-800">
                        {{ $pengajaran->kelas->tingkat ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3.5 rounded-xl border border-brand-100/80 bg-brand-50/40 p-3.5">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                    <i class="fa-solid fa-calendar-days text-sm"></i>
                </span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tahun Ajaran</p>
                    <p class="mt-0.5 text-sm font-extrabold text-slate-800">
                        {{ $pengajaran->tahunAjaran->nama ?? $pengajaran->tahun_ajaran_id }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3.5 rounded-xl border border-brand-100/80 bg-brand-50/40 p-3.5">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sky-100 text-sky-700">
                    <i class="fa-solid fa-users text-sm"></i>
                </span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jumlah Siswa</p>
                    <p class="mt-0.5 text-sm font-extrabold text-sky-700">
                        {{ $siswa->count() }} Siswa
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= DAFTAR SISWA ================= --}}
    <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-brand-100 px-6 py-4">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                    <i class="fa-solid fa-user-graduate text-sm"></i>
                </span>
                <div>
                    <h3 class="text-base font-extrabold text-slate-800">
                        Daftar Siswa
                    </h3>
                    <p class="text-xs text-slate-500">
                        Siswa yang terdaftar pada kelas dan tahun ajaran ini.
                    </p>
                </div>
            </div>

            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">
                <i class="fa-solid fa-users text-[10px]"></i>
                {{ $siswa->count() }} Siswa
            </span>
        </div>

        @if ($siswa->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="w-16 px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">Nama Siswa</th>
                        <th class="px-5 py-3.5">NIS</th>
                        <th class="px-5 py-3.5">Jenis Kelamin</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @foreach ($siswa as $index => $item)
                    <tr wire:key="siswa-{{ $item->id ?? $index }}" class="transition hover:bg-brand-50/40">
                        <td class="whitespace-nowrap px-5 py-4 text-slate-400">
                            {{ $index + 1 }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($item->nama_lengkap, 0, 1)) }}
                                </span>
                                <span class="font-semibold text-slate-800">
                                    {{ $item->nama_lengkap }}
                                </span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            @if ($item->nis)
                            <span class="inline-block rounded-lg border border-brand-200 bg-brand-50 px-2.5 py-1 font-mono text-xs font-bold text-brand-700">
                                {{ $item->nis }}
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            @if ($item->jenis_kelamin)
                            <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                {{ $item->jenis_kelamin }}
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-16 text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                <i class="fa-solid fa-users"></i>
            </span>

            <h4 class="mt-4 font-bold text-brand-900">
                Belum Ada Siswa
            </h4>

            <p class="mt-1 text-sm text-slate-500">
                Belum ada siswa yang terdaftar pada kelas ini.
            </p>
        </div>
        @endif
    </section>

</div>