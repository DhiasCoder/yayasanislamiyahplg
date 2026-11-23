<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
        <p class="mt-1 text-sm text-gray-500">
            Ringkasan aktivitas akademik sekolah Anda.
        </p>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @php
        $cards = [
        [
        'label' => 'Total Guru',
        'value' => $stats['guru'] ?? 0,
        'icon' => 'users',
        'color' => 'bg-blue-100 text-blue-600',
        ],
        [
        'label' => 'Total Siswa',
        'value' => $stats['siswa'] ?? 0,
        'icon' => 'graduation-cap',
        'color' => 'bg-green-100 text-green-600',
        ],
        [
        'label' => 'Total Kelas',
        'value' => $stats['kelas'] ?? 0,
        'icon' => 'school',
        'color' => 'bg-purple-100 text-purple-600',
        ],
        [
        'label' => 'Total Materi',
        'value' => $stats['materi'] ?? 0,
        'icon' => 'book-open',
        'color' => 'bg-orange-100 text-orange-600',
        ],
        [
        'label' => 'Total Ujian',
        'value' => $stats['ujian'] ?? 0,
        'icon' => 'clipboard-check',
        'color' => 'bg-pink-100 text-pink-600',
        ],
        ];
        @endphp

        @foreach ($cards as $card)
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">
                        {{ $card['label'] }}
                    </p>

                    <p class="mt-3 text-3xl font-bold text-gray-800">
                        {{ number_format($card['value']) }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $card['color'] }}">
                    @if ($card['icon'] === 'users')
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H2v-2a4 4 0 017.75-1.3M15 7a4 4 0 11-8 0 4 4 0 018 0zm5 4a3 3 0 10-6 0 3 3 0 006 0z" />
                    </svg>
                    @elseif ($card['icon'] === 'graduation-cap')
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 10L12 5 2 10l10 5 10-5zM6 12v5c3 2 9 2 12 0v-5M22 10v6" />
                    </svg>
                    @elseif ($card['icon'] === 'school')
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 9h.01M15 9h.01" />
                    </svg>
                    @elseif ($card['icon'] === 'book-open')
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v14m0-14C9 4 5 4 2 6v13c3-2 7-2 10 1m0-13c3-3 7-3 10-1v13c-3-2-7-2-10 1" />
                    </svg>
                    @else
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 12h6m-6 4h6" />
                    </svg>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Diskusi terbaru --}}
    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Diskusi Materi Terbaru
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Diskusi dari materi di sekolah Anda.
                </p>
            </div>

            <span class="w-fit rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                5 diskusi terbaru
            </span>
        </div>

        @if ($latestDiskusi->isNotEmpty())
        <div class="divide-y divide-gray-100">
            @foreach ($latestDiskusi as $diskusi)
            <div wire:key="diskusi-{{ $diskusi->id }}"
                class="flex gap-4 px-5 py-4 transition hover:bg-gray-50">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                    {{ strtoupper(substr($diskusi->user->name ?? 'U', 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <p class="font-semibold text-gray-800">
                            {{ $diskusi->user->name ?? 'Pengguna' }}
                        </p>

                        <span class="text-xs text-gray-400">
                            {{ $diskusi->created_at?->format('d M Y, H:i') }}
                        </span>
                    </div>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ $diskusi->isi }}
                    </p>

                    <p class="mt-2 text-xs text-blue-600">
                        Materi:
                        {{ $diskusi->materi->judul ?? 'Materi tidak tersedia' }}
                    </p>

                    @if ($diskusi->parent_id)
                    <span class="mt-2 inline-block rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-500">
                        Balasan diskusi
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-5 py-12 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
                <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m-8 6l-2 1V5a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2H5z" />
                </svg>
            </div>

            <h3 class="mt-4 text-sm font-semibold text-gray-800">
                Belum ada diskusi
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Diskusi materi akan tampil di sini ketika tersedia.
            </p>
        </div>
        @endif
    </div>
</div>