<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

    {{-- Header --}}
    <div class="h-2 bg-gradient-to-r from-indigo-500 to-indigo-600"></div>

    <div class="p-6 space-y-6">

        {{-- Title Section --}}
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Diskusi Materi</h2>
                <p class="text-sm text-slate-600">Tanya jawab dan diskusi pembelajaran</p>
            </div>
        </div>

        @php
        function avatarColor($name) {
        $colors = [
        'bg-red-500', 'bg-orange-500', 'bg-amber-500', 'bg-yellow-500',
        'bg-lime-500', 'bg-green-500', 'bg-emerald-500', 'bg-teal-500',
        'bg-cyan-500', 'bg-sky-500', 'bg-blue-500', 'bg-indigo-500',
        'bg-violet-500', 'bg-purple-500', 'bg-fuchsia-500', 'bg-pink-500',
        'bg-rose-500'
        ];

        $index = crc32($name) % count($colors);
        return $colors[$index];
        }
        @endphp

        <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">

            @forelse($komentar as $k)

            @php
            // generate inisial user
            $parts = explode(' ', $k->user->name);
            $inisial = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1],0,1) : ''));

            // generate warna avatar unik
            $color = avatarColor($k->user->name);
            @endphp

            {{-- === KOMENTAR SAYA (KANAN) === --}}
            @if($k->user_id == Auth::id())
            <div class="flex justify-end">
                <div class="max-w-[80%]">
                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-2xl rounded-tr-sm p-4 shadow-sm">

                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center text-xs font-bold">
                                {{ $inisial }}
                            </div>
                            <span class="text-sm font-semibold">{{ $k->user->name }}</span>
                        </div>

                        <p class="leading-relaxed">{{ $k->isi }}</p>

                        <div class="flex items-center gap-1 mt-2 text-xs text-indigo-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $k->created_at->format('H:i') }}</span>
                        </div>

                    </div>
                </div>
            </div>

            {{-- === KOMENTAR ORANG LAIN (KIRI) === --}}
            @else
            <div class="flex justify-start">
                <div class="max-w-[80%]">
                    <div class="bg-slate-100 rounded-2xl rounded-tl-sm p-4 shadow-sm border border-slate-200">

                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 {{ $color }} rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ $inisial }}
                            </div>
                            <span class="text-sm font-semibold text-slate-800">{{ $k->user->name }}</span>
                        </div>

                        <p class="text-slate-700 leading-relaxed">{{ $k->isi }}</p>

                        <div class="flex items-center gap-1 mt-2 text-xs text-slate-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $k->created_at->format('H:i') }}</span>
                        </div>

                    </div>
                </div>
            </div>
            @endif

            @empty

            {{-- === EMPTY STATE === --}}
            <div class="flex flex-col items-center justify-center py-12">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-slate-700 font-semibold mb-1">Belum ada diskusi</h3>
                <p class="text-sm text-slate-500">Jadilah yang pertama bertanya atau berdiskusi</p>
            </div>

            @endforelse
        </div>

        {{-- Input Form --}}
        <div class="pt-4 border-t border-slate-200">
            <div class="flex gap-3">
                {{-- Input --}}
                <div class="flex-1 relative" wire:ignore>
                    <button id="emoji-btn"
                        type="button"
                        class="absolute left-3 top-1/2 -translate-y-1/2 
                        text-xl cursor-pointer w-7 h-7 flex items-center justify-center 
                        hover:scale-110 transition">
                        😊
                    </button>
                    <input type="text"
                        wire:model="pesan"
                        id="chat-input"
                        class="w-full pl-10 pr-12 py-3 border border-slate-300 rounded-xl
                        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        placeholder="Tulis pertanyaan atau jawaban..."
                        wire:keydown.enter="kirimKomentar">
                </div>
                {{-- Tombol Kirim --}}
                <button wire:click="kirimKomentar"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-medium rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                    <span>Kirim</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                Tekan Enter untuk mengirim pesan
            </p>
        </div>
    </div>
</div>