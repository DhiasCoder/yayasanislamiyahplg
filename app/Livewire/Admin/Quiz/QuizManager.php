<?php

namespace App\Livewire\Admin\Quiz;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quizzes;
use App\Models\Kelas;
use App\Models\User;

#[\Livewire\Attributes\Layout('admin.layouts.app')]
class QuizManager extends Component
{
    use WithPagination;

    public $quiz_id, $judul, $deskripsi, $guru_id, $kelas_id, $is_verified = false, $is_global = false;
    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $selectedQuiz = [];
    public $selectAll = false;
    public $confirmingDelete = false;
    public $confirmingBulkDelete = false;
    public $quizIdToDelete;

    protected $rules = [
        'judul' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'guru_id' => 'required|exists:users,id',
        'kelas_id' => 'required|exists:kelas,id',
        'is_global' => 'boolean',
    ];

    public function resetForm()
    {
        $this->reset(['quiz_id', 'judul', 'deskripsi', 'guru_id', 'kelas_id', 'is_verified', 'is_global', 'editMode']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'guru_id' => $this->guru_id,
            'kelas_id' => $this->kelas_id,
            'is_verified' => $this->is_verified,
            'is_global' => $this->is_global,
        ];

        if ($this->editMode) {
            Quizzes::findOrFail($this->quiz_id)->update($data);
        } else {
            Quizzes::create($data);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Kuis berhasil disimpan!');
    }

    public function edit($id)
    {
        $quiz = Quizzes::findOrFail($id);
        $this->fill($quiz->toArray());
        $this->editMode = true;
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->quizIdToDelete = $id;
    }

    public function deleteQuizConfirmed()
    {
        Quizzes::findOrFail($this->quizIdToDelete)->delete();
        $this->confirmingDelete = false;
    }

    public function confirmBulkDelete()
    {
        $this->confirmingBulkDelete = true;
    }

    public function deleteSelectedConfirmed()
    {
        Quizzes::whereIn('id', $this->selectedQuiz)->delete();
        $this->selectedQuiz = [];
        $this->confirmingBulkDelete = false;
    }

    public function toggleVerification($id)
    {
        $quiz = Quizzes::findOrFail($id);
        $quiz->update(['is_verified' => !$quiz->is_verified]);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedQuiz = Quizzes::pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedQuiz = [];
        }
    }

    public function render()
    {
        $quizzes = Quizzes::with(['guru', 'kelas'])
            ->where('judul', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $kelasList = Kelas::all();
        $guruList = User::where('role', 'guru')->get();

        return view('livewire.admin.quiz.quiz-manager', compact('quizzes', 'kelasList', 'guruList'))
            ->title('Manajemen Kuis');
    }
}
