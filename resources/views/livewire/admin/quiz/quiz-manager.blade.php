<div>
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h3 class="font-semibold text-gray-800 text-lg">Manajemen Kuis</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola kuis global atau verifikasi kuis guru</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <input wire:model.lazy="search" type="text" placeholder="Cari Kuis..."
                    class="bg-gray-100 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 w-full sm:w-48">
                <button wire:click="resetForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah Kuis
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"><input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Global</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($quizzes as $q)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><input type="checkbox" wire:model="selectedQuiz" value="{{ $q->id }}"></td>
                        <td class="px-6 py-4 font-medium">{{ $q->judul }}</td>
                        <td class="px-6 py-4">{{ $q->guru->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $q->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="{{ $q->is_global ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                                {{ $q->is_global ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleVerification({{ $q->id }})"
                                class="px-2 py-1 rounded text-xs font-semibold {{ $q->is_verified ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $q->is_verified ? 'Terverifikasi' : 'Menunggu' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 flex space-x-2">
                            <button wire:click="edit({{ $q->id }})" class="text-green-600 hover:text-green-900"><i class="fas fa-edit"></i></button>
                            <button wire:click="confirmDelete({{ $q->id }})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                            <a href="{{ route('quizquestion', ['quiz_id' => $q->id]) }}"
                                class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-question-circle"></i>
                            </a>
                            <a href="{{ route('quizresults', $q->id) }}"
                                class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-chart-pie"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 px-4">
                {{ $quizzes->links() }}
            </div>
        </div>

        @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">{{ $editMode ? 'Edit Kuis' : 'Tambah Kuis' }}</h3>
                <form wire:submit.prevent="save">
                    <input wire:model="judul" type="text" placeholder="Judul Kuis" class="w-full mb-3 border px-3 py-2 rounded">
                    <textarea wire:model="deskripsi" placeholder="Deskripsi Kuis" class="w-full mb-3 border px-3 py-2 rounded"></textarea>

                    <select wire:model="guru_id" class="w-full mb-3 border px-3 py-2 rounded">
                        <option value="">Pilih Guru</option>
                        @foreach($guruList as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model="kelas_id" class="w-full mb-3 border px-3 py-2 rounded">
                        <option value="">Pilih Kelas</option>
                        @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <label class="inline-flex items-center space-x-2 mb-3">
                        <input type="checkbox" wire:model="is_global" class="rounded text-blue-600">
                        <span class="text-sm text-gray-700">Kuis Global</span>
                    </label>

                    <label class="inline-flex items-center space-x-2 mb-4">
                        <input type="checkbox" wire:model="is_verified" class="rounded text-green-600">
                        <span class="text-sm text-gray-700">Tandai Terverifikasi</span>
                    </label>

                    <div class="flex justify-end space-x-2">
                        <button wire:click="$set('showModal', false)" type="button" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($confirmingDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-sm">
                <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
                <p class="mb-6 text-gray-600">Yakin ingin menghapus kuis ini?</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingDelete', false)" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button wire:click="deleteQuizConfirmed" class="px-4 py-2 bg-red-600 text-white rounded">Hapus</button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>