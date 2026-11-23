@php
$angka = fn ($n) => is_numeric($n)
? rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',')
: $n;

$jmlSoal = $soals->count();
$jmlPg = $soals->where('tipe_soal', 'pilihan_ganda')->count();
$jmlEssay = $soals->where('tipe_soal', 'essay')->count();
$totalBobot = $soals->sum('bobot');

$input = 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2';
$normal = 'border-brand-200 focus:border-brand-400 focus:ring-brand-200';
$salah = 'border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-100';

$tipeSoal = [
'pilihan_ganda' => ['Pilihan Ganda', 'Opsi A–D, dinilai otomatis', 'fa-list-ul'],
'essay' => ['Essay', 'Jawaban berupa teks', 'fa-pen-nib'],
];

$draft = $ujian->status === 'draft';
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="mx-auto max-w-6xl space-y-6"
    x-data="{ modal: null, hapusId: null, hapusNo: null }"
    x-on:keydown.escape.window="modal = null">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('guru.tugas-ujian') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-brand-100 transition hover:text-white">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Tugas &amp; Ujian
                </a>

                <h1 class="mt-3 text-2xl font-extrabold sm:text-3xl">Kelola Soal Ujian</h1>
                <p class="mt-2 text-sm text-brand-50">{{ $ujian->judul }}</p>
            </div>

            @if ($draft)
            <button type="button" @click="modal = 'publish'" @disabled($jmlSoal < 1)
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-brand-700 shadow-md transition hover:bg-brand-50
                           disabled:cursor-not-allowed disabled:bg-white/40 disabled:text-white/70 disabled:shadow-none">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                Publikasikan Ujian
            </button>
            @else
            <span class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold backdrop-blur">
                <i class="fa-solid fa-circle-check"></i>
                Ujian Telah Dipublikasikan
            </span>
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
    <div x-data="{ show: true }" x-show="show"
        class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" @click="show = false" class="text-red-400 transition hover:text-red-600" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    {{-- ================= STATISTIK ================= --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-layer-group"></i>
            </span>
            <div>
                <p class="text-xs text-slate-500">Total Soal</p>
                <p class="text-xl font-extrabold text-brand-900">{{ $jmlSoal }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-100 text-lg text-brand-600">
                <i class="fa-solid fa-list-ul"></i>
            </span>
            <div>
                <p class="text-xs text-slate-500">Pilihan Ganda</p>
                <p class="text-xl font-extrabold text-brand-900">{{ $jmlPg }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-sky-100 text-lg text-sky-600">
                <i class="fa-solid fa-pen-nib"></i>
            </span>
            <div>
                <p class="text-xs text-slate-500">Essay</p>
                <p class="text-xl font-extrabold text-brand-900">{{ $jmlEssay }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-amber-100 text-lg text-amber-600">
                <i class="fa-solid fa-star"></i>
            </span>
            <div>
                <p class="text-xs text-slate-500">Total Bobot</p>
                <p class="text-xl font-extrabold text-brand-900">{{ $angka($totalBobot) }}<span class="ml-1 text-xs font-semibold text-slate-400">poin</span></p>
            </div>
        </div>
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-5">

        {{-- ================= FORM SOAL ================= --}}
        <div class="lg:sticky lg:top-24 lg:col-span-2">
            <form id="form-soal" wire:submit="{{ $modeEdit ? 'update' : 'simpan' }}"
                class="scroll-mt-24 space-y-5 rounded-2xl border bg-white p-5 shadow-sm {{ $modeEdit ? 'border-brand-300 ring-2 ring-brand-100' : 'border-brand-100' }}">

                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-600">
                            <i class="fa-solid {{ $modeEdit ? 'fa-pen-to-square' : 'fa-plus' }}"></i>
                        </span>
                        <div>
                            <h2 class="font-extrabold text-brand-900">{{ $modeEdit ? 'Edit Soal' : 'Tambah Soal' }}</h2>
                            <p class="text-xs text-slate-500">
                                <span x-data x-text="$wire.tipe_soal === 'essay'
                                    ? 'Masukkan pertanyaan essay.'
                                    : 'Masukkan pertanyaan dan pilihan jawaban.'">Masukkan pertanyaan dan pilihan jawaban.</span>
                            </p>
                        </div>
                    </div>

                    @if ($modeEdit)
                    <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-700">Mode edit</span>
                    @endif
                </div>

                {{-- Tipe soal --}}
                <div>
                    <span class="mb-1.5 block text-sm font-semibold text-slate-700">Tipe Soal</span>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($tipeSoal as $nilai => [$judulTipe, $deskTipe, $ikonTipe])
                        <label class="relative block {{ $tipeTerkunci ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                            <input type="radio" wire:model="tipe_soal" value="{{ $nilai }}" @disabled($tipeTerkunci) class="peer sr-only">

                            <div class="h-full rounded-2xl border-2 border-slate-200 bg-white p-3.5 pr-9 transition
                                            hover:border-brand-300 peer-checked:border-brand-500 peer-checked:bg-brand-50
                                            peer-focus-visible:ring-2 peer-focus-visible:ring-brand-300 peer-disabled:opacity-60 peer-disabled:hover:border-slate-200">
                                <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-100 text-brand-600">
                                    <i class="fa-solid {{ $ikonTipe }}"></i>
                                </span>
                                <span class="mt-2 block text-sm font-bold text-slate-800">{{ $judulTipe }}</span>
                                <span class="mt-0.5 block text-[11px] leading-snug text-slate-500">{{ $deskTipe }}</span>
                            </div>

                            <span class="pointer-events-none absolute right-3 top-3 h-5 w-5 rounded-full border-2 border-slate-300 bg-white transition
                                             peer-checked:border-brand-500 peer-checked:bg-brand-500 peer-checked:shadow-[inset_0_0_0_3px_#fff]"></span>
                        </label>
                        @endforeach
                    </div>

                    @if ($tipeTerkunci)
                    <p class="mt-2 flex items-start gap-2 text-xs text-amber-700">
                        <i class="fa-solid fa-lock mt-0.5"></i>
                        Tipe soal tidak dapat diubah karena sudah ada jawaban siswa.
                    </p>
                    @endif

                    @error('tipe_soal')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pertanyaan --}}
                <div>
                    <label for="pertanyaan" class="mb-1.5 block text-sm font-semibold text-slate-700">Pertanyaan</label>

                    <textarea id="pertanyaan" wire:model="pertanyaan" rows="4"
                        placeholder="Tuliskan pertanyaan..."
                        class="{{ $input }} resize-y {{ $errors->has('pertanyaan') ? $salah : $normal }}"></textarea>

                    @error('pertanyaan')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Opsi jawaban (pilihan ganda) --}}
                <div x-data x-show="$wire.tipe_soal === 'pilihan_ganda'"
                    @if ($tipe_soal !=='pilihan_ganda' ) style="display: none;" @endif
                    class="space-y-4">

                    @foreach (['a', 'b', 'c', 'd'] as $opsi)
                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <label for="opsi_{{ $opsi }}" class="text-sm font-semibold text-slate-700">Opsi {{ strtoupper($opsi) }}</label>

                            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-400 transition
                                              hover:border-brand-300 has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                                <input type="radio" wire:model="jawaban_benar" value="{{ $opsi }}" class="h-3.5 w-3.5 accent-brand-600">
                                Jawaban benar
                            </label>
                        </div>

                        <input id="opsi_{{ $opsi }}" type="text" wire:model="opsi_{{ $opsi }}"
                            placeholder="Jawaban {{ strtoupper($opsi) }}"
                            class="{{ $input }} {{ $errors->has('opsi_' . $opsi) ? $salah : $normal }}">

                        @error('opsi_' . $opsi)
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach

                    @error('jawaban_benar')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keterangan essay --}}
                <div x-data x-show="$wire.tipe_soal === 'essay'"
                    @if ($tipe_soal !=='essay' ) style="display: none;" @endif
                    class="flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                    <i class="fa-solid fa-circle-info mt-0.5 text-sky-500"></i>
                    <p>
                        Soal essay tidak memiliki opsi maupun kunci jawaban, dan tidak dikoreksi otomatis.
                        Siswa menjawab dalam bentuk teks.
                    </p>
                </div>

                {{-- Bobot --}}
                <div>
                    <label for="bobot" class="mb-1.5 block text-sm font-semibold text-slate-700">Bobot Nilai</label>

                    <div class="relative">
                        <input id="bobot" type="number" wire:model="bobot" min="1" inputmode="numeric"
                            class="{{ $input }} pr-14 {{ $errors->has('bobot') ? $salah : $normal }}">
                        <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">poin</span>
                    </div>

                    @error('bobot')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-2 border-t border-brand-100 pt-5">
                    @if ($modeEdit)
                    <button type="button" wire:click="batalEdit"
                        class="flex-1 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>
                    @endif

                    <button type="submit" wire:loading.attr="disabled" wire:target="simpan,update"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                               hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="simpan,update" class="inline-flex items-center gap-2">
                            <i class="fa-solid {{ $modeEdit ? 'fa-floppy-disk' : 'fa-plus' }} text-xs"></i>
                            {{ $modeEdit ? 'Simpan Perubahan' : 'Tambah Soal' }}
                        </span>
                        <span wire:loading wire:target="simpan,update" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- ================= DAFTAR SOAL ================= --}}
        <div class="lg:col-span-3">
            <div class="space-y-4 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-extrabold text-brand-900">Daftar Soal</h2>
                    <span class="rounded-full bg-brand-100 px-3 py-1 text-sm font-bold text-brand-700">{{ $jmlSoal }} Soal</span>
                </div>

                @forelse ($soals as $index => $soal)
                @php
                $essay = $soal->tipe_soal === 'essay';
                $sedangDiedit = $modeEdit && (int) $questionId === (int) $soal->id;
                @endphp

                <div wire:key="soal-{{ $soal->id }}"
                    class="rounded-xl border p-4 transition {{ $sedangDiedit ? 'border-brand-400 bg-brand-50/50 ring-2 ring-brand-100' : 'border-brand-100 hover:border-brand-300' }}">

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-start gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-sm font-extrabold
                                    {{ $essay ? 'bg-sky-100 text-sky-700' : 'bg-brand-100 text-brand-700' }}">
                                {{ $index + 1 }}
                            </span>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold
                                            {{ $essay ? 'bg-sky-100 text-sky-700' : 'bg-brand-100 text-brand-700' }}">
                                        {{ $essay ? 'Essay' : 'Pilihan Ganda' }}
                                    </span>

                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-700">
                                        <i class="fa-solid fa-star text-[9px]"></i>
                                        {{ $angka($soal->bobot) }} poin
                                    </span>
                                </div>

                                <p class="mt-2 whitespace-pre-line text-sm font-medium leading-relaxed text-slate-800">{{ $soal->pertanyaan }}</p>
                            </div>
                        </div>

                        <div class="flex shrink-0 gap-2">
                            <button type="button" wire:click="edit({{ $soal->id }})"
                                x-data
                                @click="document.getElementById('form-soal')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                                class="grid h-9 w-9 place-items-center rounded-lg bg-brand-50 text-sm text-brand-700 transition hover:bg-brand-100"
                                title="Edit soal" aria-label="Edit soal {{ $index + 1 }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <button type="button"
                                @click="modal = 'hapus'; hapusId = {{ $soal->id }}; hapusNo = {{ $index + 1 }}"
                                class="grid h-9 w-9 place-items-center rounded-lg bg-red-50 text-sm text-red-600 transition hover:bg-red-100"
                                title="Hapus soal" aria-label="Hapus soal {{ $index + 1 }}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    @if ($essay)
                    <div class="mt-4 flex items-center gap-3 rounded-lg border border-dashed border-sky-200 bg-sky-50/60 px-4 py-3 text-sm text-sky-700">
                        <i class="fa-solid fa-pen-nib"></i>
                        Siswa menjawab dalam bentuk teks. Dinilai secara manual oleh guru.
                    </div>
                    @else
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach (['a', 'b', 'c', 'd'] as $opsi)
                        @php $benar = $soal->jawaban_benar === $opsi; @endphp

                        <div class="flex items-start gap-2.5 rounded-lg p-3 text-sm
                                        {{ $benar ? 'bg-brand-50 text-brand-800 ring-1 ring-brand-300' : 'bg-slate-50 text-slate-600' }}">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-md text-xs font-extrabold
                                            {{ $benar ? 'bg-brand-500 text-white' : 'bg-white text-slate-500 ring-1 ring-slate-200' }}">
                                {{ strtoupper($opsi) }}
                            </span>
                            <span class="min-w-0 flex-1 pt-0.5">{{ $soal->{'opsi_' . $opsi} }}</span>
                            @if ($benar)
                            <i class="fa-solid fa-circle-check mt-1 text-brand-500"></i>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50/30 px-6 py-14 text-center">
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
                        <i class="fa-solid fa-file-circle-question"></i>
                    </span>
                    <p class="mt-4 font-semibold text-brand-900">Belum ada soal</p>
                    <p class="mt-1 text-sm text-slate-500">Tambahkan soal melalui formulir di samping.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ================= MODAL KONFIRMASI (PUBLIKASI & HAPUS) ================= --}}
    <div x-show="modal" x-cloak
        class="fixed inset-0 z-[60] flex items-end justify-center p-4 sm:items-center"
        role="dialog" aria-modal="true">

        <div x-show="modal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="modal = null"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div x-show="modal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
            class="relative z-10 w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">

            <div class="h-1.5 bg-gradient-to-r"
                :class="modal === 'hapus' ? 'from-red-400 to-red-500' : 'from-brand-400 to-brand-600'"></div>

            <div class="p-6 text-center sm:p-7">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-full text-2xl text-white shadow-lg"
                    :class="modal === 'hapus'
                        ? 'bg-gradient-to-br from-red-400 to-red-500 shadow-red-500/30'
                        : 'bg-gradient-to-br from-brand-400 to-brand-600 shadow-brand-500/30'">
                    <i class="fa-solid" :class="modal === 'hapus' ? 'fa-trash' : 'fa-paper-plane'"></i>
                </span>

                {{-- Publikasi --}}
                <div x-show="modal === 'publish'">
                    <h3 class="mt-4 text-xl font-extrabold text-brand-900">Publikasikan ujian?</h3>
                    <p class="mt-1.5 text-sm text-slate-500">
                        Pastikan soal dan jadwal sudah benar sebelum mempublikasikan ujian ini.
                    </p>

                    <div class="mt-5 grid grid-cols-2 gap-2 text-center">
                        <div class="rounded-xl bg-brand-50 px-2 py-3">
                            <p class="text-xl font-extrabold text-brand-700">{{ $jmlSoal }}</p>
                            <p class="text-[11px] font-medium text-brand-600">Soal</p>
                        </div>
                        <div class="rounded-xl bg-amber-50 px-2 py-3">
                            <p class="text-xl font-extrabold text-amber-600">{{ $angka($totalBobot) }}</p>
                            <p class="text-[11px] font-medium text-amber-700">Total bobot (poin)</p>
                        </div>
                    </div>
                </div>

                {{-- Hapus --}}
                <div x-show="modal === 'hapus'" x-cloak>
                    <h3 class="mt-4 text-xl font-extrabold text-brand-900">
                        Hapus soal nomor <span x-text="hapusNo"></span>?
                    </h3>
                    <p class="mt-1.5 text-sm text-slate-500">
                        Soal yang dihapus tidak dapat dikembalikan.
                    </p>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-center">
                    <button type="button" @click="modal = null"
                        class="inline-flex items-center justify-center rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-50">
                        Batal
                    </button>

                    {{-- Konfirmasi publikasi --}}
                    <button type="button" x-show="modal === 'publish'"
                        wire:click="publikasikan" wire:loading.attr="disabled" wire:target="publikasikan"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                               hover:from-brand-600 hover:to-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="publikasikan" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane text-xs"></i> Ya, Publikasikan
                        </span>
                        <span wire:loading wire:target="publikasikan" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Memproses...
                        </span>
                    </button>

                    {{-- Konfirmasi hapus --}}
                    <button type="button" x-show="modal === 'hapus'" x-cloak
                        @click="$wire.hapus(hapusId).then(() => modal = null)"
                        wire:loading.attr="disabled" wire:target="hapus"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-red-500/30 transition
                               hover:from-red-600 hover:to-red-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="hapus" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-trash text-xs"></i> Ya, Hapus
                        </span>
                        <span wire:loading wire:target="hapus" class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Menghapus...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>