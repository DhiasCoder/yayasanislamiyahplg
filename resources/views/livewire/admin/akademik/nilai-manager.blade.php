
<div class="rounded-xl bg-white p-6 shadow-sm">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Data Nilai Siswa</h2>
            <p class="text-sm text-gray-500">Monitoring nilai akademik siswa.</p>
        </div>

        <input type="text" wire:model.live.debounce.300ms="search"
            placeholder="Cari nama atau mata pelajaran..."
            class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm sm:w-72">
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">No.</th>
                    <th class="px-4 py-3">Nama Siswa</th>
                    <th class="px-4 py-3">Mata Pelajaran</th>
                    <th class="px-4 py-3">Jenis Nilai</th>
                    <th class="px-4 py-3">Nilai</th>
                    <th class="px-4 py-3">Tahun Ajaran</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($nilais as $nilai)
                    <tr>
                        <td class="px-4 py-3">{{ $nilais->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">{{ $nilai->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $nilai->pengajaran->mataPelajaran->nama_mata_pelajaran ?? '-' }}
                        </td>
                        <td class="px-4 py-3">{{ $nilai->jenis_nilai }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $nilai->nilai }}</td>
                        <td class="px-4 py-3">
                            {{ $nilai->tahunAjaran->nama ?? $nilai->tahunAjaran->tahun_ajaran ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <button
                                wire:click="hapus({{ $nilai->id }})"
                                wire:confirm="Yakin ingin menghapus nilai ini?"
                                class="text-red-600 hover:text-red-800">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Belum ada data nilai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $nilais->links() }}</div>
</div>
