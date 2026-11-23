<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
        $q = $this->currentQuestion;
        $index = $this->currentIndex;
        @endphp

        {{-- Header Kuis --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="h-2 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800">{{ $quiz->judul }}</h2>
                            <p class="text-sm text-slate-600 mt-1">Kerjakan dengan teliti dan jujur</p>
                        </div>
                    </div>

                    {{-- Progress Badge --}}
                    <div class="text-right">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="font-semibold text-emerald-700">
                                {{ $index + 1 }} / {{ count($questions) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Soal Dikerjakan</p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-600">Progress Pengerjaan</span>
                        <span class="text-sm font-semibold text-emerald-600">
                            {{ round((($index + 1) / count($questions)) * 100) }}%
                        </span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2.5 rounded-full transition-all duration-300"
                            style="width: {{ (($index + 1) / count($questions)) * 100 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Soal --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">

            {{-- Nomor Soal --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="text-lg font-bold text-blue-600">{{ $index + 1 }}</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Pertanyaan</p>
                    <p class="font-semibold text-slate-800">Soal Nomor {{ $index + 1 }}</p>
                </div>
            </div>

            {{-- Pertanyaan --}}
            <div class="bg-slate-50 rounded-lg p-5 mb-6">
                <p class="text-lg font-medium text-slate-800 leading-relaxed">{{ $q->pertanyaan }}</p>
            </div>

            {{-- Pilihan Jawaban --}}
            <div class="space-y-3">
                @php
                $labels = ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'];
                $colors = [
                'a' => 'border-blue-200 hover:bg-blue-50 hover:border-blue-300',
                'b' => 'border-emerald-200 hover:bg-emerald-50 hover:border-emerald-300',
                'c' => 'border-amber-200 hover:bg-amber-50 hover:border-amber-300',
                'd' => 'border-purple-200 hover:bg-purple-50 hover:border-purple-300'
                ];
                @endphp
                @foreach (['a','b','c','d'] as $opt)
                <label class="flex items-start gap-4 p-4 border-2 {{ $colors[$opt] }} rounded-xl cursor-pointer transition-all group"
                    wire:key="question-{{ $q->id }}-option-{{ $opt }}">
                    <input type="radio"
                        wire:model="answers.{{ $q->id }}"
                        value="{{ $opt }}"
                        class="w-5 h-5 mt-0.5 text-emerald-600 focus:ring-2 focus:ring-emerald-500">
                    <div class="flex-1 flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 bg-slate-100 group-hover:bg-white rounded-lg font-bold text-slate-700 text-sm transition-colors">
                            {{ strtoupper($opt) }}
                        </span>
                        <span class="text-slate-700 font-medium">{{ $q['opsi_'.$opt] }}</span>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="flex items-center justify-between gap-4">
            {{-- Tombol Sebelumnya --}}
            @if ($index > 0)
            <button wire:click="prev"
                class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 hover:border-slate-400 transition-all shadow-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Sebelumnya</span>
            </button>
            @else
            <div></div>
            @endif
            {{-- Tombol Next atau Submit --}}
            @if ($index < count($questions) - 1)
                <button wire:click="next"
                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-sm hover:shadow-md font-medium">
                <span>Selanjutnya</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                </button>
                @else
                <button wire:click="submitQuiz"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-md hover:shadow-lg font-medium">
                    <span>Selesaikan Kuis</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
                @endif
        </div>
    </div>
</div>