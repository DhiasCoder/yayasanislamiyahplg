<?php

namespace App\Events;


use App\Models\Diskusi_materi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class KomentarMateriEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $komentar;

    public function __construct(Diskusi_materi $komentar)
    {
        $this->komentar = $komentar->loadMissing('user');
    }

   public function broadcastOn(): array
    {
        $channels = [
            new Channel('diskusi-materi.' . $this->komentar->materi_id),
        ];

        $sekolahId = $this->komentar
            ->materi
            ->pengajaran
            ->kelas
            ->sekolah_id;

        $channels[] = new Channel('admin-diskusi.' . $sekolahId);

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'KomentarMateriEvent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->komentar->id,
            'materi_id' => $this->komentar->materi_id,
            'isi' => $this->komentar->isi,
            'user' => $this->komentar->user?->name ?? 'Pengguna',
            'waktu' => $this->komentar->created_at?->diffForHumans(),
            'user_id' => $this->komentar->user_id,
            'parent_id' => $this->komentar->parent_id,
        ];
    }
}