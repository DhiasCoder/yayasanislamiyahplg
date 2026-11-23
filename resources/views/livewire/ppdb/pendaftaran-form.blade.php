@php
/* ------------------------------------------------------------------
| Ikon (SVG sederhana, tanpa dependensi)
------------------------------------------------------------------ */
$icons = [
'user' => '
<circle cx="12" cy="8" r="4" />
<path d="M4 20c0-4 4-6 8-6s8 2 8 6" />',
'pin' => '
<path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z" />
<circle cx="12" cy="10" r="2.5" />',
'cap' => '
<path d="M2 9l10-5 10 5-10 5L2 9z" />
<path d="M6 11.5V16c0 1.5 3 3 6 3s6-1.5 6-3v-4.5" />',
'users' => '
<circle cx="9" cy="8" r="3.5" />
<path d="M2 20c0-3.5 3-5.5 7-5.5s7 2 7 5.5" />
<circle cx="17" cy="9" r="2.5" />
<path d="M17 14.5c2.8.2 5 1.8 5 4.5" />',
'check' => '
<rect x="5" y="4" width="14" height="17" rx="2" />
<path d="M9 4h6v3H9z" />
<path d="M9 14l2 2 4-4" />',
];

/* ------------------------------------------------------------------
| Struktur langkah & field (isi field sama dengan versi sebelumnya)
------------------------------------------------------------------ */
$steps = [
1 => [
'title' => 'Pribadi',
'summary' => 'Data Pribadi',
'heading' => 'Data Pribadi',
'desc' => 'Lengkapi identitas calon peserta didik.',
'icon' => 'user',
'groups' => [
[
'title' => 'Identitas Diri',
'fields' => [
['name' => 'nama_lengkap', 'label' => 'Nama Lengkap', 'req' => true, 'span' => 2, 'placeholder' => 'Sesuai akta kelahiran / ijazah', 'autocomplete' => 'name'],
['name' => 'nik', 'label' => 'NIK', 'inputmode' => 'numeric', 'maxlength' => 16, 'placeholder' => '16 digit angka', 'hint' => 'Tertera pada Kartu Keluarga / akta kelahiran.'],
['name' => 'nisn', 'label' => 'NISN', 'inputmode' => 'numeric', 'maxlength' => 10, 'placeholder' => '10 digit angka'],
['name' => 'jenis_kelamin', 'label' => 'Jenis Kelamin', 'type' => 'select', 'placeholder' => 'Pilih jenis kelamin', 'options' => ['L' => 'Laki-laki', 'P' => 'Perempuan']],
['name' => 'agama', 'label' => 'Agama', 'type' => 'select', 'placeholder' => 'Pilih agama', 'options' => ['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']],
['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'placeholder' => 'Contoh: Palembang'],
['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'type' => 'date'],
['name' => 'kewarganegaraan', 'label' => 'Kewarganegaraan'],
],
],
[
'title' => 'Keluarga & Minat',
'fields' => [
['name' => 'anak_ke', 'label' => 'Anak Ke-', 'type' => 'number', 'min' => 1, 'inputmode' => 'numeric'],
['name' => 'status_orang_tua', 'label' => 'Status Orang Tua', 'type' => 'select', 'placeholder' => 'Pilih status', 'options' => ['lengkap' => 'Lengkap', 'yatim' => 'Yatim', 'piatu' => 'Piatu', 'yatim_piatu' => 'Yatim Piatu']],
['name' => 'bakat_hobi', 'label' => 'Bakat / Hobi', 'span' => 2, 'placeholder' => 'Contoh: Membaca, sepak bola'],
],
],
],
],

2 => [
'title' => 'Domisili',
'summary' => 'Tempat Tinggal',
'heading' => 'Data Tempat Tinggal',
'desc' => 'Informasi alamat dan kontak calon peserta didik.',
'icon' => 'pin',
'groups' => [
[
'title' => 'Alamat',
'fields' => [
['name' => 'alamat', 'label' => 'Alamat Lengkap', 'type' => 'textarea', 'span' => 2, 'placeholder' => 'Jalan, RT/RW, kelurahan, kecamatan'],
['name' => 'status_tempat_tinggal', 'label' => 'Status Tempat Tinggal', 'type' => 'select', 'placeholder' => 'Pilih status', 'options' => ['orang_tua' => 'Bersama Orang Tua', 'menumpang' => 'Menumpang', 'kos' => 'Kos', 'asrama' => 'Asrama']],
['name' => 'jarak_rumah', 'label' => 'Jarak Rumah ke Sekolah', 'placeholder' => 'Contoh: 5 km'],
],
],
[
'title' => 'Kontak',
'fields' => [
['name' => 'no_telepon', 'label' => 'Nomor Telepon', 'type' => 'tel', 'inputmode' => 'tel', 'placeholder' => 'Telepon rumah (jika ada)'],
['name' => 'no_hp', 'label' => 'Nomor HP', 'req' => true, 'type' => 'tel', 'inputmode' => 'tel', 'placeholder' => '08xxxxxxxxxx', 'hint' => 'Nomor aktif yang bisa dihubungi panitia.'],
['name' => 'email', 'label' => 'Email', 'type' => 'email', 'span' => 2, 'placeholder' => 'nama@email.com', 'autocomplete' => 'email'],
],
],
],
],

3 => [
'title' => 'Pendidikan',
'summary' => 'Pendidikan',
'heading' => 'Data Pendidikan',
'desc' => 'Informasi riwayat pendidikan sebelumnya.',
'icon' => 'cap',
'groups' => [
[
'title' => 'Riwayat Pendidikan',
'fields' => [
['name' => 'asal_sekolah', 'label' => 'Asal Sekolah', 'span' => 2, 'placeholder' => 'Nama sekolah sebelumnya'],
['name' => 'nomor_ijazah', 'label' => 'Nomor Ijazah'],
['name' => 'tanggal_ijazah', 'label' => 'Tanggal Ijazah', 'type' => 'date'],
['name' => 'jumlah_skhun', 'label' => 'Jumlah Nilai SKHUN', 'type' => 'number', 'step' => '0.01', 'min' => 0, 'inputmode' => 'decimal'],
],
],
[
'title' => 'Data Fisik',
'fields' => [
['name' => 'berat_badan', 'label' => 'Berat Badan (kg)', 'type' => 'number', 'step' => '0.1', 'min' => 0, 'inputmode' => 'decimal', 'format' => 'kg'],
['name' => 'tinggi_badan', 'label' => 'Tinggi Badan (cm)', 'type' => 'number', 'step' => '0.1', 'min' => 0, 'inputmode' => 'decimal', 'format' => 'cm'],
['name' => 'golongan_darah', 'label' => 'Golongan Darah', 'type' => 'select', 'placeholder' => 'Pilih golongan darah', 'options' => ['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O']],
],
],
[
'title' => 'Pendaftaran',
'fields' => [
['name' => 'masuk_kelas', 'label' => 'Mendaftar ke Kelas', 'placeholder' => 'Contoh: VII'],
],
],
],
],

4 => [
'title' => 'Orang Tua',
'summary' => 'Orang Tua / Wali',
'heading' => 'Data Orang Tua / Wali',
'desc' => 'Lengkapi informasi orang tua atau wali calon peserta didik.',
'icon' => 'users',
'groups' => [
[
'title' => 'Data Ayah',
'fields' => [
['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'span' => 2],
['name' => 'tempat_lahir_ayah', 'label' => 'Tempat Lahir Ayah'],
['name' => 'tanggal_lahir_ayah', 'label' => 'Tanggal Lahir Ayah', 'type' => 'date'],
['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah'],
['name' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah'],
['name' => 'penghasilan_ayah', 'label' => 'Penghasilan Ayah', 'type' => 'number', 'min' => 0, 'inputmode' => 'numeric', 'placeholder' => 'Rp per bulan', 'format' => 'rupiah'],
],
],
[
'title' => 'Data Ibu',
'fields' => [
['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'span' => 2],
['name' => 'tempat_lahir_ibu', 'label' => 'Tempat Lahir Ibu'],
['name' => 'tanggal_lahir_ibu', 'label' => 'Tanggal Lahir Ibu', 'type' => 'date'],
['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu'],
['name' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu'],
['name' => 'penghasilan_ibu', 'label' => 'Penghasilan Ibu', 'type' => 'number', 'min' => 0, 'inputmode' => 'numeric', 'placeholder' => 'Rp per bulan', 'format' => 'rupiah'],
],
],
[
'title' => 'Data Wali (Opsional)',
'fields' => [
['name' => 'nama_wali', 'label' => 'Nama Wali'],
['name' => 'hubungan_wali', 'label' => 'Hubungan dengan Wali', 'placeholder' => 'Contoh: Paman'],
['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali'],
['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali'],
['name' => 'penghasilan_wali', 'label' => 'Penghasilan Wali', 'type' => 'number', 'min' => 0, 'inputmode' => 'numeric', 'placeholder' => 'Rp per bulan', 'format' => 'rupiah'],
],
],
[
'title' => 'Alamat & Kewarganegaraan',
'fields' => [
['name' => 'alamat_orang_tua_wali', 'label' => 'Alamat Orang Tua / Wali', 'type' => 'textarea', 'span' => 2],
['name' => 'kewarganegaraan_orang_tua', 'label' => 'Kewarganegaraan Orang Tua'],
],
],
],
],

5 => [
'title' => 'Konfirmasi',
'summary' => 'Konfirmasi',
'heading' => 'Konfirmasi Data',
'desc' => 'Periksa kembali data sebelum menyelesaikan pendaftaran.',
'icon' => 'check',
'groups' => [],
],
];

/* Peta key error -> label & langkah (untuk ringkasan error) */
$fieldMap = ['setuju' => ['label' => 'Pernyataan persetujuan', 'step' => 5]];
foreach ($steps as $n => $s) {
foreach ($s['groups'] as $g) {
foreach ($g['fields'] as $f) {
$fieldMap['form.' . $f['name']] = ['label' => $f['label'], 'step' => $n];
}
}
}

/* Format nilai untuk halaman konfirmasi */
$tampil = function (array $f, $v) {
if (! filled($v)) {
return null;
}

$type = $f['type'] ?? 'text';

if ($type === 'select') {
return $f['options'][$v] ?? $v;
}

if ($type === 'date') {
try {
return \Carbon\Carbon::parse($v)->translatedFormat('d F Y');
} catch (\Throwable $e) {
return $v;
}
}

return match ($f['format'] ?? null) {
'rupiah' => is_numeric($v) ? 'Rp ' . number_format((float) $v, 0, ',', '.') : $v,
'kg' => $v . ' kg',
'cm' => $v . ' cm',
default => $v,
};
};

$current = $steps[$step];
$tutup = filled($periode->tanggal_selesai ?? null) ? \Carbon\Carbon::parse($periode->tanggal_selesai) : null;
@endphp

<div class="min-h-screen bg-gradient-to-b from-green-50 via-white to-green-50/40 px-4 py-8 sm:py-12">
    <div class="mx-auto max-w-4xl">

        {{-- ================= HEADER ================= --}}
        <section class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-green-500 to-emerald-600 p-6 text-white shadow-lg shadow-green-600/20 sm:p-8">
            <div class="pointer-events-none absolute -right-12 -top-12 h-52 w-52 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-16 right-1/3 h-40 w-40 rounded-full bg-white/10"></div>

            <div class="relative">
                <p class="text-xs font-bold uppercase tracking-widest text-green-100">Pendaftaran Peserta Didik Baru</p>

                <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Formulir Pendaftaran</h1>

                <p class="mt-2 text-sm text-green-50">
                    {{ $periode->nama ?? 'Periode PPDB' }}
                    @if ($periode->sekolah)
                    · {{ $periode->sekolah->nama ?? 'Sekolah' }}
                    @endif
                </p>

                @if ($tutup)
                <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-xs font-medium backdrop-blur">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2" />
                        <path d="M3 10h18M8 3v4M16 3v4" />
                    </svg>
                    Pendaftaran ditutup {{ $tutup->translatedFormat('d F Y') }}
                </div>
                @endif
            </div>
        </section>

        {{-- ================= STEP INDICATOR ================= --}}
        <div class="mb-6 scroll-mt-24 rounded-3xl border border-green-100 bg-white p-5 shadow-sm"
            x-data
            x-init="$watch('$wire.step', () => $nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start' })))">

            <ol class="flex items-start">
                @foreach ($steps as $number => $s)
                @php
                $selesai = $step > $number;
                $aktif = $step === $number;
                @endphp

                <li class="relative flex-1">
                    @unless ($loop->last)
                    <span class="absolute left-1/2 top-[1.125rem] h-0.5 w-full {{ $selesai ? 'bg-green-500' : 'bg-slate-200' }}" aria-hidden="true"></span>
                    @endunless

                    <button type="button" wire:click="goToStep({{ $number }})" @disabled($number> $step)
                        aria-label="Langkah {{ $number }}: {{ $s['title'] }}"
                        @if ($aktif) aria-current="step" @endif
                        class="relative z-10 mx-auto grid h-9 w-9 place-items-center rounded-full text-sm font-bold transition
                        {{ $aktif
                                    ? 'bg-green-600 text-white ring-4 ring-green-100'
                                    : ($selesai
                                        ? 'bg-green-500 text-white hover:bg-green-600'
                                        : 'cursor-not-allowed bg-slate-100 text-slate-400') }}">
                        @if ($selesai)
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        @else
                        {{ $number }}
                        @endif
                    </button>

                    <span class="mt-2 hidden text-center text-xs font-semibold sm:block {{ $aktif ? 'text-green-700' : ($selesai ? 'text-green-600' : 'text-slate-400') }}">
                        {{ $s['title'] }}
                    </span>
                </li>
                @endforeach
            </ol>

            <div class="mt-4 flex items-center justify-between gap-3 text-xs">
                <p class="font-semibold text-green-700">
                    Langkah {{ $step }} dari {{ count($steps) }}
                    <span class="font-medium text-slate-400">· {{ $current['title'] }}</span>
                </p>

                <p class="flex items-center gap-1.5 text-slate-400">
                    <svg class="h-3.5 w-3.5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                    Draf tersimpan otomatis
                </p>
            </div>

            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-green-100">
                <div class="h-full rounded-full bg-gradient-to-r from-green-400 to-green-600 transition-all duration-500"
                    style="width: {{ ($step / count($steps)) * 100 }}%"></div>
            </div>
        </div>

        {{-- ================= FORM CONTENT ================= --}}
        <div class="rounded-3xl border border-green-100 bg-white p-5 shadow-sm sm:p-8">

            {{-- Info singkat --}}
            @if ($step === 1)
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-800">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 8h.01M11 12h1v4h1" />
                </svg>
                <p>
                    Kolom bertanda <span class="font-bold text-red-500">*</span> wajib diisi.
                    Data Anda tersimpan otomatis, jadi aman jika halaman dimuat ulang dan bisa dilanjutkan kapan saja.
                </p>
            </div>
            @endif

            {{-- Ringkasan error --}}
            @if ($errors->any())
            @php
            $daftarError = collect($errors->keys())->map(fn ($k) => [
            'label' => $fieldMap[$k]['label'] ?? $k,
            'step' => $fieldMap[$k]['step'] ?? $step,
            'msg' => $errors->first($k),
            ]);
            @endphp

            <div x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'center' }))"
                class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4" role="alert">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 8v5M12 16h.01" />
                    </svg>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-red-800">Ada isian yang perlu diperbaiki</p>

                        <ul class="mt-2 space-y-1.5">
                            @foreach ($daftarError->take(8) as $e)
                            <li class="text-sm text-red-700">
                                <span class="font-semibold">{{ $e['label'] }}:</span>
                                {{ $e['msg'] }}
                                @if ($e['step'] !== $step)
                                <button type="button" wire:click="goToStep({{ $e['step'] }})"
                                    class="ml-1 font-semibold underline underline-offset-2 hover:text-red-900">
                                    Perbaiki
                                </button>
                                @endif
                            </li>
                            @endforeach
                        </ul>

                        @if ($daftarError->count() > 8)
                        <p class="mt-2 text-xs text-red-600">dan {{ $daftarError->count() - 8 }} isian lainnya.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Isi langkah (di-key supaya animasi berjalan tiap pindah langkah) --}}
            <div wire:key="step-{{ $step }}" class="ppdb-step">

                {{-- Judul langkah --}}
                <div class="mb-6 flex items-center gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-green-100 text-green-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$current['icon']] !!}</svg>
                    </span>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-800">{{ $current['heading'] }}</h2>
                        <p class="mt-0.5 text-sm text-slate-500">{{ $current['desc'] }}</p>
                    </div>
                </div>

                {{-- ============ LANGKAH 1 - 4: FORM ============ --}}
                @if ($step < 5)
                    <div class="space-y-5">
                    @foreach ($current['groups'] as $group)
                    <section class="rounded-2xl border border-green-100 bg-green-50/40 p-4 sm:p-5">
                        <h3 class="mb-4 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-green-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                            {{ $group['title'] }}
                        </h3>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            @foreach ($group['fields'] as $f)
                            @php
                            $key = 'form.' . $f['name'];
                            $type = $f['type'] ?? 'text';
                            $id = 'f-' . $f['name'];
                            $bad = $errors->has($key);
                            @endphp

                            <div class="{{ ($f['span'] ?? 1) === 2 ? 'md:col-span-2' : '' }}">
                                <label for="{{ $id }}" class="ppdb-label">
                                    {{ $f['label'] }}
                                    @if ($f['req'] ?? false)
                                    <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @if ($type === 'select')
                                <select id="{{ $id }}" wire:model.blur="{{ $key }}"
                                    aria-invalid="{{ $bad ? 'true' : 'false' }}"
                                    class="ppdb-input {{ $bad ? 'is-invalid' : '' }}">
                                    <option value="">{{ $f['placeholder'] ?? 'Pilih' }}</option>
                                    @foreach ($f['options'] as $val => $lab)
                                    <option value="{{ $val }}">{{ $lab }}</option>
                                    @endforeach
                                </select>
                                @elseif ($type === 'textarea')
                                <textarea id="{{ $id }}" rows="3" wire:model.blur="{{ $key }}"
                                    placeholder="{{ $f['placeholder'] ?? '' }}"
                                    aria-invalid="{{ $bad ? 'true' : 'false' }}"
                                    class="ppdb-input resize-none {{ $bad ? 'is-invalid' : '' }}"></textarea>
                                @else
                                <input id="{{ $id }}" type="{{ $type }}" wire:model.blur="{{ $key }}"
                                    placeholder="{{ $f['placeholder'] ?? '' }}"
                                    aria-invalid="{{ $bad ? 'true' : 'false' }}"
                                    @isset($f['inputmode']) inputmode="{{ $f['inputmode'] }}" @endisset
                                    @isset($f['maxlength']) maxlength="{{ $f['maxlength'] }}" @endisset
                                    @isset($f['min']) min="{{ $f['min'] }}" @endisset
                                    @isset($f['step']) step="{{ $f['step'] }}" @endisset
                                    @isset($f['autocomplete']) autocomplete="{{ $f['autocomplete'] }}" @endisset
                                    class="ppdb-input {{ $bad ? 'is-invalid' : '' }}">
                                @endif

                                @isset($f['hint'])
                                @unless ($bad)
                                <p class="ppdb-hint">{{ $f['hint'] }}</p>
                                @endunless
                                @endisset

                                @error($key)
                                <p class="ppdb-error" role="alert">
                                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 8v5M12 16h.01" />
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endforeach
            </div>
            @endif

            {{-- ============ LANGKAH 5: KONFIRMASI ============ --}}
            @if ($step === 5)
            <div class="space-y-4">
                @foreach ($steps as $n => $s)
                @continue($n === 5)

                <div class="overflow-hidden rounded-2xl border border-green-100">
                    <div class="flex items-center justify-between bg-green-50 px-4 py-3">
                        <h3 class="flex items-center gap-2 font-bold text-green-800">
                            <svg class="h-5 w-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$s['icon']] !!}</svg>
                            {{ $s['summary'] }}
                        </h3>

                        <button type="button" wire:click="goToStep({{ $n }})"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold text-green-600 transition hover:bg-green-100 hover:text-green-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 20h4L19 9l-4-4L4 16v4z" />
                            </svg>
                            Ubah
                        </button>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 p-4 sm:grid-cols-2">
                        @foreach ($s['groups'] as $group)
                        @foreach ($group['fields'] as $f)
                        @php $nilai = $tampil($f, $form[$f['name']] ?? null); @endphp

                        <div class="{{ ($f['span'] ?? 1) === 2 ? 'sm:col-span-2' : '' }}">
                            <dt class="text-xs text-slate-400">{{ $f['label'] }}</dt>
                            <dd class="mt-0.5 break-words text-sm font-medium {{ $nilai ? 'text-slate-700' : 'text-slate-300' }}">
                                {{ $nilai ?? '-' }}
                            </dd>
                        </div>
                        @endforeach
                        @endforeach
                    </dl>
                </div>
                @endforeach
            </div>

            {{-- Pernyataan --}}
            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <label class="flex cursor-pointer gap-3 text-sm text-amber-900">
                    <input type="checkbox" wire:model="setuju" class="mt-0.5 h-5 w-5 shrink-0 accent-green-600">
                    <span>
                        Saya menyatakan bahwa data yang saya isi adalah benar
                        dan dapat dipertanggungjawabkan.
                    </span>
                </label>

                @error('setuju')
                <p class="ppdb-error" role="alert">{{ $message }}</p>
                @enderror
            </div>
            @endif
        </div>

        {{-- ================= NAVIGASI ================= --}}
        <div class="sticky bottom-0 z-10 -mx-5 -mb-5 mt-8 flex items-center justify-between gap-3 rounded-b-3xl border-t border-green-100 bg-white/95 px-5 py-4 backdrop-blur sm:-mx-8 sm:-mb-8 sm:px-8">

            @if ($step > 1)
            <button type="button" wire:click="previousStep" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-xl border border-green-200 bg-white px-5 py-2.5 text-sm font-semibold text-green-700 transition hover:bg-green-50 disabled:opacity-50">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5m6-6l-6 6 6 6" />
                </svg>
                Sebelumnya
            </button>
            @else
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5m6-6l-6 6 6 6" />
                </svg>
                Kembali
            </a>
            @endif

            @if ($step < 5)
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-600/30 transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                <span wire:loading.remove wire:target="nextStep" class="inline-flex items-center gap-2">
                    Selanjutnya
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14m-6-6l6 6-6 6" />
                    </svg>
                </span>
                <span wire:loading wire:target="nextStep" class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25" />
                        <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                    </svg>
                    Memeriksa...
                </span>
                </button>
                @else
                <button type="button" wire:click="simpan" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-600/30 transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="simpan" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                        Kirim Pendaftaran
                    </span>
                    <span wire:loading wire:target="simpan" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25" />
                            <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                        </svg>
                        Menyimpan...
                    </span>
                </button>
                @endif
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-slate-400">
        Pastikan semua data yang dimasukkan sudah benar.
    </p>
</div>

<style>
    .ppdb-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.4rem;
    }

    .ppdb-input {
        width: 100%;
        border: 1px solid #d3e3d8;
        border-radius: 0.75rem;
        padding: 0.7rem 0.9rem;
        font-size: 0.875rem;
        color: #1e293b;
        background: #fff;
        outline: none;
        transition: border-color .15s, box-shadow .15s, background-color .15s;
    }

    .ppdb-input::placeholder {
        color: #94a3b8;
    }

    .ppdb-input:hover {
        border-color: #86c99b;
    }

    .ppdb-input:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgb(22 163 74 / 15%);
    }

    .ppdb-input.is-invalid {
        border-color: #f87171;
        background-color: #fef2f2;
    }

    .ppdb-input.is-invalid:focus {
        box-shadow: 0 0 0 3px rgb(248 113 113 / 22%);
    }

    select.ppdb-input {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 2.4rem;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'><path d='M5.5 7.5 10 12l4.5-4.5' stroke='%2316a34a' stroke-width='1.8' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>");
        background-repeat: no-repeat;
        background-position: right 0.8rem center;
        background-size: 1.1rem;
    }

    .ppdb-hint {
        margin-top: 0.3rem;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .ppdb-error {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.35rem;
        font-size: 0.75rem;
        color: #dc2626;
    }

    @@keyframes ppdb-fade {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .ppdb-step {
        animation: ppdb-fade .28s ease;
    }

    @@media (prefers-reduced-motion: reduce) {
        .ppdb-step {
            animation: none;
        }
    }
</style>
</div>