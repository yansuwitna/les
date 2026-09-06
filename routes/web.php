<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Ortu\DashboardController as OrtuDashboard;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::guard('admin')->user()
        ?? \Illuminate\Support\Facades\Auth::guard('guru')->user()
        ?? \Illuminate\Support\Facades\Auth::guard('siswa')->user()
        ?? auth()->user();

    $peran = $user?->peran ?: $user?->role;
    if ($peran === 'admin') return redirect('/admin');
    if ($peran === 'guru') return redirect('/guru');
    if ($peran === 'ortu' || $peran === 'siswa') return redirect('/ortu');
    return redirect('/masuk');
})->name('dashboard');

Route::middleware(['auth:admin,guru,siswa,web'])->group(function () {
    
    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
        
        // Data Guru (Indonesian URL: /admin/guru)
        Route::patch('guru/{guru}/status', [GuruController::class, 'toggleStatus'])->name('guru.status');
        Route::resource('guru', GuruController::class)->except(['create', 'show', 'edit']);
        Route::get('teachers', fn() => redirect()->route('admin.guru.index'))->name('teachers.index');
        Route::post('teachers', [GuruController::class, 'store'])->name('teachers.store');
        Route::put('teachers/{teacher}', [GuruController::class, 'update'])->name('teachers.update');
        Route::delete('teachers/{teacher}', [GuruController::class, 'destroy'])->name('teachers.destroy');

        // Data Siswa (Indonesian URL: /admin/siswa)
        Route::patch('siswa/{siswa}/status', [SiswaController::class, 'toggleStatus'])->name('siswa.status');
        Route::resource('siswa', SiswaController::class)->except(['create', 'show', 'edit']);

        // Jadwal Pelajaran (Indonesian URL: /admin/jadwal)
        Route::patch('jadwal/{jadwal}/status', [JadwalController::class, 'toggleStatus'])->name('jadwal.status');
        Route::resource('jadwal', JadwalController::class)->except(['create', 'show', 'edit']);

        // Identitas & Web (Indonesian URL: /admin/identitas-web)
        Route::get('identitas-web', [PengaturanController::class, 'index'])->name('identitas.index');
        Route::post('identitas-web', [PengaturanController::class, 'update'])->name('identitas.update');
    });

    // Guru Routes (Indonesian URL: /guru)
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/', [GuruDashboard::class, 'index'])->name('dashboard');
        
        // Siswa Bimbingan Guru (URL: /guru/bimbingan)
        Route::get('bimbingan', [GuruDashboard::class, 'jadwal'])->name('bimbingan.index');
        Route::put('bimbingan/{bimbingan}/jadwal', [GuruDashboard::class, 'updateJadwalBimbingan'])->name('bimbingan.jadwal.update');
        Route::get('jadwal', fn() => redirect()->route('guru.bimbingan.index'))->name('jadwal.index');

        // Materi Pembelajaran (Tabel Materi)
        Route::get('materi', [GuruDashboard::class, 'materi'])->name('materi.index');
        Route::post('materi', [GuruDashboard::class, 'storeMateri'])->name('materi.store');
        Route::put('materi/{materi}', [GuruDashboard::class, 'updateMateri'])->name('materi.update');
        Route::delete('materi/{materi}', [GuruDashboard::class, 'destroyMateri'])->name('materi.destroy');

        // Target Capaian Pembelajaran (Tabel Target)
        // URL: guru/target?materi_id=kode enkripsi
        Route::get('target', [GuruDashboard::class, 'target'])->name('target.index');
        Route::post('target', [GuruDashboard::class, 'storeTarget'])->name('target.store');
        Route::put('target/{target}', [GuruDashboard::class, 'updateTarget'])->name('target.update');
        Route::delete('target/{target}', [GuruDashboard::class, 'destroyTarget'])->name('target.destroy');

        // Log Kegiatan Harian
        Route::get('kegiatan', [GuruDashboard::class, 'kegiatan'])->name('kegiatan.index');
        Route::get('kegiatan/cetak', [GuruDashboard::class, 'cetakKegiatan'])->name('kegiatan.cetak');
        Route::post('kegiatan', [GuruDashboard::class, 'storeKegiatan'])->name('kegiatan.store');
        Route::put('kegiatan/{kegiatan}', [GuruDashboard::class, 'updateKegiatan'])->name('kegiatan.update');
        Route::delete('kegiatan/{kegiatan}', [GuruDashboard::class, 'destroyKegiatan'])->name('kegiatan.destroy');
    });

    // Ortu / Siswa Routes (Indonesian URL: /ortu atau /siswa)
    Route::middleware('role:ortu')->prefix('ortu')->name('ortu.')->group(function () {
        Route::get('/', [OrtuDashboard::class, 'index'])->name('dashboard');
    });
    Route::middleware('role:ortu')->get('/siswa-dashboard', [OrtuDashboard::class, 'index'])->name('siswa.dashboard');

    // Profile Routes (Indonesian URL: /profil)
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil', [ProfileController::class, 'update']);
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', fn() => redirect('/profil'));
});

require __DIR__.'/auth.php';
