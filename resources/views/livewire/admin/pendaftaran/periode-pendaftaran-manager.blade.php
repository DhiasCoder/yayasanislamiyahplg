
<div class="space-y-6">
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    Periode Pendaftaran
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola periode penerimaan siswa baru.
                </p>
            </div>

            <button wire:click="tambah"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                + Tambah Periode
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg bg-green-50 p-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl bg-white p-6 shadow-sm">
        <div class="mb-5 flex flex-wrap gap-3">
            <input wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Cari nama periode..."
                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm sm:w-72">

            <select wire:model.live="filterStatus"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="dibuka">Dibuka</option>
                <option value="ditutup">Ditutup</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Nama Periode</th>
                        <th class="px-4 py-3">Tahun Ajaran</th>
                        <th class="px-4 py-3">Tanggal Mulai</th>
                        <th class="px-4 py-3">Tanggal Selesai</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($periodes as $periode)
                        <tr>
                            <td class="px-4 py-3">
                                {{ $periodes->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $periode->nama }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $periode->tahunAjaran->nama
                                    ?? $periode->tahunAjaran->tahun_ajaran
                                    ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                               {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    $warna = match ($periode->status) {
                                        'dibuka' => 'bg-green-100 text-green-700',
                                        'ditutup' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs {{ $warna }}">
                                    {{ ucfirst($periode->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex gap-3">
                                    <button wire:click="edit({{ $periode->id }})"
                                        class="text-indigo-600 hover:text-indigo-800">
                                        Edit
                                    </button>

                                    <button
                                        wire:click="hapus({{ $periode->id }})"
                                        wire:confirm="Yakin ingin menghapus periode ini?"
                                        class="text-red-600 hover:text-red-800">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                class="px-4 py-8 text-center text-gray-500">
                                Belum ada periode pendaftaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $periodes->links() }}
        </div>
    </div>

    {{-- Modal Tambah/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl"
                wire:key="periode-modal">

                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ $periodeId ? 'Edit Periode' : 'Tambah Periode' }}
                    </h2>

                    <button wire:click="$set('showModal', false)"
                        class="text-2xl text-gray-400 hover:text-gray-700">
                        &times;
                    </button>
                </div>

                <form wire:submit="simpan" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            Nama Periode
                        </label>
                        <input wire:model="nama" type="text"
                            placeholder="Contoh: PPDB Gelombang 1"
                            class="w-full rounded-lg border px-3 py-2 text-sm">
                        @error('nama')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            Tahun Ajaran
                        </label>
                        <select wire:model="tahun_ajaran_id"
                            class="w-full rounded-lg border px-3 py-2 text-sm">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach ($tahunAjarans as $tahun)
                                <option value="{{ $tahun->id }}">
                                    {{ $tahun->nama ?? $tahun->tahun_ajaran ?? $tahun->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium">
                                Tanggal Mulai
                            </label>
                            <input wire:model="tanggal_mulai" type="date"
                                class="w-full rounded-lg border px-3 py-2 text-sm">
                            @error('tanggal_mulai')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">
                                Tanggal Selesai
                            </label>
                            <input wire:model="tanggal_selesai" type="date"
                                class="w-full rounded-lg border px-3 py-2 text-sm">
                            @error('tanggal_selesai')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            Status
                        </label>
                        <select wire:model="status"
                            class="w-full rounded-lg border px-3 py-2 text-sm">
                            <option value="draft">Draft</option>
                            <option value="dibuka">Dibuka</option>
                            <option value="ditutup">Ditutup</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-4">
                        <button type="button"
                            wire:click="$set('showModal', false)"
                            class="rounded-lg border px-4 py-2 text-sm text-gray-600">
                            Batal
                        </button>

                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            <span wire:loading.remove wire:target="simpan">
                                Simpan
                            </span>
                            <span wire:loading wire:target="simpan">
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
