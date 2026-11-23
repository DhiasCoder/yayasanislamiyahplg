<div class="space-y-6"
    wire:poll.15s="cekWaktu"
    x-data="{
        seconds: @js($sisaDetik),
        timer: null,
        expired: false,

        start() {
            this.timer = setInterval(() => {
                if (this.seconds > 0) {
                    this.seconds--;
                } else if (!this.expired) {
                    this.expired = true;
                    clearInterval(this.timer);
                    $wire.cekWaktu();
                }
            }, 1000);
        },

        formatTime() {
            const minutes = Math.floor(this.seconds / 60);
            const seconds = this.seconds % 60;

            return String(minutes).padStart(2, '0') + ':' +
                   String(seconds).padStart(2, '0');
        }
    }"
    x-init="start()"
    x-on:livewire:navigating.window="clearInterval(timer)">

    @php
    $soal = $questions[$currentIndex] ?? null;
    $jumlahSoal = count($questions);
    $nomor = $currentIndex + 1;

    // Jumlah soal terjawab (nilai awal dari server, lalu diperbarui langsung oleh Alpine)
    $terjawab = collect($questions)
    ->filter(fn ($q) => ($jawaban[$q['id']] ?? null) !== null && ($jawaban[$q['id']] ?? '') !== '')
    ->count();
    @endphp

    @if ($soal && !$sudahSelesai)

    {{-- ================= HEADER (menempel saat di-scroll) ================= --}}
    <div class="sticky top-[4.5rem] z-30 rounded-2xl border border-brand-100 bg-white/95 p-4 shadow-sm backdrop-blur sm:p-5">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-xl text-white shadow-md shadow-brand-500/30">
                    <i class="fa-solid fa-file-pen"></i>
                </span>

                <div>
                    <h1 class="text-lg font-extrabold text-brand-900 sm:text-xl">Pengerjaan Ujian</h1>
                    <p class="mt-0.5 text-sm text-slate-500">
                        Soal {{ $nomor }} dari {{ $jumlahSoal }}
                        <span class="text-brand-300">·</span>
                        Terjawab
                        <span class="font-semibold text-brand-700"
                            x-text="Object.values($wire.jawaban || {}).filter(v => v !== null && v !== '').length">{{ $terjawab }}</span>/{{ $jumlahSoal }}
                    </p>
                </div>
            </div>

            {{-- Timer: berubah merah saat sisa waktu 5 menit atau kurang --}}
            <div class="flex items-center gap-3 self-start rounded-2xl px-5 py-2.5 ring-1 transition-colors sm:self-auto"
                :class="seconds <= 300
                    ? 'bg-red-50 text-red-600 ring-red-200'
                    : 'bg-brand-50 text-brand-700 ring-brand-200'">
                <i class="fa-regular fa-clock text-lg" :class="seconds <= 300 ? 'animate-pulse' : ''"></i>

                <div class="leading-tight">
                    <p class="text-[11px] font-semibold uppercase tracking-wide opacity-80">Sisa Waktu</p>
                    <p class="text-xl font-extrabold tabular-nums" x-text="formatTime()"></p>
                </div>
            </div>
        </div>

        <div class="mt-4 h-2 overflow-hidden rounded-full bg-brand-100">
            <div class="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600 transition-all duration-500"
                style="width: {{ ($nomor / max($jumlahSoal, 1)) * 100 }}%"></div>
        </div>
    </div>

    {{-- Peringatan waktu hampir habis --}}
    <div x-show="seconds <= 300 && seconds > 0" x-cloak
        x-transition
        class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span><strong>Waktu hampir habis.</strong> Pastikan semua jawaban sudah terisi, lalu selesaikan ujian.</span>
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-3">

        {{-- ================= SOAL ================= --}}
        <div wire:key="soal-{{ $soal['id'] }}"
            class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2">

            <div class="mb-5 flex items-center justify-between gap-3">
                <span class="inline-flex items-center gap-2 rounded-lg bg-brand-100 px-3 py-1.5 text-xs font-bold text-brand-700">
                    <i class="fa-solid fa-circle-question"></i>
                    Soal {{ $nomor }}
                </span>

                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                    {{ $soal['tipe_soal'] === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' }}
                </span>
            </div>

            <h2 class="mb-6 whitespace-pre-line text-lg font-semibold leading-relaxed text-slate-800">{{ $soal['pertanyaan'] }}</h2>

            {{-- PILIHAN GANDA --}}
            @if ($soal['tipe_soal'] === 'pilihan_ganda')
            <div class="space-y-3">
                @foreach (['a', 'b', 'c', 'd'] as $opsi)
                @php
                $kolom = 'opsi_' . $opsi;
                $nilaiOpsi = $soal[$kolom] ?? null;
                $terpilih = ($jawaban[$soal['id']] ?? null) === $opsi;
                @endphp
                @if ($nilaiOpsi !== null && $nilaiOpsi !== '')
                <label
                    wire:key="soal-{{ $soal['id'] }}-opsi-{{ $opsi }}"
                    class="group flex cursor-pointer items-start gap-3 rounded-xl border-2 p-4 transition
                {{ $terpilih
                    ? 'border-brand-500 bg-brand-50 shadow-sm'
                    : 'border-slate-200 bg-white hover:border-brand-300 hover:bg-brand-50/50' }}
                has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-300">

                    <input
                        type="radio"
                        name="soal-{{ $soal['id'] }}"
                        wire:model.live="jawaban.{{ $soal['id'] }}"
                        value="{{ $opsi }}"
                        class="sr-only">

                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-sm font-extrabold uppercase transition
                    {{ $terpilih
                        ? 'bg-brand-500 text-white'
                        : 'bg-slate-100 text-slate-500' }}">
                        {{ $opsi }}
                    </span>

                    <span class="pt-1 text-sm leading-relaxed text-slate-700">
                        {{ $nilaiOpsi }}
                    </span>
                </label>
                @endif
                @endforeach
                @error('jawaban.' . $soal['id'])
                <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            {{-- ESSAY --}}
            @elseif ($soal['tipe_soal'] === 'essay')

            <textarea
                wire:model="jawaban.{{ $soal['id'] }}"
                rows="8"
                maxlength="10000"
                placeholder="Tuliskan jawaban kamu di sini..."
                class="w-full resize-y rounded-xl border border-brand-200 bg-brand-50/30 px-4 py-3 text-sm text-slate-700 outline-none transition
                       placeholder:text-slate-400 focus:border-brand-400 focus:bg-white focus:ring-2 focus:ring-brand-200"></textarea>

            <div class="mt-1.5 flex items-start justify-between gap-3">
                <div>
                    @error('jawaban.' . $soal['id'])
                    <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <span class="shrink-0 text-xs text-slate-400"
                    x-text="(($wire.jawaban?.[{{ $soal['id'] }}] ?? '').length) + ' / 10000'"></span>
            </div>

            @endif

            {{-- Tombol navigasi --}}
            <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-brand-100 pt-5">

                <button
                    wire:click="keSoal({{ max(0, $currentIndex - 1) }})"
                    wire:loading.attr="disabled" wire:target="keSoal"
                    @disabled($currentIndex===0)
                    class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50
                           disabled:cursor-not-allowed disabled:opacity-40">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Sebelumnya
                </button>

                @if ($currentIndex < $jumlahSoal - 1)

                    <button
                    wire:click="keSoal({{ $currentIndex + 1 }})"
                    wire:loading.attr="disabled" wire:target="keSoal"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                               hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="keSoal" class="inline-flex items-center gap-2">
                        Simpan &amp; Selanjutnya
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                    <span wire:loading wire:target="keSoal" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                        Menyimpan...
                    </span>
                    </button>

                    @else

                    {{-- Tombol pembuka modal konfirmasi (menggantikan wire:confirm bawaan browser) --}}
                    <div x-data="{
                            open: false,
                            total: {{ $jumlahSoal }},
                            get terjawab() {
                                return Object.values(this.$wire.jawaban || {})
                                    .filter(v => v !== null && v !== '').length;
                            }
                        }"
                        x-on:keydown.escape.window="open = false">

                        <button type="button" @click="open = true"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                                   hover:from-brand-600 hover:to-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2">
                            <i class="fa-solid fa-flag-checkered text-xs"></i>
                            Selesaikan Ujian
                        </button>

                        {{-- ================= MODAL KONFIRMASI ================= --}}
                        <div x-show="open" x-cloak
                            class="fixed inset-0 z-[60] flex items-end justify-center p-4 sm:items-center"
                            role="dialog" aria-modal="true" aria-labelledby="judul-selesai-ujian">

                            {{-- Latar gelap --}}
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                @click="open = false"
                                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

                            {{-- Panel --}}
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                                class="relative z-10 w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">

                                <div class="h-1.5 bg-gradient-to-r from-brand-400 to-brand-600"></div>

                                <div class="p-6 text-center sm:p-7">
                                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-2xl text-white shadow-lg shadow-brand-500/30">
                                        <i class="fa-solid fa-flag-checkered"></i>
                                    </span>

                                    <h3 id="judul-selesai-ujian" class="mt-4 text-xl font-extrabold text-brand-900">
                                        Selesaikan ujian sekarang?
                                    </h3>
                                    <p class="mt-1.5 text-sm text-slate-500">
                                        Jawaban akan dikirim dan tidak bisa diubah lagi.
                                    </p>

                                    {{-- Ringkasan --}}
                                    <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                                        <div class="rounded-xl bg-brand-50 px-2 py-3">
                                            <p class="text-xl font-extrabold text-brand-700" x-text="terjawab"></p>
                                            <p class="text-[11px] font-medium text-brand-600">Terjawab</p>
                                        </div>

                                        <div class="rounded-xl px-2 py-3 transition-colors"
                                            :class="total - terjawab > 0 ? 'bg-amber-50' : 'bg-slate-50'">
                                            <p class="text-xl font-extrabold transition-colors"
                                                :class="total - terjawab > 0 ? 'text-amber-600' : 'text-slate-400'"
                                                x-text="total - terjawab"></p>
                                            <p class="text-[11px] font-medium text-slate-500">Belum dijawab</p>
                                        </div>

                                        <div class="rounded-xl bg-slate-50 px-2 py-3">
                                            <p class="text-xl font-extrabold tabular-nums text-slate-700" x-text="formatTime()"></p>
                                            <p class="text-[11px] font-medium text-slate-500">Sisa waktu</p>
                                        </div>
                                    </div>

                                    {{-- Peringatan jika masih ada soal kosong --}}
                                    <div x-show="total - terjawab > 0" x-cloak
                                        class="mt-4 flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-3 text-left text-xs text-amber-800">
                                        <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                                        <p>
                                            Masih ada <strong x-text="total - terjawab"></strong> soal yang belum dijawab.
                                            Kamu masih bisa kembali untuk melengkapinya sebelum mengirim.
                                        </p>
                                    </div>

                                    <div x-show="total - terjawab === 0" x-cloak
                                        class="mt-4 flex items-center gap-2.5 rounded-xl border border-brand-200 bg-brand-50 px-3.5 py-3 text-left text-xs text-brand-800">
                                        <i class="fa-solid fa-circle-check text-brand-500"></i>
                                        <p>Semua soal sudah terjawab.</p>
                                    </div>

                                    {{-- Tombol --}}
                                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-center">
                                        <button type="button" @click="open = false"
                                            wire:loading.attr="disabled" wire:target="selesaikanUjian"
                                            class="inline-flex items-center justify-center rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50
                                                   disabled:cursor-not-allowed disabled:opacity-50">
                                            Kembali Mengerjakan
                                        </button>

                                        <button type="button"
                                            wire:click="selesaikanUjian"
                                            wire:loading.attr="disabled" wire:target="selesaikanUjian"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition
                                                   hover:from-brand-600 hover:to-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                                            <span wire:loading.remove wire:target="selesaikanUjian" class="inline-flex items-center gap-2">
                                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                                Ya, Selesaikan
                                            </span>
                                            <span wire:loading wire:target="selesaikanUjian" class="inline-flex items-center gap-2">
                                                <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                                                Mengirim...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @endif

            </div>
        </div>

        {{-- ================= NAVIGASI NOMOR SOAL ================= --}}
        <aside class="rounded-2xl border border-brand-100 bg-white p-5 shadow-sm lg:sticky lg:top-48">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h3 class="font-extrabold text-brand-900">Navigasi Soal</h3>
                <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-bold text-brand-700">{{ $jumlahSoal }} soal</span>
            </div>

            <div class="grid grid-cols-5 gap-2 sm:grid-cols-8 lg:grid-cols-5">
                @foreach ($questions as $index => $item)
                @if ($currentIndex === $index)
                <button
                    wire:key="nomor-soal-{{ $item['id'] }}"
                    wire:click="keSoal({{ $index }})"
                    aria-current="true"
                    class="flex h-10 w-full items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white shadow-md shadow-brand-500/30 ring-2 ring-brand-200">
                    {{ $index + 1 }}
                </button>
                @else
                {{-- Status "sudah dijawab" mengikuti isian di browser, jadi langsung berubah saat memilih --}}
                <button
                    wire:key="nomor-soal-{{ $item['id'] }}"
                    wire:click="keSoal({{ $index }})"
                    :class="($wire.jawaban?.[{{ $item['id'] }}] ?? '') !== ''
                                ? 'bg-brand-100 text-brand-700 hover:bg-brand-200'
                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                    class="flex h-10 w-full items-center justify-center rounded-lg text-sm font-semibold transition">
                    {{ $index + 1 }}
                </button>
                @endif
                @endforeach
            </div>

            <div class="mt-5 space-y-2 border-t border-brand-100 pt-4 text-xs text-slate-500">
                <span class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded bg-brand-600"></span>
                    Sedang dibuka
                </span>
                <span class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded bg-brand-100 ring-1 ring-brand-200"></span>
                    Sudah dijawab
                </span>
                <span class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded bg-slate-100 ring-1 ring-slate-200"></span>
                    Belum dijawab
                </span>
            </div>
        </aside>
    </div>

    @else

    {{-- ================= UJIAN SELESAI ================= --}}
    <div class="relative overflow-hidden rounded-3xl border border-brand-100 bg-white px-6 py-14 text-center shadow-sm">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-brand-100/70"></div>
        <div class="pointer-events-none absolute -bottom-16 -left-10 h-44 w-44 rounded-full bg-brand-100/50"></div>

        <div class="relative">
            <div class="mx-auto mb-5 grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-4xl text-white shadow-lg shadow-brand-500/30">
                <i class="fa-solid fa-check"></i>
            </div>

            <h2 class="text-2xl font-extrabold text-brand-900">Ujian Selesai</h2>

            <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                Jawaban ujian telah dikirim.
            </p>

            <a href="{{ Route::has('siswa.ujian') ? route('siswa.ujian') : url('/') }}"
                class="mt-7 inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-500/30 transition hover:bg-brand-600">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Kembali ke Daftar Ujian
            </a>
        </div>
    </div>

    @endif

</div>