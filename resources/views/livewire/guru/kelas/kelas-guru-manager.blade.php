<div>
    <div class="bg-white rounded-lg shadow mb-6">
        <!-- Header -->
        <div class="p-4 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h3 class="font-semibold text-gray-800 text-lg">List Kelas Saya</h3>
                <p class="text-sm text-gray-500 mt-1">Kelas Guru</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <input wire:model.lazy="search" type="text" placeholder="Cari Kelas..."
                    class="bg-gray-100 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-48">
            </div>
        </div>

        <!-- Tabel Desktop -->
        <div class="hidden lg:block overflow-x-auto">
            @if(count($selectedKelas) > 0)
            <button wire:click="confirmBulkDelete"
                class="group relative inline-flex items-center gap-2 px-4 py-2 mb-4 ml-6
                bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 
                text-white font-medium text-sm rounded-lg shadow-md hover:shadow-lg 
                transform hover:scale-[1.02] active:scale-95 transition-all duration-150 ease-in-out">
                <i class="fas fa-trash"></i>
                <span>Hapus Terpilih ({{ count($selectedKelas) }})</span>
            </button>
            @endif

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wali Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Dibuat</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($kelasList as $kelas)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">{{ $kelas->nama_kelas }}</td>
                        <td class="px-6 py-4">
                            {{ $kelas->guru?->name ?? 'Belum Diatur' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($kelas->created_at)->format('d M Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 px-4">
                {{ $kelasList->links() }}
            </div>
        </div>

        <!-- Kartu Mobile -->
        <div class="lg:hidden p-4 space-y-4">
            @foreach ($kelasList as $kelas)
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-semibold text-blue-700">{{ $kelas->nama_kelas }}</span>
                </div>
                <div class="text-xs text-gray-500 mb-3">
                    Dibuat: {{ \Carbon\Carbon::parse($kelas->created_at)->format('d M Y') }}
                </div>
            </div>
        </div>
        @endforeach
        <div class="mt-4">
            {{ $kelasList->links() }}
        </div>
    </div>
</div>
</div>