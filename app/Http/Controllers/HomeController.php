<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Pengaturan;
use App\Models\Testimoni;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Jadwal;

class HomeController extends Controller
{
    public function index()
    {
        $schedules = Jadwal::with(['guru', 'siswa'])
            ->where('aktif', true)
            ->get();

        $bimbingan = \App\Models\Bimbingan::with(['guru', 'siswa'])
            ->where('aktif', true)
            ->get();

        return Inertia::render('Home', [
            'settings' => Pengaturan::first(),
            'testimonials' => Testimoni::where('aktif', true)->get(),
            'teachers_count' => Guru::count(),
            'students_count' => Siswa::count(),
            'schedules' => $schedules,
            'kegiatan_terjadwal' => [],
            'bimbingan' => $bimbingan,
        ]);
    }
}
