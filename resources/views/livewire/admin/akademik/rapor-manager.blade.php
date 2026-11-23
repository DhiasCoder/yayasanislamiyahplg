
<div class="rounded-xl bg-white p-6 shadow-sm">
    <div class="mb-5">
        <h2 class="text-lg font-semibold text-gray-800">Manajemen Rapor</h2>
        <p class="text-sm text-gray-500">Pantau rapor dan status publikasi.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search"
            placeholder="Cari nama siswa..."
            class="rounded-lg border px-4 py-2 text-sm">

        <select wire:model.live="filterStatus" class="rounded-lg border px-4 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="published">Dipublikasikan</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3">No.</th>
                    <th class="px-4 py-3">Nama Siswa</th>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Tahun Ajaran</th>
                    <th class="px-4 py-3">Semester</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($rapors as $rapor)
                    <tr>
                        <td class="px-4 py-3">{{ $rapors->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">{{ $rapor->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $rapor->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $rapor->tahunAjaran->nama ?? $rapor->tahunAjaran->tahun_ajaran ?? '-' }}
                        </td>
                        <td class="px-4 py-3 capitalize">{{ $rapor->semester }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-3 py-1 text-xs
                                {{ $rapor->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $rapor->status === 'published' ? 'Dipublikasikan' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="ubahStatus({{ $rapor->id }})"
                                wire:confirm="Yakin ingin mengubah status rapor?"
                                class="text-indigo-600 hover:text-indigo-800">
                                {{ $rapor->status === 'draft' ? 'Publikasikan' : 'Jadikan Draft' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Belum ada data rapor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rapors->links() }}</div>
</div>
