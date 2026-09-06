<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Jadwal;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'teachers' => Guru::count(),
                'students' => Siswa::count(),
                'schedules' => Jadwal::count(),
                'guru' => Guru::count(),
                'siswa' => Siswa::count(),
                'jadwal' => Jadwal::count(),
            ]
        ]);
    }
}
