<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Materi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari');
        $filter_hari = $request->input('hari');

        $jadwal = Jadwal::with(['guru', 'siswa'])
            ->when($cari, function ($query, $cari) {
                $query->whereHas('guru', fn ($q) => $q->where('nama', 'like', "%{$cari}%"))
                      ->orWhereHas('siswa', fn ($q) => $q->where('nama', 'like', "%{$cari}%"));
            })
            ->when($filter_hari, function ($query, $hari) {
                $query->where('hari', $hari);
            })
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Jadwal/Index', [
            'daftar_jadwal' => $jadwal,
            'daftar_guru' => Guru::where('aktif', true)->orderBy('nama')->get(['id', 'nama', 'nik']),
            'daftar_siswa' => Siswa::where('aktif', true)->orderBy('nama')->get(['id', 'nama', 'nomor_siswa']),
            'filters' => [
                'cari' => $cari,
                'hari' => $filter_hari,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'siswa_id' => 'required|exists:siswa,id',
            'hari' => 'nullable|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'nullable|string',
            'jam_selesai' => 'nullable|string',
            'aktif' => 'nullable|boolean',
        ], [
            'guru_id.required' => 'Guru pembimbing wajib dipilih.',
            'guru_id.exists' => 'Guru yang dipilih tidak valid.',
            'siswa_id.required' => 'Siswa bimbingan wajib dipilih.',
            'siswa_id.exists' => 'Siswa yang dipilih tidak valid.',
            'hari.in' => 'Pilihan hari tidak valid.',
        ]);

        Jadwal::create([
            'guru_id' => $validated['guru_id'],
            'siswa_id' => $validated['siswa_id'],
            'hari' => $validated['hari'] ?? 'Senin',
            'jam_mulai' => $validated['jam_mulai'] ?? '00:00',
            'jam_selesai' => $validated['jam_selesai'] ?? '00:00',
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : true,
        ]);

        return redirect()->back()->with('success', 'Data pembimbing siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'siswa_id' => 'required|exists:siswa,id',
            'hari' => 'nullable|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'nullable|string',
            'jam_selesai' => 'nullable|string',
            'aktif' => 'nullable|boolean',
        ], [
            'guru_id.required' => 'Guru pembimbing wajib dipilih.',
            'siswa_id.required' => 'Siswa bimbingan wajib dipilih.',
        ]);

        $jadwal->update([
            'guru_id' => $validated['guru_id'],
            'siswa_id' => $validated['siswa_id'],
            'hari' => $validated['hari'] ?? $jadwal->hari,
            'jam_mulai' => $validated['jam_mulai'] ?? $jadwal->jam_mulai,
            'jam_selesai' => $validated['jam_selesai'] ?? $jadwal->jam_selesai,
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : $jadwal->aktif,
        ]);

        return redirect()->back()->with('success', 'Data pembimbing siswa berhasil diperbarui.');
    }

    public function toggleStatus(Jadwal $jadwal)
    {
        $jadwal->update([
            'aktif' => !$jadwal->aktif,
        ]);

        $statusStr = $jadwal->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Status bimbingan berhasil {$statusStr}.");
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->back()->with('success', 'Data pembimbing siswa berhasil dihapus.');
    }
}
