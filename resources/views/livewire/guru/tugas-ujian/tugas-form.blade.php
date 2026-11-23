@php
$input = 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2';
$normal = 'border-brand-200 focus:border-brand-400 focus:ring-brand-200';
$salah = 'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-100';
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

            <h1 class="mt-3 text-2xl font-extrabold sm:text-3xl">
                {{ $tugasId ? 'Edit Tugas' : 'Buat Tugas' }}
            </h1>
            <p class="mt-2 max-w-lg text-sm text-brand-50">
                Kelola informasi dan pengaturan tugas siswa.
            </p>
        </div>
    </section>

    <form wire:submit="simpan"
        x-data="{ drag: false, uploading: false, progress: 0 }"
        x-on:livewire-upload-start="uploading = true; progress = 0"
        x-on:livewire-upload-finish="uploading = false"
        x-on:livewire-upload-error="uploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
        class="space-y-5">

        {{-- ================= INFORMASI TUGAS ================= --}}
        <section class="space-y-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="fa-solid fa-file-lines"></i>
                </span>
                <h2 class="font-extrabold text-brand-900">Informasi Tugas</h2>
            </div>

            <div>
                <label for="pengajaran_id" class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Kelas dan Mata Pelajaran
                </label>

                <select id="pengajaran_id" wire:model="pengajaran_id"
                    class="{{ $input }} {{ $errors->has('pengajaran_id') ? $salah : $normal }}">
                    <option value="">Pilih Pengajaran</option>

                    @foreach ($pengajaran as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->kelas->nama_kelas }}
                        - {{ $item->mataPelajaran->nama }}
                    </option>
                    @endforeach
                </select>

                @error('pengajaran_id')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="judul" class="mb-1.5 block text-sm font-semibold text-slate-700">Judul Tugas</label>

                <input id="judul" type="text" wire:model="judul"
                    placeholder="Masukkan judul tugas"
                    class="{{ $input }} {{ $errors->has('judul') ? $salah : $normal }}">

                @error('judul')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi</label>

                <textarea id="deskripsi" wire:model="deskripsi" rows="5"
                    placeholder="Tuliskan instruksi tugas..."
                    class="{{ $input }} resize-y {{ $errors->has('deskripsi') ? $salah : $normal }}"></textarea>

                <div class="mt-1.5 flex items-start justify-between gap-3">
                    <div>
                        @error('deskripsi')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <span class="shrink-0 text-xs text-slate-400"
                        x-text="(($wire.deskripsi || '').length) + ' karakter'"></span>
                </div>
            </div>
        </section>

        {{-- ================= FILE SOAL ================= --}}
        <section class="space-y-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="fa-solid fa-paperclip"></i>
                </span>
                <div>
                    <h2 class="font-extrabold text-brand-900">File Soal</h2>
                    <p class="text-xs text-slate-500">Opsional · maksimal 10 MB</p>
                </div>
            </div>

            <div>
                <label for="file-soal"
                    @dragover.prevent="drag = true"
                    @dragleave.prevent="drag = false"
                    @drop.prevent="drag = false; $refs.berkas.files = $event.dataTransfer.files; $refs.berkas.dispatchEvent(new Event('change', { bubbles: true }))"
                    :class="drag ? 'border-brand-500 bg-brand-100/60' : 'border-brand-300 bg-brand-50/40 hover:bg-brand-50'"
                    class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed px-4 py-8 text-center transition
                           @error('file_soal') !border-red-300 !bg-red-50 @enderror">

                    @if ($file_soal)
                    @php
                    $bisaPratinjau = method_exists($file_soal, 'isPreviewable') && $file_soal->isPreviewable();
                    @endphp

                    @if ($bisaPratinjau)
                    <img src="{{ $file_soal->temporaryUrl() }}" alt="Pratinjau"
                        class="h-24 w-24 rounded-xl border border-brand-200 object-cover shadow-sm">
                    @else
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-brand-100 text-brand-600">
                        <i class="fa-solid fa-file-lines text-2xl"></i>
                    </span>
                    @endif

                    <p class="max-w-full break-all text-sm font-semibold text-brand-800">
                        <i class="fa-solid fa-circle-check mr-1 text-brand-500"></i>
                        File baru: {{ $file_soal->getClientOriginalName() }}
                    </p>
                    <p class="text-xs text-slate-500">Klik atau seret file lain untuk mengganti</p>
                    @else
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-white text-2xl text-brand-500 shadow-sm ring-1 ring-brand-100">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </span>
                    <p class="text-sm font-semibold text-slate-700">
                        <span class="text-brand-600">Klik untuk memilih file</span> atau seret ke sini
                    </p>
                    <p class="text-xs text-slate-400">Maksimal 10 MB</p>
                    @endif
                </label>

                <input id="file-soal" x-ref="berkas" type="file" wire:model="file_soal" class="sr-only">

                {{-- Progres unggah --}}
                <div x-show="uploading" x-cloak class="mt-3">
                    <div class="mb-1 flex items-center justify-between text-xs text-brand-700">
                        <span>Mengunggah file...</span>
                        <span x-text="progress + '%'"></span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-brand-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600 transition-all"
                            :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>

                {{-- File sebelumnya --}}
                @if ($fileLama && ! $file_soal)
                <a href="{{ Storage::disk('public')->url($fileLama) }}" target="_blank"
                    class="mt-3 inline-flex items-center gap-2 rounded-lg bg-brand-50 px-3.5 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-100">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    Lihat file sebelumnya
                </a>
                @endif

                @error('file_soal')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- ================= JADWAL ================= --}}
        <section class="space-y-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="fa-regular fa-calendar-days"></i>
                </span>
                <h2 class="font-extrabold text-brand-900">Jadwal</h2>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="tanggal_mulai" class="mb-1.5 block text-sm font-semibold text-slate-700">Tanggal Mulai</label>

                    <input id="tanggal_mulai" type="datetime-local" wire:model="tanggal_mulai"
                        class="{{ $input }} {{ $errors->has('tanggal_mulai') ? $salah : $normal }}">

                    @error('tanggal_mulai')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deadline" class="mb-1.5 block text-sm font-semibold text-slate-700">Deadline</label>

                    <input id="deadline" type="datetime-local" wire:model="deadline"
                        class="{{ $input }} {{ $errors->has('deadline') ? $salah : $normal }}">

                    @error('deadline')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Peringatan jika deadline lebih awal dari tanggal mulai --}}
            <div x-data x-show="$wire.deadline && $wire.tanggal_mulai && $wire.deadline < $wire.tanggal_mulai" x-cloak
                class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <p>Deadline lebih awal dari tanggal mulai. Periksa kembali jadwal tugas.</p>
            </div>
        </section>

        {{-- ================= PENILAIAN & PENGATURAN ================= --}}
        <section class="space-y-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-100 text-brand-600">
                    <i class="fa-solid fa-star"></i>
                </span>
                <h2 class="font-extrabold text-brand-900">Penilaian &amp; Pengaturan</h2>
            </div>

            <div class="max-w-xs">
                <label for="nilai_maksimal" class="mb-1.5 block text-sm font-semibold text-slate-700">Nilai Maksimal</label>

                <input id="nilai_maksimal" type="number" wire:model="nilai_maksimal" min="1" inputmode="numeric"
                    class="{{ $input }} {{ $errors->has('nilai_maksimal') ? $salah : $normal }}">

                @error('nilai_maksimal')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-brand-100 bg-brand-50/40 p-4 transition hover:bg-brand-50">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Izinkan pengumpulan terlambat</span>
                    <span class="mt-0.5 block text-xs text-slate-500">
                        Siswa tetap dapat mengumpulkan tugas setelah deadline berakhir.
                    </span>
                </span>

                <input type="checkbox" wire:model="boleh_terlambat" class="peer sr-only">

                <span class="relative h-6 w-11 shrink-0 rounded-full bg-slate-300 transition
                             after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition
                             peer-checked:bg-brand-500 peer-checked:after:translate-x-5
                             peer-focus-visible:ring-2 peer-focus-visible:ring-brand-300 peer-focus-visible:ring-offset-2"></span>
            </label>
        </section>

        {{-- ================= AKSI ================= --}}
        <div class="sticky bottom-0 z-10 flex items-center justify-end gap-3 rounded-2xl border border-brand-100 bg-white/95 px-5 py-4 shadow-lg shadow-brand-100/50 backdrop-blur">
            <a href="{{ route('guru.tugas-ujian') }}"
                class="rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                Batal
            </a>

            <button type="submit" :disabled="uploading" wire:loading.attr="disabled" wire:target="simpan"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                       hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                <span wire:loading.remove wire:target="simpan" class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    {{ $tugasId ? 'Simpan Perubahan' : 'Buat Tugas' }}
                </span>

                <span wire:loading wire:target="simpan" class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                    Menyimpan...
                </span>
            </button>
        </div>

    </form>
</div>