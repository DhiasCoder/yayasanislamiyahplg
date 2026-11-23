<?php

namespace App\Livewire\Admin\Quiz;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quizzes;
use App\Models\Quiz_question;

#[\Livewire\Attributes\Layout('admin.layouts.app')]
class QuestionManager extends Component
{
    use WithPagination;

    public $quiz_id;
    public $question_id;

    public $pertanyaan;
    public $opsi_a;
    public $opsi_b;
    public $opsi_c;
    public $opsi_d;
    public $jawaban_benar;

    public $search = '';
    public $showModal = false;
    public $editMode = false;

    public $selectedQuestions = [];
    public $selectAll = false;
    public $confirmingDelete = false;
    public $confirmingBulkDelete = false;
    public $questionIdToDelete;

    protected $rules = [
        'pertanyaan' => 'required|string',
        'opsi_a' => 'required|string',
        'opsi_b' => 'required|string',
        'opsi_c' => 'required|string',
        'opsi_d' => 'required|string',
        'jawaban_benar' => 'required|in:a,b,c,d',
    ];

    /** Load quiz_id ketika halaman dibuka */
    public function mount($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    /** Reset Form */
    public function resetForm()
    {
        $this->reset(['question_id', 'pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'jawaban_benar', 'editMode']);
        $this->resetValidation();
        $this->showModal = true;
    }

    /** Simpan / update pertanyaan */
    public function save()
    {
        $this->validate();

        $data = [
            'quiz_id' => $this->quiz_id,
            'pertanyaan' => $this->pertanyaan,
            'opsi_a' => $this->opsi_a,
            'opsi_b' => $this->opsi_b,
            'opsi_c' => $this->opsi_c,
            'opsi_d' => $this->opsi_d,
            'jawaban_benar' => $this->jawaban_benar,
        ];

        if ($this->editMode) {
            Quiz_question::findOrFail($this->question_id)->update($data);
        } else {
            Quiz_question::create($data);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Pertanyaan berhasil disimpan!');
    }

    /** Edit pertanyaan */
    public function edit($id)
    {
        $q = Quiz_question::findOrFail($id);
        $this->fill($q->toArray());
        $this->question_id = $id;
        $this->editMode = true;
        $this->showModal = true;
    }

    /** Konfirmasi hapus */
    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->questionIdToDelete = $id;
    }

    /** Hapus satu pertanyaan */
    public function deleteQuestionConfirmed()
    {
        Quiz_question::findOrFail($this->questionIdToDelete)->delete();
        $this->confirmingDelete = false;
    }

    /** Konfirmasi bulk delete */
    public function confirmBulkDelete()
    {
        $this->confirmingBulkDelete = true;
    }

    /** Hapus banyak */
    public function deleteSelectedConfirmed()
    {
        Quiz_question::whereIn('id', $this->selectedQuestions)->delete();
        $this->selectedQuestions = [];
        $this->confirmingBulkDelete = false;
    }

    /** Select All */
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedQuestions = Quiz_question::where('quiz_id', $this->quiz_id)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedQuestions = [];
        }
    }

    /** Render view */
    public function render()
    {
        $quiz = Quizzes::findOrFail($this->quiz_id);

        $questions = Quiz_question::where('quiz_id', $this->quiz_id)
            ->where('pertanyaan', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.quiz.question-manager', compact('quiz', 'questions'))
            ->title('Manajemen Pertanyaan Kuis');
    }
}
