<?php

namespace App\Livewire\Guru\Laporan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quiz_result;
use App\Models\Quizzes;
use App\Models\QuizResultAnswer;

#[\Livewire\Attributes\Layout('guru.layouts.app')]
class QuizGuruReportManager extends Component
{
    use WithPagination;

    public $search = '';
    public $quizFilter = '';
    public $showDetail = false;

    public $selectedResult;
    public $answers = [];

    public function viewDetail($id)
    {
        $guruId = auth()->id();

        $this->selectedResult = Quiz_result::with(['user', 'quiz'])
            ->whereHas('quiz', fn($q) => $q->where('guru_id', $guruId))
            ->findOrFail($id);

        $this->answers = QuizResultAnswer::with('question')
            ->where('quiz_result_id', $id)
            ->get();

        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->reset(['selectedResult', 'answers']);
        $this->showDetail = false;
    }

    public function render()
    {
        $guruId = auth()->id();

        $quizzes = Quizzes::where('guru_id', $guruId)
            ->orderBy('judul')
            ->get();

        $results = Quiz_result::with(['user', 'quiz'])
            ->whereHas('quiz', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->when($this->quizFilter, function ($q) {
                $q->where('quiz_id', $this->quizFilter);
            })
            ->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.guru.laporan.quiz-guru-report-manager', compact('results', 'quizzes'))
            ->title('Laporan Nilai Siswa');
    }
}
