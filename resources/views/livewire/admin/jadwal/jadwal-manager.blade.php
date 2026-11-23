
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Jadwal Pelajaran</h1>
            <p class="mt-1 text-sm text-gray-500">
                Atur waktu belajar berdasarkan penugasan guru.
            </p>
        </div>

        <button wire:click="create"
            class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
            + Tambah Jadwal
        </button>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Total Jadwal</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">{{ $totalJadwal }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
        <div class="border-b border-gray-100 p-4">
            <input type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari hari, guru, mata pelajaran, atau kelas..."
                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 sm:max-w-md">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">No.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Hari / Waktu</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Mata Pelajaran</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Guru</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kelas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tahun Ajaran</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Ruang</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($jadwals as $jadwal)
                        @php
                            $pengajaran = $jadwal->pengajaran;
                        @endphp

                        <tr wire:key="jadwal-{{ $jadwal->id }}" class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ $jadwals->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="text-sm font-medium text-gray-800">{{ $jadwal->hari }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ substr((string) $jadwal->jam_mulai, 0, 5) }}
                                    – {{ substr((string) $jadwal->jam_selesai, 0, 5) }}
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                {{ $pengajaran?->mataPelajaran?->nama ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ $pengajaran?->guru?->nama_lengkap ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ $pengajaran?->kelas?->nama_kelas ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">
                                {{ $pengajaran?->tahunAjaran?->nama ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if ($jadwal->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-3">
                                    <button wire:click="edit({{ $jadwal->id }})"
                                        class="font-medium text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </button>

                                    <button wire:click="delete({{ $jadwal->id }})"
                                        wire:confirm="Yakin ingin menghapus jadwal ini?"
                                        class="font-medium text-red-600 hover:text-red-800">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500">
                                Belum ada jadwal pelajaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-5 py-4">
            {{ $jadwals->links() }}
        </div>
    </div>

    {{-- Modal Tambah/Edit --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900/50 p-4">
            <div class="my-auto w-full max-w-xl rounded-2xl bg-white shadow-xl" wire:click.stop>
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            {{ $jadwalId ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran' }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">Tentukan penugasan dan waktu pelajaran.</p>
                    </div>

                    <button type="button" wire:click="resetForm"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                        ✕
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="space-y-4 px-6 py-5">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                Penugasan Guru *
                            </label>
                            <select wire:model="pengajaran_id"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih penugasan guru</option>
                                @foreach ($pengajarans as $pengajaran)
                                    <option value="{{ $pengajaran->id }}">
                                        {{ $pengajaran->kelas?->nama_kelas ?? '-' }}
                                        — {{ $pengajaran->mataPelajaran?->nama ?? '-' }}
                                        — {{ $pengajaran->guru?->nama_lengkap ?? '-' }}
                                        ({{ $pengajaran->tahunAjaran?->nama ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pengajaran_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Hari *</label>
                            <select wire:model="hari"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih hari</option>
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hariPilihan)
                                    <option value="{{ $hariPilihan }}">{{ $hariPilihan }}</option>
                                @endforeach
                            </select>
                            @error('hari')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">Jam Mulai *</label>
                                <input type="time" wire:model="jam_mulai"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('jam_mulai')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700">Jam Selesai *</label>
                                <input type="time" wire:model="jam_selesai"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('jam_selesai')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3">
                            <input type="checkbox" wire:model="is_active"
                                class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span>
                                <span class="block text-sm font-medium text-gray-700">Jadwal aktif</span>
                                <span class="mt-1 block text-xs text-gray-500">
                                    Jadwal aktif akan ditampilkan sebagai jadwal pelajaran yang berlaku.
                                </span>
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4">
                        <button type="button" wire:click="resetForm"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>

                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
