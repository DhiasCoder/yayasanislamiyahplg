<?php

namespace App\Livewire\Admin\Quiz;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quiz_result;
use App\Models\QuizResultAnswer;
use App\Models\Quizzes;

#[\Livewire\Attributes\Layout('admin.layouts.app')]
class QuizResultAnswerManager extends Component
{
    use WithPagination;

    public $quiz_id;

    public $result_id;
    public $selectedResult;
    public $answers = [];

    public $search = '';
    public $showModal = false;

    public $selectedResults = [];
    public $selectAll = false;
    public $confirmingDelete = false;
    public $confirmingBulkDelete = false;
    public $resultIdToDelete;

    public function mount($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function viewDetail($id)
    {
        $this->result_id = $id;

        $this->selectedResult = Quiz_result::with('user')
            ->where('id', $id)
            ->first();

        $this->answers = QuizResultAnswer::with('question')
            ->where('quiz_result_id', $id)
            ->get();

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->reset(['result_id', 'selectedResult', 'answers']);
        $this->showModal = false;
    }


    /** Konfirmasi hapus satu data hasil kuis (opsional) */
    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->resultIdToDelete = $id;
    }

    /** Hapus satu hasil kuis */
    public function deleteResultConfirmed()
    {
        Quiz_result::findOrFail($this->resultIdToDelete)->delete();
        QuizResultAnswer::where('quiz_result_id', $this->resultIdToDelete)->delete();

        $this->confirmingDelete = false;
    }

    /** Konfirmasi bulk delete */
    public function confirmBulkDelete()
    {
        $this->confirmingBulkDelete = true;
    }

    /** Bulk delete hasil kuis */
    public function deleteSelectedConfirmed()
    {
        Quiz_result::whereIn('id', $this->selectedResults)->delete();
        QuizResultAnswer::whereIn('quiz_result_id', $this->selectedResults)->delete();

        $this->selectedResults = [];
        $this->confirmingBulkDelete = false;
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedResults = Quiz_result::where('quiz_id', $this->quiz_id)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedResults = [];
        }
    }

    public function render()
    {
        $quiz = Quizzes::findOrFail($this->quiz_id);

        $results = Quiz_result::with('user')
            ->where('quiz_id', $this->quiz_id)
            ->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.quiz.quiz-result-answer-manager', compact('quiz', 'results'))
            ->title('Hasil Kuis Siswa');
    }
}
