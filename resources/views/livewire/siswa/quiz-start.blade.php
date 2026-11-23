<div class="max-w-3xl mx-auto p-6">
    <div class="bg-white p-5 rounded-xl shadow-lg">

        <h2 class="text-2xl font-bold">{{ $quiz->judul }}</h2>
        <p class="text-gray-600 mt-2">{{ $quiz->deskripsi }}</p>

        <div class="mt-6">
            <p class="text-gray-700">
                Jumlah Soal: <b>{{ $quiz->questions->count() }}</b>
            </p>
        </div>

        <button wire:click="startQuiz"
            class="mt-6 bg-green-600 text-white px-4 py-2 rounded-lg">
            Mulai Kuis
        </button>
    </div>
</div>