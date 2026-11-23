{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Rekap Absensi</h1>
                <p class="mt-2 max-w-lg text-sm text-brand-50">
                    Pantau sesi pembelajaran dan rekapitulasi kehadiran siswa di seluruh kelas sekolah.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-clipboard-user"></i>
                    {{ $absensis->total() }} sesi absensi tercatat
                </span>
            </div>
        </div>
    </section>

    {{-- ================= FILTER & PENCARIAN ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
            {{-- Pencarian --}}
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari kelas atau mata pelajaran..."
                    class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-2.5 pl-11 pr-11 text-sm text-slate-700 outline-none transition
                           placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

                <i wire:loading wire:target="search"
                    class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
            </div>

            {{-- Filter Tanggal, Status & Reset --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input
                    type="date"
                    wire:model.live="tanggal"
                    aria-label="Filter tanggal"
                    class="rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">

                <select
                    wire:model.live="status"
                    aria-label="Filter status kehadiran"
                    class="rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                           focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpa">Alpa</option>
                </select>

                <button
                    type="button"
                    wire:click="resetFilter"
                    wire:loading.attr="disabled"
                    wire:target="resetFilter"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50 hover:text-brand-700 disabled:opacity-60">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- ================= TABEL REKAP ABSENSI ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[840px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">Kelas</th>
                        <th class="px-5 py-3.5">Mata Pelajaran</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Jam</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Hadir</th>
                        <th class="px-5 py-3.5 text-center">Total Absen</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($absensis as $absensi)
                    @php
                    $jumlahHadir = $absensi->detailAbsensis?->where('status', 'hadir')->count() ?? 0;
                    $totalAbsen = $absensi->detailAbsensis?->count() ?? 0;
                    @endphp

                    <tr wire:key="rekap-{{ $absensi->id }}" class="transition hover:bg-brand-50/40">
                        <td class="whitespace-nowrap px-5 py-4 text-slate-400">
                            {{ $absensis->firstItem() + $loop->index }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            @if ($absensi->pengajaran?->kelas?->nama_kelas)
                            <span class="inline-block rounded-lg border border-brand-200 bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">
                                {{ $absensi->pengajaran->kelas->nama_kelas }}
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700">
                                    <i class="fa-solid fa-book-open text-xs"></i>
                                </span>
                                <span class="font-semibold text-slate-800">
                                    {{ $absensi->pengajaran?->mataPelajaran?->nama ?? '-' }}
                                </span>
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-600">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-brand-500"></i>
                                {{ $absensi->tanggal ? \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d M Y') : '-' }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-xs font-medium text-slate-700">
                                <i class="fa-regular fa-clock text-brand-500"></i>
                                {{ $absensi->jam_mulai ? substr($absensi->jam_mulai, 0, 5) : '-' }}
                                -
                                {{ $absensi->jam_selesai ? substr($absensi->jam_selesai, 0, 5) : '-' }}
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">
                                <i class="fa-solid fa-user-check text-[10px]"></i>
                                {{ $jumlahHadir }} Siswa
                            </span>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">
                                <i class="fa-solid fa-users text-[10px]"></i>
                                {{ $totalAbsen }} Siswa
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
                                <i class="fa-solid fa-clipboard-user"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Belum ada data absensi</p>
                            <p class="mt-1 text-sm text-slate-500">
                                Data sesi kehadiran akan muncul di sini atau coba sesuaikan filter pencarian Anda.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $absensis->links() }}</div>

</div>