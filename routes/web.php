<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


// Route Superadmin
use App\Livewire\Superadmin\DashboardSuperadmin;
use App\Livewire\Superadmin\Sekolah\SekolahManager;
use App\Livewire\Superadmin\Pengguna\PenggunaManager;
use App\Livewire\Superadmin\Role\RoleManager;
use App\Livewire\Superadmin\TahunAjaran\TahunAjaranManager;

// Route Admin
use App\Livewire\Admin\DashboardComponent;
use App\Livewire\Admin\Siswa\SiswaManager;
use App\Livewire\Admin\Guru\GuruManager;
use App\Livewire\Admin\PenempatanSiswa\PenempatanSiswaManager;
use App\Livewire\Admin\Kelas\KelasManager;
use App\Livewire\Admin\Jurusan\JurusanManager;
use App\Livewire\Admin\MataPelajaran\MataPelajaranManager;
use App\Livewire\Admin\Pengguna\PenggunaManagers;
use App\Livewire\Admin\TahunAjaran\TahunAjaranManagers;
use App\Livewire\Admin\PenugasanGuru\PenugasanGuruManager;
use App\Livewire\Admin\Jadwal\JadwalManager;
use App\Livewire\Admin\Materi\MateriManager;
use App\Livewire\Admin\Diskusi\DiskusiMateriManager;
use App\Livewire\Admin\Ujian\UjianManager;
use App\Livewire\Admin\Absensi\AbsensiManager;
use App\Livewire\Admin\Akademik\AkademikManager;
use App\Livewire\Admin\Pendaftaran\PeriodePendaftaranManager;
use App\Livewire\Admin\Ppdb\PeriodeManager;
use App\Livewire\Admin\Ppdb\PendaftarManager;
use App\Livewire\Admin\Ppdb\VerifikasiDokumen;
use App\Livewire\Admin\Ppdb\PembayaranManager;
use App\Livewire\Admin\Ppdb\SeleksiManager;
use App\Livewire\Admin\Ppdb\DaftarUlangManager;
use App\Http\Controllers\Admin\Ppdb\DokumenPreviewController;

// Route Guru
use App\Livewire\Guru\DashboardGuruComponent;
use App\Livewire\Guru\Materi\MateriAbsensiManager;
use App\Livewire\Guru\Diskusi\DiskusiMateriGuruManager;
use App\Livewire\Guru\Kelas\KelasMengajarManager;
use App\Livewire\Guru\Kelas\DetailKelasMengajar;
use App\Livewire\Guru\TugasUjian\TugasUjianManager;
use App\Livewire\Guru\TugasUjian\UjianCreate;
use App\Livewire\Guru\TugasUjian\UjianQuestionManager;
use App\Livewire\Guru\TugasUjian\UjianEdit;
use App\Livewire\Guru\TugasUjian\TugasForm;
use App\Livewire\Guru\Nilai\NilaiManager;

// Route Siswa
use App\Livewire\Murid\DashboardMurid;
use App\Livewire\Murid\MateriListComponent;
use App\Livewire\Murid\MataPelajaran\MataPelajaranMuridManager;
use App\Livewire\Murid\MataPelajaran\DetailMataPelajaranMurid;
use App\Livewire\Murid\Materi\DetailMateriMurid;
use App\Livewire\Murid\Materi\DiskusiMateriMurid;
use App\Livewire\Murid\Materi\AbsensiMateriMurid;
use App\Livewire\Murid\Tugas\TugasUjianComponent;
use App\Http\Controllers\Murid\TugasFileController;
use App\Livewire\Murid\Tugas\UjianMengerjakanComponent;
use App\Livewire\Murid\Nilai\NilaiSaya;

// PPDB
use App\Livewire\Ppdb\PendaftaranForm;
use App\Livewire\Ppdb\DokumenForm;
use App\Livewire\Ppdb\PembayaranForm;
use App\Livewire\Ppdb\StatusPendaftaran;

Route::get('/', function () {
    return view('welcome');
});

// ===================== AUTH =====================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ===================== SUPER ADMIN =====================
Route::prefix('superadmin')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', DashboardSuperadmin::class)->name('superadmin.dashboard');
    Route::get('/sekolah', SekolahManager::class)->name('superadmin.sekolah');
    Route::get('/pengguna', PenggunaManager::class)->name('superadmin.pengguna');
    Route::get('/role', RoleManager::class)->name('superadmin.role');
    Route::get('/tahun-ajaran', TahunAjaranManager::class)->name('superadmin.tahun-ajaran');
});

// ===================== ADMIN =====================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', DashboardComponent::class)->name('admin.dashboard');
    Route::get('/siswa', SiswaManager::class)->name('siswa');
    Route::get('/guru', GuruManager::class)->name('guru');
    Route::get('/penempatan', PenempatanSiswaManager::class)->name('penempatan');
    Route::get('/kelas', KelasManager::class)->name('kelas');
    Route::get('/jurusan', JurusanManager::class)->name('jurusan');
    Route::get('/mapel', MataPelajaranManager::class)->name('mapel');
    Route::get('/pengguna', PenggunaManagers::class)->name('pengguna');
    Route::get('/tahunajaran', TahunAjaranManagers::class)->name('tahunajaran');
    Route::get('/penugasanguru', PenugasanGuruManager::class)->name('penugasanguru');
    Route::get('/jadwal', JadwalManager::class)->name('jadwal');
    Route::get('/materi', MateriManager::class)->name('materi');
    Route::get('/diskusimateri', DiskusiMateriManager::class)->name('diskusimateri');
    Route::get('/ujian', UjianManager::class)->name('ujian');
    Route::get('/absensi', AbsensiManager::class)->name('absensi');
    Route::get('/akademik', AkademikManager::class)->name('akademik');
    Route::get('/periode-pendaftaran', PeriodePendaftaranManager::class)->name('periode-pendaftaran');
    //PPDB
    Route::get('/ppdb/periode', PeriodeManager::class)->name('periode');
    Route::get('/ppdb/pendaftar', PendaftarManager::class)->name('pendaftar');
    Route::get('/ppdb/dokumen', VerifikasiDokumen::class)->name('dokumen');
    Route::get('/ppdb/pembayaran', PembayaranManager::class)->name('pembayaran');
    Route::get('/ppdb/seleksi', SeleksiManager::class)->name('seleksi');
    Route::get('/ppdb/daftar-ulang', DaftarUlangManager::class)->name('daftar-ulang');
    Route::get('/dokumen/{id}/preview', DokumenPreviewController::class)->name('dokumen.preview');
});

// ===================== GURU =====================
Route::prefix('guru')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', DashboardGuruComponent::class)->name('guru.dashboard');
    Route::get('/materi', MateriAbsensiManager::class)->name('guru.materi');
    Route::get('/diskusi-materi/{materi}', DiskusiMateriGuruManager::class)->name('guru.diskusi');
    Route::get('/kelas-manager', KelasMengajarManager::class)->name('guru.kelas-manager');
    Route::get('/kelas-manager/{pengajaran}', DetailKelasMengajar::class)->name('guru.kelas.detail');
    Route::get('/tugas/create', TugasForm::class)->name('guru.tugas.create');
    Route::get('/tugas/{tugas}/edit', TugasForm::class)->name('guru.tugas.edit');
    Route::get('/tugas-ujian', TugasUjianManager::class)->name('guru.tugas-ujian');
    Route::get('/ujian/create', UjianCreate::class)->name('guru.ujian.create');
    Route::get('/ujian/{ujian}/soal', UjianQuestionManager::class)->name('guru.ujian.soal');
    Route::get('/ujian/{ujian}/edit', UjianEdit::class)->name('guru.ujian.edit');
    Route::get('/nilai', NilaiManager::class)->name('guru.nilai');
});

// ===================== MURID =====================
Route::prefix('siswa')->middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/dashboard', DashboardMurid::class)->name('siswa.dashboard');
    Route::get('/materi', MateriListComponent::class)->name('siswa.materi');
    Route::get('/mata-pelajaran', MataPelajaranMuridManager::class)->name('siswa.mapel');
    Route::get('/mata-pelajaran/{pengajaran}', DetailMataPelajaranMurid::class)->name('siswa.mapel.detail');
    Route::get('/materi/{materi}', DetailMateriMurid::class)->name('materi.detail');
    Route::get('/materi/{materi}/diskusi', DiskusiMateriMurid::class)->name('materi.diskusi');
    Route::get('/materi/{materi}/absensi', AbsensiMateriMurid::class)->name('materi.absensi');
    Route::get('/tugas-ujian', TugasUjianComponent::class)->name('tugas-ujian');
    Route::get('/murid/tugas/{id}/file-soal', [TugasFileController::class, 'show'])->name('murid.tugas.file-soal');
    Route::get('/murid/ujian/{ujianId}/kerjakan', UjianMengerjakanComponent::class)->name('murid.ujian.kerjakan');
    Route::get('/nilai-saya', NilaiSaya::class)->name('siswa.nilai');
});

Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/daftar/{periode}', PendaftaranForm::class)
        ->name('daftar');

    Route::get('/dokumen', DokumenForm::class)
        ->name('dokumen');

    Route::get('/pembayaran', PembayaranForm::class)
        ->name('pembayaran');

    Route::get('/cek-status', StatusPendaftaran::class)
        ->name('cek-status');
});
