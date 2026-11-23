@php
    $backUrl = Route::has('siswa.materi') ? route('siswa.materi') : url('/');
    $me      = auth()->user();
    $avatar  = fn ($name) => 'https://ui-avatars.com/api/?name=' . urlencode($name ?: 'Pengguna') . '&background=3a9d5d&color=fff&bold=true';

    $total = $diskusi instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
        ? $diskusi->total()
        : $diskusi->count();
@endphp

{{-- Livewire: elemen root harus satu --}}
<div class="space-y-6">

    <a href="{{ $backUrl }}"
        onclick="if (history.length > 1) { history.back(); return false; }"
        class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700 transition">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke materi
    </a>

    <div class="grid items-start gap-6 lg:grid-cols-3">

        {{-- ================= KOLOM UTAMA ================= --}}
        <div class="min-w-0 space-y-6 lg:col-span-2">

            {{-- Header --}}
            <section class="relative overflow-hidden rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-brand-100/70 blur-2xl"></div>

                <div class="relative flex items-start gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-xl text-white shadow-md shadow-brand-500/30">
                        <i class="fa-solid fa-comments"></i>
                    </span>

                    <div class="min-w-0">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">
                            Diskusi Materi
                        </span>
                        <h2 class="mt-2 text-xl font-extrabold leading-tight text-brand-900 sm:text-2xl">
                            {{ $materi->judul }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">Ajukan pertanyaan atau berikan tanggapan seputar materi ini.</p>
                    </div>
                </div>
            </section>

            {{-- Notifikasi sukses --}}
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

            {{-- Form diskusi --}}
            <form wire:submit="kirimDiskusi" class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <img src="{{ $avatar($me->name ?? '') }}" alt="{{ $me->name ?? 'Anda' }}"
                        class="hidden h-10 w-10 shrink-0 rounded-full ring-2 ring-brand-200 sm:block">

                    <div class="min-w-0 flex-1">
                        <label for="isi" class="mb-2 block text-sm font-semibold text-brand-900">Tulis diskusi</label>

                        <textarea id="isi" wire:model="isi" rows="4"
                            class="w-full resize-none rounded-xl border bg-brand-50/40 px-4 py-3 text-sm text-slate-700 outline-none transition
                                   placeholder:text-slate-400 focus:bg-white focus:ring-2
                                   @error('isi') border-red-300 focus:border-red-400 focus:ring-red-100 @else border-brand-200 focus:border-brand-400 focus:ring-brand-200 @enderror"
                            placeholder="Tulis pertanyaan atau tanggapan..."></textarea>

                        @error('isi')
                            <p class="mt-1.5 flex items-center gap-1.5 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation text-xs"></i> {{ $message }}
                            </p>
                        @enderror

                        <div class="mt-3 flex items-center justify-between gap-3">
                            <span class="text-xs text-slate-400" x-data x-text="(($wire.isi || '').length) + ' karakter'"></span>

                            <button type="submit" wire:loading.attr="disabled" wire:target="kirimDiskusi"
                                class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30
                                       transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                                <i class="fa-solid fa-paper-plane text-xs" wire:loading.remove wire:target="kirimDiskusi"></i>
                                <i class="fa-solid fa-circle-notch fa-spin text-xs" wire:loading wire:target="kirimDiskusi"></i>
                                <span wire:loading.remove wire:target="kirimDiskusi">Kirim Diskusi</span>
                                <span wire:loading wire:target="kirimDiskusi">Mengirim…</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Daftar diskusi --}}
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-bold text-brand-900">Percakapan</h3>
                    <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-semibold text-brand-700">{{ $total }} diskusi</span>
                </div>

                <div class="space-y-4">
                    @forelse ($diskusi as $item)
                        @php
                            $nama  = $item->user->name ?? 'Pengguna';
                            $milik = auth()->check() && ($item->user_id ?? null) == auth()->id();
                        @endphp

                        <article wire:key="diskusi-{{ $item->id }}"
                            class="flex gap-3 rounded-2xl border bg-white p-5 shadow-sm transition
                                   {{ $milik ? 'border-brand-300 bg-brand-50/40' : 'border-brand-100' }}">
                            <img src="{{ $avatar($nama) }}" alt="{{ $nama }}"
                                class="h-10 w-10 shrink-0 rounded-full ring-2 {{ $milik ? 'ring-brand-400' : 'ring-brand-100' }}">

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                    <p class="font-semibold text-brand-900">{{ $nama }}</p>

                                    @if ($milik)
                                        <span class="rounded-full bg-brand-500 px-2 py-0.5 text-[10px] font-semibold text-white">Anda</span>
                                    @endif

                                    <span class="text-xs text-slate-400"
                                        title="{{ $item->created_at?->translatedFormat('d F Y, H:i') }}">
                                        · {{ $item->created_at?->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="mt-2 whitespace-pre-line break-words leading-relaxed text-slate-600">{{ $item->isi }}</p>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-brand-200 bg-white/70 px-6 py-12 text-center">
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-100 text-3xl text-brand-500">
                                <i class="fa-regular fa-comment-dots"></i>
                            </span>
                            <p class="mt-4 font-semibold text-brand-900">Belum ada diskusi untuk materi ini</p>
                            <p class="mt-1 text-sm text-slate-500">Jadilah yang pertama bertanya atau memberi tanggapan.</p>
                        </div>
                    @endforelse
                </div>

                @if (method_exists($diskusi, 'links'))
                    <div class="mt-5">{{ $diskusi->links() }}</div>
                @endif
            </div>
        </div>

        {{-- ================= KOLOM SAMPING ================= --}}
        <aside class="space-y-6 lg:sticky lg:top-24">
            <section class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm">
                <h3 class="flex items-center gap-2 font-bold text-brand-900">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-100 text-brand-600">
                        <i class="fa-solid fa-lightbulb"></i>
                    </span>
                    Etika Berdiskusi
                </h3>

                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-check mt-0.5 text-xs text-brand-500"></i>
                        Gunakan bahasa yang sopan dan jelas.
                    </li>
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-check mt-0.5 text-xs text-brand-500"></i>
                        Pastikan pertanyaan berkaitan dengan materi.
                    </li>
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-check mt-0.5 text-xs text-brand-500"></i>
                        Baca diskusi sebelumnya agar tidak mengulang pertanyaan.
                    </li>
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-check mt-0.5 text-xs text-brand-500"></i>
                        Hargai pendapat teman dan guru.
                    </li>
                </ul>
            </section>

            <section class="rounded-2xl border border-brand-100 bg-brand-50 p-5">
                <div class="flex items-start gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white text-brand-500 shadow-sm">
                        <i class="fa-solid fa-circle-question"></i>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-brand-800">Pertanyaanmu belum terjawab?</p>
                        <p class="mt-0.5 text-xs text-slate-500">Guru pengampu akan menanggapi diskusi secara berkala.</p>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>