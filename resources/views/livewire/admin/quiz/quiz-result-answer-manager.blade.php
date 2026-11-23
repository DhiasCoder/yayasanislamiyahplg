<div>
    <div class="bg-white rounded-lg shadow mb-6">

        {{-- HEADER --}}
        <div class="p-4 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h3 class="font-semibold text-gray-800 text-lg">Hasil Kuis: {{ $quiz->judul }}</h3>
                <p class="text-sm text-gray-500 mt-1">Lihat hasil pengerjaan siswa & detail jawabannya</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <input wire:model.lazy="search" type="text" placeholder="Cari siswa..."
                    class="bg-gray-100 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 w-full sm:w-48">

                @if(count($selectedResults) > 0)
                <button wire:click="confirmBulkDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Hapus Terpilih ({{ count($selectedResults) }})
                </button>
                @endif
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($results as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" wire:model="selectedResults" value="{{ $r->id }}">
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $r->user->name }}</td>
                        <td class="px-6 py-4">{{ $r->skor }}</td>
                        <td class="px-6 py-4">{{ $r->tanggal_selesai }}</td>

                        <td class="px-6 py-4 flex space-x-3">
                            <button wire:click="viewDetail({{ $r->id }})"
                                class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </button>

                            <button wire:click="confirmDelete({{ $r->id }})"
                                class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 px-4">
                {{ $results->links() }}
            </div>
        </div>

        {{-- MODAL DETAIL --}}
        @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-2xl">

                <h3 class="text-lg font-bold mb-2">Detail Hasil Kuis</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Siswa: <span class="font-semibold">{{ $selectedResult->user->name }}</span> <br>
                    Skor: <span class="font-semibold">{{ $selectedResult->skor }}</span>
                </p>

                <div class="max-h-80 overflow-y-auto border rounded p-4">
                    @foreach($answers as $ans)
                    <div class="mb-4">
                        <p class="font-semibold">• {{ $ans->question->pertanyaan }}</p>

                        <div class="mt-1 ml-4">
                            <p class="text-sm">
                                Jawaban Siswa:
                                <span class="font-medium">{{ $ans->jawaban_siswa }}</span>
                            </p>

                            <p class="text-sm">
                                Status:
                                <span class="{{ $ans->is_correct ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ $ans->is_correct ? 'Benar' : 'Salah' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-end mt-4">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-300 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- MODAL DELETE --}}
        @if($confirmingDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-sm">
                <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
                <p class="mb-6 text-gray-600">Yakin ingin menghapus hasil kuis ini?</p>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingDelete', false)" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button wire:click="deleteResultConfirmed" class="px-4 py-2 bg-red-600 text-white rounded">Hapus</button>
                </div>
            </div>
        </div>
        @endif

        {{-- MODAL BULK DELETE --}}
        @if($confirmingBulkDelete)
        <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-sm">
                <h3 class="text-lg font-bold mb-3">Hapus Banyak Data?</h3>
                <p class="text-sm text-gray-600 mb-5">Semua hasil kuis yang dipilih akan dihapus permanen.</p>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingBulkDelete', false)" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                    <button wire:click="deleteSelectedConfirmed" class="bg-red-600 text-white px-4 py-2 rounded">Hapus</button>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>