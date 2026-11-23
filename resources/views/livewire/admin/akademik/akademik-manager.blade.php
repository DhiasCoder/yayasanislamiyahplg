
<div class="space-y-6">
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold text-gray-800">Manajemen Akademik</h1>
        <p class="mt-1 text-sm text-gray-500">
            Kelola nilai, rapor, dan rekap akademik siswa.
        </p>

        <div class="mt-5 flex flex-wrap gap-2 border-b">
            <button wire:click="pilihTab('nilai')"
                class="border-b-2 px-4 py-3 text-sm font-medium
                {{ $tab === 'nilai' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500' }}">
                Nilai
            </button>

            <button wire:click="pilihTab('rapor')"
                class="border-b-2 px-4 py-3 text-sm font-medium
                {{ $tab === 'rapor' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500' }}">
                Rapor
            </button>

            <button wire:click="pilihTab('rekap')"
                class="border-b-2 px-4 py-3 text-sm font-medium
                {{ $tab === 'rekap' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500' }}">
                Rekap Nilai & Kehadiran
            </button>
        </div>
    </div>

    @if ($tab === 'nilai')
        <livewire:admin.akademik.nilai-manager :key="'akademik-nilai'" />
    @elseif ($tab === 'rapor')
        <livewire:admin.akademik.rapor-manager :key="'akademik-rapor'" />
    @elseif ($tab === 'rekap')
        <livewire:admin.akademik.rekap-akademik-manager :key="'akademik-rekap'" />
    @endif
</div>
