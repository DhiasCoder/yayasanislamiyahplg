
<div class="rounded-xl bg-white p-6 shadow-sm">
    <div class="mb-5">
        <h2 class="text-lg font-semibold text-gray-800">Rekap Nilai & Kehadiran</h2>
        <p class="text-sm text-gray-500">Ringkasan akademik siswa di sekolah Anda.</p>
    </div>

    <input wire:model.live.debounce.300ms="search"
        placeholder="Cari nama siswa..."
        class="mb-4 w-full rounded-lg border px-4 py-2 text-sm sm:w-72">

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">No.</th>
                    <th class="px-4 py-3">Nama Siswa</th>
                    <th class="px-4 py-3">Rata-rata Nilai</th>
                    <th class="px-4 py-3">Hadir</th>
                    <th class="px-4 py-3">Izin</th>
                    <th class="px-4 py-3">Sakit</th>
                    <th class="px-4 py-3">Alpa</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($siswas as $siswa)
                    <tr>
                        <td class="px-4 py-3">{{ $siswas->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">{{ $siswa->nama_lengkap }}</td>
                        <td class="px-4 py-3 font-semibold">
                            {{ $siswa->nilais_avg_nilai !== null ? number_format($siswa->nilais_avg_nilai, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-green-700">{{ $siswa->jumlah_hadir }}</td>
                        <td class="px-4 py-3">{{ $siswa->jumlah_izin }}</td>
                        <td class="px-4 py-3">{{ $siswa->jumlah_sakit }}</td>
                        <td class="px-4 py-3 text-red-600">{{ $siswa->jumlah_alpa }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Data siswa tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $siswas->links() }}</div>
</div>
