@php
$linkClass = fn ($active) => 'sb-link flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all '
. ($active
? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30'
: 'text-slate-600 hover:bg-brand-50 hover:text-brand-700');

$iconClass = fn ($active) => 'w-5 text-center shrink-0 ' . ($active ? 'text-white' : 'text-brand-500');

/*
* Deteksi menu aktif yang lebih andal: cocokkan nama route (termasuk turunannya,
* mis. siswa.create), pola URL, atau URL yang diawali URL route menu tersebut.
*/
$isActive = function ($menu) {
$url = route($menu['route']);
$current = request()->url();

return request()->routeIs($menu['route'], $menu['route'] . '.*')
|| request()->is($menu['pattern'])
|| $current === $url
|| str_starts_with($current, rtrim($url, '/') . '/');
};

/*
* Struktur menu. Untuk menambah / memindah menu cukup ubah array ini.
* - item biasa : ['label', 'icon', 'route', 'pattern']
* - item grup : ['label', 'icon', 'items' => [ ...item biasa... ]]
*/
$sections = [
'Main' => [
['label' => 'Dashboard', 'icon' => 'fa-house', 'route' => 'admin.dashboard', 'pattern' => 'admin/dashboard*'],
],

'Master Data' => [
[
'label' => 'Data Pengguna',
'icon' => 'fa-users',
'items' => [
['label' => 'Siswa', 'icon' => 'fa-user-graduate', 'route' => 'siswa', 'pattern' => 'admin/siswa*'],
['label' => 'Guru', 'icon' => 'fa-chalkboard-user', 'route' => 'guru', 'pattern' => 'admin/guru*'],
['label' => 'Pengguna Role', 'icon' => 'fa-user-gear', 'route' => 'pengguna', 'pattern' => 'admin/pengguna*'],
['label' => 'Penempatan Siswa', 'icon' => 'fa-people-arrows', 'route' => 'penempatan', 'pattern' => 'admin/penempatan*'],
],
],
[
'label' => 'Data Akademik',
'icon' => 'fa-school',
'items' => [
['label' => 'Tahun Ajaran', 'icon' => 'fa-calendar-days', 'route' => 'tahunajaran', 'pattern' => 'admin/tahunajaran*'],
['label' => 'Jurusan', 'icon' => 'fa-sitemap', 'route' => 'jurusan', 'pattern' => 'admin/jurusan*'],
['label' => 'Kelas', 'icon' => 'fa-door-open', 'route' => 'kelas', 'pattern' => 'admin/kelas*'],
['label' => 'Mata Pelajaran', 'icon' => 'fa-book', 'route' => 'mapel', 'pattern' => 'admin/mapel*'],
['label' => 'Penugasan Guru', 'icon' => 'fa-people-arrows', 'route' => 'penugasanguru', 'pattern' => 'admin/penugasanguru*'],
],
],
[
'label' => 'Data Pembelajaran',
'icon' => 'fa-book-open',
'items' => [
['label' => 'Materi', 'icon' => 'fa-book', 'route' => 'materi', 'pattern' => 'admin/materi*'],
['label' => 'Diskusi Materi', 'icon' => 'fa-book', 'route' => 'diskusimateri', 'pattern' => 'admin/diskusimateri*'],
['label' => 'Ujian', 'icon' => 'fa-file-pen', 'route' => 'ujian', 'pattern' => 'admin/ujian*'],
],
],
[
'label' => 'Data Penilaian',
'icon' => 'fa-clipboard',
'items' => [
['label' => 'Log Absensi', 'icon' => 'fa-check', 'route' => 'absensi', 'pattern' => 'admin/absensi*'],
['label' => 'Rekap Nilai & Rapor', 'icon' => 'fa-box-archive', 'route' => 'akademik', 'pattern' => 'admin/akademik*'],
],
],
[
'label' => 'Data PPDB',
'icon' => 'fa-file-invoice',
'items' => [
['label' => 'Periode PPDB', 'icon' => 'fa-check', 'route' => 'periode', 'pattern' => 'admin/periode*'],
['label' => 'Pendaftar', 'icon' => 'fa-user', 'route' => 'pendaftar', 'pattern' => 'admin/pendaftar*'],
['label' => 'Verifikasi Dokumen', 'icon' => 'fa-file', 'route' => 'dokumen', 'pattern' => 'admin/dokumen*'],
['label' => 'Verifikasi Pembayaran', 'icon' => 'fa-money-bill-wave', 'route' => 'pembayaran', 'pattern' => 'admin/pembayaran*'],
['label' => 'Seleksi Pendaftar', 'icon' => 'fa-user-check', 'route' => 'seleksi', 'pattern' => 'admin/seleksi*'],
['label' => 'Daftar Ulang', 'icon' => 'fa-user-check', 'route' => 'daftar-ulang', 'pattern' => 'admin/daftar-ulang*'],
],
],
],
];
@endphp

<style>
    /* Saat sidebar collapsed (desktop): sembunyikan header grup, tampilkan semua ikon menu */
    @media (min-width: 1024px) {
        html.sb-collapsed .sb-group-btn {
            display: none;
        }

        html.sb-collapsed .sb-sub {
            display: block !important;
            margin-left: 0;
            padding-left: 0;
            border-left: 0;
            margin-top: 0;
        }
    }
</style>

{{-- Sidebar mulai di bawah header (top-16) --}}
<aside id="sidebar"
    class="fixed top-16 left-0 z-40 w-64 h-[calc(100vh-4rem)] flex flex-col bg-white border-r border-brand-100 overflow-hidden
           transform -translate-x-full lg:translate-x-0 transition-[width,transform] duration-300 ease-in-out">

    <nav class="flex-1 overflow-y-auto overflow-x-hidden mt-4 px-3 space-y-4">

        @foreach ($sections as $sectionName => $items)
        <div class="space-y-1">
            <div class="sb-label text-[11px] uppercase tracking-wider text-brand-600/70 px-4 mb-1">{{ $sectionName }}</div>
            <div class="sb-divider h-px bg-brand-100 mx-2 mb-2"></div>

            @foreach ($items as $item)

            {{-- ========== GRUP MENU ========== --}}
            @if (isset($item['items']))
            @php
            $groupActive = collect($item['items'])->contains(fn ($sub) => $isActive($sub));
            @endphp

            <div class="sb-group" x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">
                @unless ($loop->first)
                <div class="sb-divider h-px bg-brand-100 mx-2 my-2"></div>
                @endunless

                <button type="button" @click="open = !open" :aria-expanded="open"
                    class="sb-group-btn w-full flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-all
                                       {{ $groupActive ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-brand-50 hover:text-brand-700' }}">
                    <i class="fa-solid {{ $item['icon'] }} w-5 text-center shrink-0 text-brand-500"></i>
                    <span class="ml-3 flex-1 text-left whitespace-nowrap">{{ $item['label'] }}</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-brand-400 transition-transform duration-200"
                        :class="{ 'rotate-180': open }"></i>
                </button>

                <div x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="sb-sub mt-1 ml-5 pl-3 border-l border-brand-100 space-y-1"
                    @unless ($groupActive) style="display: none;" @endunless>

                    @foreach ($item['items'] as $sub)
                    @php $active = $isActive($sub); @endphp
                    <a href="{{ route($sub['route']) }}" title="{{ $sub['label'] }}" class="{{ $linkClass($active) }}">
                        <i class="fa-solid {{ $sub['icon'] }} {{ $iconClass($active) }}"></i>
                        <span class="sb-label ml-3 whitespace-nowrap">{{ $sub['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- ========== MENU BIASA ========== --}}
            @else
            @php $active = $isActive($item); @endphp
            <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}" class="{{ $linkClass($active) }}">
                <i class="fa-solid {{ $item['icon'] }} {{ $iconClass($active) }}"></i>
                <span class="sb-label ml-3 whitespace-nowrap">{{ $item['label'] }}</span>
            </a>
            @endif

            @endforeach
        </div>
        @endforeach

    </nav>

    {{-- Footer sidebar (disembunyikan saat collapsed) --}}
    <div class="sb-label shrink-0 border-t border-brand-100 p-4 bg-brand-50/70 text-center">
        <p class="text-xs text-slate-500">© {{ date('Y') }} SIMAK</p>
        <p class="text-xs font-semibold text-brand-600 mt-0.5">YAYASAN ISLAMIYAH</p>
    </div>
</aside>

<!-- Overlay untuk mobile -->
<div id="sidebarOverlay" class="fixed inset-x-0 bottom-0 top-16 z-30 hidden lg:hidden bg-slate-900/40"></div>