{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold sm:text-3xl">Manajemen Materi</h1>
                <p class="mt-2 max-w-md text-sm text-brand-50">
                    Kelola materi pembelajaran untuk setiap kelas dan mata pelajaran.
                </p>

                <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <i class="fa-solid fa-book-open"></i>
                    {{ $totalMateri }} materi terdaftar
                </span>
            </div>

            <button type="button" wire:click="create"
                class="inline-flex shrink-0 items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-brand-700 shadow-md transition hover:bg-brand-50 active:scale-[0.99] sm:self-auto">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Materi
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
                <i class="fa-solid fa-book-open"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Materi</p>
                <p class="mt-0.5 text-2xl font-extrabold text-slate-800">{{ $totalMateri }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Materi Aktif</p>
                <p class="mt-0.5 text-2xl font-extrabold text-brand-600">{{ $materiAktif }}</p>
            </div>
        </div>
    </div>

    {{-- ================= FILTER PENCARIAN ================= --}}
    <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm sm:p-5">
        <div class="relative sm:max-w-lg">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-400"></i>

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari judul materi, mata pelajaran, guru, atau kelas..."
                class="w-full rounded-xl border border-brand-100 bg-brand-50/50 py-3 pl-11 pr-11 text-sm outline-none transition
                       placeholder:text-slate-400 focus:border-brand-300 focus:bg-white focus:ring-2 focus:ring-brand-200">

            <i wire:loading wire:target="search"
                class="fa-solid fa-circle-notch fa-spin absolute right-4 top-1/2 -translate-y-1/2 text-sm text-brand-500"></i>
        </div>
    </div>

    {{-- ================= GRID MATERI ================= --}}
    @if ($materis->count())
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($materis as $materi)
        <div wire:key="materi-{{ $materi->id }}"
            class="flex flex-col overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

            {{-- Card Header --}}
            <div class="flex items-start justify-between gap-3 border-b border-brand-100 bg-gradient-to-r from-brand-50/80 to-white p-5">
                <div class="flex min-w-0 items-start gap-3.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $materi->is_active ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-500' }}">
                        <i class="fa-solid fa-book-open"></i>
                    </span>

                    <div class="min-w-0">
                        <h3 class="line-clamp-2 break-words text-base font-extrabold text-slate-800">
                            {{ $materi->judul }}
                        </h3>
                        <p class="mt-1 text-xs font-semibold text-brand-600">
                            {{ $materi->pengajaran?->mataPelajaran?->nama ?? 'Mata pelajaran belum tersedia' }}
                        </p>
                    </div>
                </div>

                @if ($materi->is_active)
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                    Aktif
                </span>
                @else
                <span class="inline-block shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    Nonaktif
                </span>
                @endif
            </div>

            {{-- Card Body --}}
            <div class="flex flex-1 flex-col p-5">
                <div class="space-y-2.5 text-sm text-slate-600">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-chalkboard-user mt-0.5 w-4 shrink-0 text-center text-brand-500"></i>
                        <span class="font-medium text-slate-700">
                            {{ $materi->pengajaran?->guru?->nama_lengkap ?? 'Guru belum tersedia' }}
                        </span>
                    </div>

                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-door-open mt-0.5 w-4 shrink-0 text-center text-brand-500"></i>
                        <span>
                            Kelas <strong class="font-semibold text-slate-700">{{ $materi->pengajaran?->kelas?->nama_kelas ?? '-' }}</strong>
                        </span>
                    </div>

                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-calendar-days mt-0.5 w-4 shrink-0 text-center text-brand-500"></i>
                        <span>
                            Tahun ajaran {{ $materi->pengajaran?->tahunAjaran?->nama ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 border-t border-brand-50 pt-4">
                    <p class="mb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Deskripsi
                    </p>
                    <p class="text-sm leading-relaxed text-slate-600">
                        @if ($materi->deskripsi)
                        {{ \Illuminate\Support\Str::limit($materi->deskripsi, 120) }}
                        @else
                        <span class="italic text-slate-400">Tidak ada deskripsi materi.</span>
                        @endif
                    </p>
                </div>

                {{-- File dan Video --}}
                <div class="mt-4 flex flex-wrap gap-2">
                    @if ($materi->file_path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($materi->file_path) }}"
                        target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">
                        <i class="fa-solid fa-file-arrow-down text-[11px]"></i>
                        Lihat File
                    </a>
                    @endif

                    @if ($materi->video_url)
                    <a href="{{ $materi->video_url }}"
                        target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                        <i class="fa-solid fa-circle-play text-[11px]"></i>
                        Video
                    </a>
                    @endif

                    @if (!$materi->file_path && !$materi->video_url)
                    <span class="text-xs italic text-slate-400">Belum ada file atau video.</span>
                    @endif
                </div>

                {{-- Card Actions --}}
                <div class="mt-auto pt-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-brand-50 pt-4">
                        <button type="button"
                            wire:click="toggleStatus({{ $materi->id }})"
                            wire:loading.attr="disabled"
                            wire:target="toggleStatus({{ $materi->id }})"
                            @class([ 'inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold transition disabled:opacity-60' , 'border-amber-200 bg-amber-50/70 text-amber-700 hover:bg-amber-100'=> $materi->is_active,
                            'border-brand-200 bg-brand-50 text-brand-700 hover:bg-brand-100' => !$materi->is_active,
                            ])>
                            <i class="fa-solid {{ $materi->is_active ? 'fa-ban' : 'fa-check' }} text-[11px]"></i>
                            {{ $materi->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button"
                                wire:click="edit({{ $materi->id }})"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-brand-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                Edit
                            </button>

                            <button type="button"
                                wire:click="delete({{ $materi->id }})"
                                wire:confirm="Yakin ingin menghapus materi ini?"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div>
        {{ $materis->links() }}
    </div>
    @else
    {{-- ================= EMPTY STATE ================= --}}
    <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-16 text-center">
        <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-2xl text-brand-500">
            <i class="fa-solid fa-book-open"></i>
        </span>

        <h3 class="mt-4 font-bold text-brand-900">
            {{ $search ? 'Materi tidak ditemukan' : 'Belum ada materi' }}
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            {{ $search ? 'Coba gunakan kata kunci pencarian lain.' : 'Tambahkan materi pembelajaran pertama untuk sekolah ini.' }}
        </p>

        @if (!$search)
        <button type="button" wire:click="create"
            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700">
            <i class="fa-solid fa-plus text-xs"></i>
            Tambah Materi
        </button>
        @endif
    </div>
    @endif

    {{-- ================= MODAL TAMBAH / EDIT MATERI ================= --}}
    @if ($showForm)
    <div class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center sm:p-4"
        x-data="{ show: false }" x-init="$nextTick(() => show = true)"
        x-on:keydown.escape.window="$wire.set('showForm', false)"
        role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
            :class="show ? 'opacity-100' : 'opacity-0'"
            wire:click="$set('showForm', false)"></div>

        <div class="relative z-10 flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl transition duration-200 sm:rounded-3xl"
            :class="show ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            wire:click.stop>

            <form wire:submit="save" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                {{-- Header Modal --}}
                <div class="flex shrink-0 items-center justify-between gap-3 bg-gradient-to-br from-brand-500 to-brand-600 px-6 py-5 text-white">
                    <div class="flex min-w-0 items-center gap-3.5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-lg font-extrabold text-brand-700 shadow-md">
                            <i class="fa-solid {{ $materiId ? 'fa-pen-to-square' : 'fa-book-medical' }}"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-brand-100">
                                {{ $materiId ? 'Perbarui Bahan Ajar' : 'Bahan Ajar Baru' }}
                            </p>
                            <h2 class="truncate text-lg font-extrabold sm:text-xl">
                                {{ $materiId ? 'Edit Materi' : 'Tambah Materi' }}
                            </h2>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('showForm', false)" aria-label="Tutup"
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 overflow-y-auto p-6">

                    {{-- Penugasan Guru --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Penugasan Guru <span class="text-red-500">*</span>
                        </label>

                        <select wire:model="pengajaran_id"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                            <option value="">-- Pilih penugasan guru --</option>

                            @foreach ($pengajarans as $pengajaran)
                            <option value="{{ $pengajaran->id }}">
                                {{ $pengajaran->mataPelajaran?->nama ?? 'Mapel' }}
                                — {{ $pengajaran->kelas?->nama_kelas ?? 'Kelas' }}
                                — {{ $pengajaran->guru?->nama_lengkap ?? 'Guru' }}
                                ({{ $pengajaran->tahunAjaran?->nama ?? 'Tahun ajaran' }})
                            </option>
                            @endforeach
                        </select>

                        @error('pengajaran_id')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Judul --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Judul Materi <span class="text-red-500">*</span>
                        </label>

                        <input type="text" wire:model="judul"
                            placeholder="Contoh: Pengenalan Sistem Informasi"
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">

                        @error('judul')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            Deskripsi
                        </label>

                        <textarea wire:model="deskripsi" rows="4"
                            placeholder="Jelaskan isi materi pembelajaran..."
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200"></textarea>

                        @error('deskripsi')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Upload File --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            File Materi
                        </label>

                        <input type="file" wire:model="file"
                            accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png"
                            class="block w-full rounded-xl border border-brand-200 bg-brand-50/30 text-sm text-slate-600 transition
                                   file:mr-4 file:cursor-pointer file:border-0 file:bg-brand-100 file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-brand-700 hover:file:bg-brand-200">

                        <p class="mt-1.5 text-xs text-slate-400">
                            PDF, Word, PowerPoint, Excel, atau gambar. Maksimal 10 MB.
                        </p>

                        @if ($materiId && $materis->firstWhere('id', $materiId)?->file_path)
                        <div class="mt-2 flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs text-sky-800">
                            <i class="fa-solid fa-file-circle-check text-sky-500"></i>
                            <span>File lama tersedia. Unggah file baru jika ingin menggantinya.</span>
                        </div>
                        @endif

                        @error('file')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror

                        <div wire:loading wire:target="file" class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium text-brand-600">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Mengunggah file...
                        </div>
                    </div>

                    {{-- Video URL --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-brand-700">
                            URL Video
                        </label>

                        <input type="url" wire:model="video_url"
                            placeholder="https://www.youtube.com/watch?v=..."
                            class="w-full rounded-xl border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:border-brand-400 focus:ring-2 focus:ring-brand-200">

                        @error('video_url')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50/40 p-4 transition hover:bg-brand-50/80 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                        <input type="checkbox" wire:model="is_active"
                            class="mt-0.5 h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">

                        <span>
                            <span class="block text-sm font-semibold text-slate-800">
                                Materi aktif
                            </span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">
                                Materi aktif dapat ditampilkan kepada pengguna yang berhak mengaksesnya.
                            </span>
                        </span>
                    </label>

                    @error('is_active')
                    <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-red-600">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Footer Modal --}}
                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-brand-100 bg-white px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="$set('showForm', false)"
                        class="rounded-xl border border-brand-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    <button type="submit" wire:loading.attr="disabled" wire:target="save,file"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20 transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            {{ $materiId ? 'Simpan Perubahan' : 'Simpan Materi' }}
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