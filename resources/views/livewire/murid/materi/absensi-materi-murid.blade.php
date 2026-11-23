<div class="space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ url()->previous() }}"
            class="mb-3 inline-flex items-center gap-2 text-sm text-slate-500 hover:text-brand-600">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
        <h2 class="text-2xl font-bold text-brand-900">
            Absensi Materi
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ $materi->judul }}
        </p>
    </div>

    {{-- Notifikasi --}}
    @if (session()->has('success'))
    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
        <i class="fa-solid fa-circle-check mr-2"></i>
        {{ session('success') }}
    </div>
    @endif

    @error('absensi')
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600">
        {{ $message }}
    </div>
    @enderror

    {{-- Informasi Absensi --}}
    <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">

        <div class="mb-5 flex items-center justify-between">
            <h3 class="font-bold text-brand-900">
                Status Kehadiran
            </h3>

            @if ($absensi)
            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                Absensi Dibuka
            </span>
            @else
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                Belum Dibuka
            </span>
            @endif
        </div>

        @if ($absensi)

        <div class="space-y-4">

            <div class="rounded-xl bg-brand-50 p-4">
                <p class="text-sm text-slate-500">Tanggal Absensi</p>
                <p class="mt-1 font-semibold text-brand-900">
                    {{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}
                </p>

                @if ($absensi->jam_mulai && $absensi->jam_selesai)
                <p class="mt-2 text-sm text-slate-500">
                    Waktu:
                    {{ $absensi->jam_mulai }} - {{ $absensi->jam_selesai }}
                </p>
                @endif
            </div>

            @if ($statusAbsensi)

            <div class="rounded-xl border border-green-200 bg-green-50 p-5 text-center">
                <i class="fa-solid fa-circle-check text-3xl text-green-600"></i>

                <p class="mt-3 font-bold text-green-700">
                    Kehadiran Berhasil Dicatat
                </p>

                <p class="mt-1 text-sm text-green-600">
                    Status kamu:
                    <span class="font-bold uppercase">
                        {{ $statusAbsensi }}
                    </span>
                </p>

                {{-- Waktu Absen --}}
                <div class="mt-4 border-t border-green-200 pt-4">
                    <p class="text-xs text-green-700">
                        Waktu Kehadiran
                    </p>

                    <p class="mt-1 font-semibold text-green-800">
                        <i class="fa-regular fa-clock mr-1"></i>

                        {{ \Carbon\Carbon::parse(
                    \App\Models\AbsensiSiswa::where('absensi_id', $absensi->id)
                        ->where('siswa_id', Auth::user()->siswa->id)
                        ->value('waktu_absen')
                )->translatedFormat('d F Y, H:i') }}
                        WIB
                    </p>
                </div>
            </div>

            @else

            <div class="py-4 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-brand-100 text-brand-600">
                    <i class="fa-solid fa-clipboard-user text-2xl"></i>
                </div>

                <h4 class="mt-4 font-bold text-slate-800">
                    Konfirmasi Kehadiran
                </h4>

                <p class="mt-1 text-sm text-slate-500">
                    Tekan tombol berikut untuk mencatat kehadiranmu.
                </p>

                <button
                    wire:click="absenHadir"
                    wire:loading.attr="disabled"
                    class="mt-5 rounded-xl bg-brand-600 px-6 py-3 font-semibold text-white transition hover:bg-brand-700 disabled:opacity-50">

                    <span wire:loading.remove wire:target="absenHadir">
                        <i class="fa-solid fa-check mr-2"></i>
                        Saya Hadir
                    </span>

                    <span wire:loading wire:target="absenHadir">
                        Memproses...
                    </span>
                </button>
            </div>

            @endif

        </div>

        @else
        <div class="py-10 text-center">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-slate-100 text-slate-400">
                <i class="fa-solid fa-calendar-xmark text-2xl"></i>
            </div>
            <h4 class="mt-4 font-bold text-slate-700">
                Absensi Belum Dibuka
            </h4>
            <p class="mt-1 text-sm text-slate-500">
                Silakan menunggu guru membuka sesi absensi.
            </p>
        </div>
        @endif
    </section>
</div>