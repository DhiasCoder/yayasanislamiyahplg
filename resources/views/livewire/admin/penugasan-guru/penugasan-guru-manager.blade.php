{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Penugasan Guru</h1>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Atur distribusi guru pengampu mata pelajaran pada setiap kelas dan tahun ajaran.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    {{ $totalPenugasan }} penugasan terdaftar
                </span>
            </div>

            <button type="button" wire:click="create"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Penugasan
            </button>
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

    {{-- ================= STATISTIK ================= --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-clipboard-list"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Penugasan</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">
                    {{ $totalPenugasan }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Penugasan Aktif</p>
                <p class="mt-0.5 text-2xl font-extrabold text-brand-600">
                    {{ $penugasanAktif }}
                </p>
            </div>
        </div>
    </div>

    {{-- ================= FILTER PENCARIAN ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="relative sm:max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

            <input type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari guru, mata pelajaran, kelas..."
                class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                       placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

            <i wire:loading wire:target="search"
                class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
        </div>
    </div>

    {{-- ================= TABEL PENUGASAN GURU ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left text-sm">
                <thead class="bg-brand-50 text-xs uppercase tracking-wide text-brand-700">
                    <tr>
                        <th class="px-5 py-3.5">No</th>
                        <th class="px-5 py-3.5">Guru</th>
                        <th class="px-5 py-3.5">Mata Pelajaran</th>
                        <th class="px-5 py-3.5">Kelas</th>
                        <th class="px-5 py-3.5">Tahun Ajaran</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-brand-50">
                    @forelse ($penugasans as $item)
                    <tr wire:key="penugasan-{{ $item->id }}" class="transition hover:bg-brand-50/40">
                        <td class="whitespace-nowrap px-5 py-4 text-slate-400">
                            {{ $penugasans->firstItem() + $loop->index }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            @if ($item->guru)
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                                    {{ strtoupper(mb_substr($item->guru->nama_lengkap, 0, 1)) }}
                                </span>
                                <span class="font-semibold text-slate-800">
                                    {{ $item->guru->nama_lengkap }}
                                </span>
                            </div>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-700">
                            {{ $item->mataPelajaran?->nama ?? '-' }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            @if ($item->kelas)
                            <span class="inline-block rounded-lg border border-brand-200 bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">
                                {{ $item->kelas->nama_kelas }}
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                            {{ $item->tahunAjaran?->nama ?? '-' }}
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-center">
                            @if ($item->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Tidak Aktif
                            </span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                    wire:click="edit({{ $item->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                    Edit
                                </button>

                                <button type="button"
                                    wire:click="toggleStatus({{ $item->id }})"
                                    wire:confirm="Ubah status penugasan ini?"
                                    @class([ 'inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold transition' , 'border-amber-200 bg-amber-50/70 text-amber-700 hover:bg-amber-100'=> $item->is_active,
                                    'border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100' => !$item->is_active,
                                    ])>
                                    <i class="fa-solid {{ $item->is_active ? 'fa-ban' : 'fa-check' }} text-[11px]"></i>
                                    {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>

                                <button type="button"
                                    wire:click="delete({{ $item->id }})"
                                    wire:confirm="Yakin ingin menghapus penugasan ini?"
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
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </span>
                            <p class="mt-4 font-bold text-brand-900">Belum ada data penugasan guru</p>
                            <p class="mt-1 text-sm text-slate-500">Klik tombol "Tambah Penugasan" untuk menetapkan guru ke mata pelajaran dan kelas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $penugasans->links() }}</div>

    {{-- ================= MODAL TAMBAH / EDIT PENUGASAN ================= --}}
    @if ($showForm)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.resetForm()"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="resetForm"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <form wire:submit="save" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $pengajaranId ? 'fa-user-pen' : 'fa-chalkboard-user' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $pengajaranId ? 'Perbarui Penugasan' : 'Distribusi Mengajar' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $pengajaranId ? 'Edit Penugasan Guru' : 'Tambah Penugasan Guru' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="resetForm" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 overflow-y-auto p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                                Tahun Ajaran <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="tahun_ajaran_id"
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                                <option value="">Pilih tahun ajaran</option>
                                @foreach ($tahunAjarans as $tahun)
                                <option value="{{ $tahun->id }}">
                                    {{ $tahun->nama }}{{ $tahun->is_active ? ' (Aktif)' : '' }}
                                </option>
                                @endforeach
                            </select>
                            @error('tahun_ajaran_id')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center justify-between gap-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-brand-700">
                                    Kelas <span class="text-red-500">*</span>
                                </label>
                                <span wire:loading wire:target="tahun_ajaran_id" class="inline-flex items-center gap-1 text-[11px] font-medium text-brand-600">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Memuat...
                                </span>
                            </div>
                            <select wire:model="kelas_id" @disabled(!$tahun_ajaran_id)
                                class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                       focus:border-brand-400 focus:ring-2 focus:ring-brand-200 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                <option value="">Pilih kelas</option>
                                @foreach ($kelasOptions as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                            @unless ($tahun_ajaran_id)
                            <p class="mt-1 text-xs text-slate-400">Pilih tahun ajaran terlebih dahulu.</p>
                            @endunless
                            @error('kelas_id')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Guru <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="guru_id"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih guru</option>
                            @foreach ($gurus as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        @error('guru_id')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Mata Pelajaran <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="mata_pelajaran_id"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">Pilih mata pelajaran</option>
                            @foreach ($mataPelajarans as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                            @endforeach
                        </select>
                        @error('mata_pelajaran_id')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50/40 p-4 transition hover:bg-brand-50/80 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                        <input type="checkbox" wire:model="is_active"
                            class="mt-0.5 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">Penugasan aktif</span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">
                                Guru dapat menggunakan penugasan ini untuk kegiatan pembelajaran dan penilaian.
                            </span>
                        </span>
                    </label>
                </div>

                {{-- Footer Modal --}}
                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="resetForm"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Simpan
                        </span>
                        <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
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