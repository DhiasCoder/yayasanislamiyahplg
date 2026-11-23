<div>
    <div class="bg-white rounded-lg shadow mb-6">

        {{-- HEADER --}}
        <div class="p-4 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h3 class="font-semibold text-gray-800 text-lg">Manajemen Pertanyaan — {{ $quiz->judul }}</h3>
                <p class="text-sm text-gray-500 mt-1">Kelola soal dan opsi pilihan ganda untuk kuis ini</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <input wire:model.lazy="search" type="text" placeholder="Cari Pertanyaan..."
                    class="bg-gray-100 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 w-full sm:w-48">

                <button wire:click="resetForm"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah Pertanyaan
                </button>

                <a href="{{ route('quiz') }}"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
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

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pertanyaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jawaban Benar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($questions as $q)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" wire:model="selectedQuestions" value="{{ $q->id }}">
                        </td>

                        <td class="px-6 py-4 font-medium">
                            {{ Str::limit($q->pertanyaan, 80) }}
                            <div class="text-xs text-gray-600 mt-1">
                                A: {{ $q->opsi_a }} <br>
                                B: {{ $q->opsi_b }} <br>
                                C: {{ $q->opsi_c }} <br>
                                D: {{ $q->opsi_d }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">
                                {{ strtoupper($q->jawaban_benar) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 flex space-x-2">
                            <button wire:click="edit({{ $q->id }})"
                                class="text-green-600 hover:text-green-900">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button wire:click="confirmDelete({{ $q->id }})"
                                class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 px-4">
                {{ $questions->links() }}
            </div>
        </div>


        {{-- MODAL TAMBAH / EDIT --}}
        @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-lg">

                <h3 class="text-lg font-bold mb-4">
                    {{ $editMode ? 'Edit Pertanyaan' : 'Tambah Pertanyaan' }}
                </h3>

                <form wire:submit.prevent="save">

                    <textarea wire:model="pertanyaan" placeholder="Tulis Pertanyaan..."
                        class="w-full mb-3 border px-3 py-2 rounded"></textarea>

                    {{-- OPSI --}}
                    <input wire:model="opsi_a" type="text" placeholder="Opsi A"
                        class="w-full mb-3 border px-3 py-2 rounded">

                    <input wire:model="opsi_b" type="text" placeholder="Opsi B"
                        class="w-full mb-3 border px-3 py-2 rounded">

                    <input wire:model="opsi_c" type="text" placeholder="Opsi C"
                        class="w-full mb-3 border px-3 py-2 rounded">

                    <input wire:model="opsi_d" type="text" placeholder="Opsi D"
                        class="w-full mb-3 border px-3 py-2 rounded">

                    {{-- JAWABAN BENAR --}}
                    <select wire:model="jawaban_benar"
                        class="w-full mb-4 border px-3 py-2 rounded">
                        <option value="">Pilih Jawaban Benar</option>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="d">D</option>
                    </select>

                    {{-- BUTTON --}}
                    <div class="flex justify-end space-x-2">
                        <button wire:click="$set('showModal', false)" type="button"
                            class="bg-gray-300 px-4 py-2 rounded">
                            Batal
                        </button>

                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
        @endif


        {{-- MODAL DELETE --}}
        @if($confirmingDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-sm">

                <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
                <p class="mb-6 text-gray-600">Yakin ingin menghapus pertanyaan ini?</p>

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('confirmingDelete', false)"
                        class="px-4 py-2 bg-gray-300 rounded">
                        Batal
                    </button>

                    <button wire:click="deleteQuestionConfirmed"
                        class="px-4 py-2 bg-red-600 text-white rounded">
                        Hapus
                    </button>
                </div>

            </div>
        </div>
        @endif

    </div>
</div>