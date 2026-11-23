@php
$namaKelas = $materi->pengajaran?->kelas?->nama_kelas ?? 'Kelas';
$avatar = fn ($nama) => 'https://ui-avatars.com/api/?name=' . urlencode($nama ?: 'Pengguna') . '&background=e1f4e6&color=23653c&bold=true';

$prevTanggal = null;
$prevUserId = null;
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-500 to-brand-600 p-6 text-white shadow-lg shadow-brand-500/20 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <a href="{{ url('/guru/materi') }}"
                class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/25">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                Kembali ke Materi
            </a>

            <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <h1 class="text-2xl font-extrabold sm:text-3xl">Diskusi Materi</h1>
                    <p class="mt-1.5 max-w-2xl text-sm font-medium text-brand-50">
                        {{ $materi->judul }}
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                            <i class="fa-solid fa-school"></i>
                            {{ $namaKelas }}
                        </span>

                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                            <i class="fa-solid fa-comments"></i>
                            {{ $diskusis->count() }} pesan tercatat
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= RUANG DISKUSI ================= --}}
    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">

        {{-- Header ruang diskusi --}}
        <div class="flex items-center gap-3.5 border-b border-brand-100 bg-gradient-to-r from-brand-50/80 to-white px-5 py-4 sm:px-6">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-lg text-white shadow-md shadow-brand-500/20">
                <i class="fa-solid fa-comments"></i>
            </span>

            <div class="min-w-0 flex-1">
                <h2 class="text-base font-extrabold text-slate-800">Ruang Diskusi Kelas</h2>
                <p class="mt-0.5 text-xs text-slate-500">Percakapan interaktif antara guru dan siswa mengenai materi ini.</p>
            </div>

            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                {{ $diskusis->count() }} Pesan
            </span>
        </div>

        {{-- Area percakapan --}}
        <div id="diskusi-chat"
            class="flex max-h-[60vh] min-h-[360px] flex-col gap-1.5 overflow-y-auto bg-brand-50/50 p-5 sm:p-6"
            style="background-image: radial-gradient(#c3e8cd 1px, transparent 1px); background-size: 22px 22px;"
            x-data
            x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
            x-on:pesan-diskusi-terkirim.window="
                $nextTick(() => $el.scrollTop = $el.scrollHeight)
            ">

            @forelse ($diskusis as $diskusi)
            @php
            $pesanSaya = (int) $diskusi->user_id === (int) auth()->id();
            $nama = $diskusi->user?->name ?? 'Pengguna';
            $tanggal = $diskusi->created_at?->toDateString();
            $hariBaru = $tanggal !== $prevTanggal;
            $ganti = $hariBaru || $prevUserId !== $diskusi->user_id;

            $labelHari = null;
            if ($hariBaru && $diskusi->created_at) {
            $labelHari = $diskusi->created_at->isToday()
            ? 'Hari ini'
            : ($diskusi->created_at->isYesterday()
            ? 'Kemarin'
            : $diskusi->created_at->translatedFormat('d F Y'));
            }
            @endphp

            {{-- Pemisah tanggal --}}
            @if ($labelHari)
            <div class="my-2.5 flex justify-center">
                <span class="rounded-full border border-brand-100 bg-white px-3.5 py-1 text-[11px] font-bold text-brand-700 shadow-sm">
                    {{ $labelHari }}
                </span>
            </div>
            @endif

            <div wire:key="diskusi-{{ $diskusi->id }}"
                class="flex items-end gap-2.5 {{ $pesanSaya ? 'justify-end' : 'justify-start' }} {{ $ganti ? 'mt-2.5' : '' }}">

                {{-- Avatar lawan bicara (hanya di pesan pertama dalam rangkaian) --}}
                @unless ($pesanSaya)
                @if ($ganti)
                <img src="{{ $avatar($nama) }}" alt="{{ $nama }}"
                    class="h-8 w-8 shrink-0 rounded-full shadow-sm ring-2 ring-white">
                @else
                <span class="h-8 w-8 shrink-0"></span>
                @endif
                @endunless

                <div class="max-w-[85%] px-4 py-2.5 shadow-sm sm:max-w-[72%]
                        {{ $pesanSaya
                            ? 'rounded-2xl rounded-br-sm bg-brand-600 text-white shadow-brand-500/15'
                            : 'rounded-2xl rounded-bl-sm border border-brand-100 bg-white text-slate-800' }}">

                    @if (! $pesanSaya && $ganti)
                    <p class="mb-1 text-xs font-extrabold text-brand-700">{{ $nama }}</p>
                    @endif

                    <p class="whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $diskusi->isi }}</p>

                    <p class="mt-1 text-right text-[10px] font-medium {{ $pesanSaya ? 'text-brand-100' : 'text-slate-400' }}"
                        title="{{ $diskusi->created_at?->translatedFormat('d F Y, H:i') }}">
                        {{ $diskusi->created_at?->format('H:i') }}
                    </p>
                </div>
            </div>

            @php
            $prevTanggal = $tanggal;
            $prevUserId = $diskusi->user_id;
            @endphp
            @empty
            <div class="m-auto py-12 text-center">
                <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl border border-brand-100 bg-white text-2xl text-brand-500 shadow-sm">
                    <i class="fa-regular fa-comment-dots"></i>
                </span>
                <p class="mt-4 font-bold text-brand-900">Belum ada diskusi</p>
                <p class="mt-1 text-sm text-slate-500">Mulai percakapan dengan mengirim pesan pertama untuk kelas ini.</p>
            </div>
            @endforelse
        </div>

        {{-- Form kirim pesan --}}
        <form wire:submit="kirimPesan" x-data class="border-t border-brand-100 bg-white p-4 sm:p-5">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <textarea
                        wire:model="pesan"
                        rows="2"
                        maxlength="5000"
                        placeholder="Tulis pesan atau tanggapan untuk siswa..."
                        @keydown.enter="if (! $event.shiftKey) { $event.preventDefault(); $wire.kirimPesan(); }"
                        class="w-full resize-none rounded-2xl border border-brand-200 bg-brand-50/40 px-4 py-3 text-sm text-slate-700 outline-none transition
                               placeholder:text-slate-400 focus:border-brand-400 focus:bg-white focus:ring-2 focus:ring-brand-200"></textarea>
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="kirimPesan"
                    class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-2xl bg-brand-600 px-5 text-sm font-semibold text-white shadow-sm shadow-brand-500/20
                           transition hover:bg-brand-700 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60">
                    <i class="fa-solid fa-paper-plane text-xs" wire:loading.remove wire:target="kirimPesan"></i>
                    <i class="fa-solid fa-circle-notch fa-spin text-xs" wire:loading wire:target="kirimPesan"></i>
                    <span class="hidden sm:inline">Kirim</span>
                </button>
            </div>

            @error('pesan')
            <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-red-600">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
            </p>
            @enderror

            <div class="mt-2 flex items-center justify-between px-1 text-[11px] text-slate-400">
                <span class="hidden sm:inline-flex items-center gap-1.5">
                    <i class="fa-regular fa-keyboard text-brand-400"></i>
                    Tekan <strong>Enter</strong> untuk kirim · <strong>Shift + Enter</strong> untuk baris baru
                </span>
                <span class="ml-auto font-mono" x-text="(($wire.pesan || '').length) + ' / 5000'"></span>
            </div>
        </form>

    </div>
</div>