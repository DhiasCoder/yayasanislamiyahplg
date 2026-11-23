<?php

namespace App\Http\Controllers\Admin\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\PpdbDokumen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokumenPreviewController extends Controller
{
    public function __invoke($id)
    {
        $sekolahId = DB::table('user_roles')
            ->where('user_id', Auth::id())
            ->whereNotNull('sekolah_id')
            ->value('sekolah_id');

        abort_if(!$sekolahId, 403);

        $dokumen = PpdbDokumen::whereHas('pendaftar.periode', function ($query) use ($sekolahId) {
            $query->where('sekolah_id', $sekolahId);
        })->findOrFail($id);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($dokumen->file_path), 404);

        $filePath = $disk->path($dokumen->file_path);

        $mimeType = mime_content_type($filePath);

        abort_unless(
            in_array($mimeType, [
                'image/jpeg',
                'image/png',
                'image/webp',
                'application/pdf',
            ]),
            415,
            'Format dokumen tidak didukung.'
        );

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' .
                basename($dokumen->nama_file ?? $dokumen->file_path) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
