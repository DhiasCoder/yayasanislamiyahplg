<div>
    <div class="bg-white rounded-lg shadow mb-6">
        <!-- Header -->
        <div class="p-4 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h3 class="font-semibold text-gray-800 text-lg">Manajemen Materi</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola semua materi pembelajaran guru dan akses kelas</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <input wire:model.lazy="search" type="text" placeholder="Cari Materi..."
                    class="bg-gray-100 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-48">
                <button wire:click="resetForm"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Materi
                </button>
            </div>
        </div>

        <!-- Bulk Delete -->
        @if(count($selectedMateri) > 0)
        <button wire:click="confirmBulkDelete"
            class="group relative inline-flex items-center gap-2 px-4 py-2 mb-4 ml-6
            bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 
            text-white font-medium text-sm rounded-lg shadow-md hover:shadow-lg transition-all">
            <i class="fas fa-trash"></i> Hapus Terpilih ({{ count($selectedMateri) }})
        </button>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll" class="rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Video</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($materiList as $m)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <input type="checkbox" wire:model="selectedMateri" value="{{ $m->id }}">
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $m->judul }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $m->guru->name ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $m->kelas->nama_kelas ?? '-' }}
                        </td>

                        {{-- File Download --}}
                        <td class="px-6 py-4">
                            @if($m->file_path)
                            <a href="{{ Storage::url($m->file_path) }}" target="_blank"
                                class="text-blue-600 hover:text-blue-800 underline text-sm">
                                Download
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>

                        {{-- Video URL --}}
                        <td class="px-6 py-4">
                            @if($m->video_url)
                            <a href="{{ $m->video_url }}" target="_blank"
                                class="text-purple-600 hover:text-purple-800 underline text-sm">
                                Tonton
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $m->created_at->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">
                            <button wire:click="toggleStatus({{ $m->id }})"
                                class="px-2 py-1 rounded text-xs font-semibold 
                                {{ $m->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $m->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>

                        <td class="px-6 py-4 flex space-x-2">
                            <button wire:click="edit({{ $m->id }})" class="text-green-600 hover:text-green-900">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="{{ route('guru.materi.detail', $m->id) }}"
                                class="text-blue-600 hover:text-blue-900 mr-2">
                                <i class="fas fa-comments"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $m->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 px-4">
                {{ $materiList->links() }}
            </div>
        </div>

        <!-- Modal Form -->
        @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">{{ $editMode ? 'Edit Materi' : 'Tambah Materi' }}</h3>
                <form wire:submit.prevent="save" enctype="multipart/form-data">
                    <input wire:model="judul" type="text" placeholder="Judul Materi" class="w-full mb-3 border px-3 py-2 rounded" />
                    <textarea wire:model="deskripsi" placeholder="Deskripsi Materi" class="w-full mb-3 border px-3 py-2 rounded"></textarea>

                    <select wire:model="guru_id" class="w-full mb-3 border px-3 py-2 rounded">
                        <option value="">Pilih Guru</option>
                        @foreach ($guruList as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model="kelas_id" class="w-full mb-3 border px-3 py-2 rounded">
                        <option value="">Pilih Kelas</option>
                        @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <input wire:model="file_path" type="file" class="w-full mb-3 border px-3 py-2 rounded" />
                    <input wire:model="video_url" type="text" placeholder="URL Video (opsional)" class="w-full mb-3 border px-3 py-2 rounded" />

                    <label class="inline-flex items-center space-x-2 mb-4">
                        <input type="checkbox" wire:model="is_active" class="rounded text-blue-600">
                        <span class="text-sm text-gray-700">Aktifkan Materi</span>
                    </label>

                    <div class="flex justify-end space-x-2">
                        <button wire:click="$set('showModal', false)" type="button" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Modal Konfirmasi Hapus -->
        @if($confirmingDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-sm">
                <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
                <p class="mb-6 text-gray-600">Yakin ingin menghapus materi ini?</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingDelete', false)" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button wire:click="deleteMateriConfirmed" class="px-4 py-2 bg-red-600 text-white rounded">Hapus</button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>