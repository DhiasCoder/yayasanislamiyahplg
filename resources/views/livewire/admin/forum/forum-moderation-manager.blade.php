<div>
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h3 class="font-semibold text-gray-800 text-lg">Moderasi Forum Diskusi</h3>
                <p class="text-sm text-gray-500 mt-1">Pantau komentar forum dan lakukan tindakan moderasi</p>
            </div>
            <input wire:model.lazy="search" type="text" placeholder="Cari komentar..."
                class="bg-gray-100 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 w-full sm:w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3"><input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Forum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komentar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($komentars as $k)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><input type="checkbox" wire:model="selectedKomentar" value="{{ $k->id }}"></td>
                        <td class="px-6 py-4 font-medium">{{ $k->forum->judul ?? '-' }}</td>
                        <td class="px-6 py-4 truncate max-w-sm">{{ $k->isi_komentar }}</td>
                        <td class="px-6 py-4">{{ $k->user->name ?? '-' }} ({{ $k->user->role }})</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold 
                                {{ $k->status === 'dihapus' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($k->status ?? 'normal') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex space-x-2">
                            <button wire:click="showKomentar({{ $k->id }})" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></button>
                            <button wire:click="confirmDelete({{ $k->id }})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                            <button wire:click="blockUser({{ $k->user->id }})" class="text-gray-700 hover:text-black"><i class="fas fa-user-slash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 px-4">
                {{ $komentars->links() }}
            </div>
        </div>

        @if($showModalKomentar)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-lg">
                <h3 class="text-lg font-bold mb-4">Detail Komentar</h3>
                <p class="text-sm text-gray-700 mb-2"><strong>Forum:</strong> {{ $currentKomentar->forum->judul }}</p>
                <p class="text-sm text-gray-700 mb-2"><strong>Oleh:</strong> {{ $currentKomentar->user->name }} ({{ $currentKomentar->user->role }})</p>
                <div class="p-3 bg-gray-50 rounded border mb-4">{{ $currentKomentar->isi_komentar }}</div>
                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('showModalKomentar', false)" class="bg-gray-300 px-4 py-2 rounded">Tutup</button>
                </div>
            </div>
        </div>
        @endif

        @if($confirmingDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-sm">
                <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
                <p class="mb-6 text-gray-600">Yakin ingin menghapus komentar ini?</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingDelete', false)" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button wire:click="deleteKomentarConfirmed" class="px-4 py-2 bg-red-600 text-white rounded">Hapus</button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>