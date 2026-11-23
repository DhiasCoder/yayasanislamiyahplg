<?php

namespace App\Livewire\Admin\Laporan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quiz_result;
use App\Models\Quizzes;
use App\Models\QuizResultAnswer;

#[\Livewire\Attributes\Layout('admin.layouts.app')]
class QuizReportManager extends Component
{
    use WithPagination;

    public $search = '';
    public $quizFilter = '';
    public $showDetail = false;

    public $selectedResult;
    public $answers = [];

    public function viewDetail($id)
    {
        $this->selectedResult = Quiz_result::with(['user', 'quiz'])
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
        $quizzes = Quizzes::orderBy('judul')->get();

        $results = Quiz_result::with(['user', 'quiz'])
            ->when($this->quizFilter, function ($q) {
                $q->where('quiz_id', $this->quizFilter);
            })
            ->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.laporan.quiz-report-manager', compact('results', 'quizzes'))
            ->title('Laporan Nilai Siswa');
    }
}
