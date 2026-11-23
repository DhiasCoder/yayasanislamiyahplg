<div>
    <div class="bg-white shadow rounded-lg p-5">
        <h2 class="text-xl font-bold mb-4 text-gray-700">Laporan Nilai Siswa</h2>

        {{-- FILTER --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
            <div>
                <input wire:model="search" type="text" placeholder="Cari nama siswa..."
                    class="w-full px-3 py-2 bg-gray-100 rounded-lg focus:ring focus:ring-blue-400">
            </div>

            <div>
                <select wire:model="quizFilter" class="w-full px-3 py-2 bg-gray-100 rounded-lg focus:ring">
                    <option value="">Semua Kuis</option>
                    @foreach($quizzes as $qz)
                    <option value="{{ $qz->id }}">{{ $qz->judul }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Siswa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Kuis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Skor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Persentase</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($results as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $r->user->name }}</td>
                        <td class="px-4 py-3">{{ $r->quiz->judul }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $r->skor }}</td>
                        <td class="px-4 py-3">{{ $r->persentase() }}%</td>
                        <td class="px-4 py-3">{{ $r->tanggal_format }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="viewDetail({{ $r->id }})"
                                class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $results->links() }}</div>
        </div>
    </div>

    {{-- DETAIL MODAL --}}
    @if($showDetail)
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-2xl">

            <h3 class="text-lg font-bold mb-2">Detail Hasil Kuis</h3>

            <p class="text-sm text-gray-600 mb-4">
                Siswa: <span class="font-semibold">{{ $selectedResult->user->name }}</span><br>
                Kuis: <span class="font-semibold">{{ $selectedResult->quiz->judul }}</span><br>
                Skor: <span class="font-semibold">{{ $selectedResult->skor }}</span>
            </p>

            <div class="max-h-80 overflow-y-auto border p-4 rounded">
                @foreach($answers as $ans)
                <div class="mb-4">
                    <p class="font-semibold">• {{ $ans->question->pertanyaan }}</p>

                    <div class="ml-3 mt-1">
                        <p class="text-sm">Jawaban: <span class="font-medium">{{ $ans->jawaban_siswa }}</span></p>

                        <p class="text-sm">
                            Status:
                            <span class="{{ $ans->benar ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $ans->benar ? 'Benar' : 'Salah' }}
                            </span>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-end mt-4">
                <button wire:click="closeDetail"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Tutup</button>
            </div>

        </div>
    </div>
    @endif
</div>