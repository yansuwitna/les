<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bimbingan;
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

        $jadwal = Bimbingan::with(['guru', 'siswa'])
            ->when($cari, function ($query, $cari) {
                $query->whereHas('guru', fn ($q) => $q->where('nama', 'like', "%{$cari}%"))
                      ->orWhereHas('siswa', fn ($q) => $q->where('nama', 'like', "%{$cari}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Jadwal/Index', [
            'daftar_jadwal' => $jadwal,
            'daftar_guru' => Guru::where('aktif', true)->orderBy('nama')->get(['id', 'nama', 'nik']),
            'daftar_siswa' => Siswa::where('aktif', true)->orderBy('nama')->get(['id', 'nama', 'nomor_siswa']),
            'filters' => [
                'cari' => $cari,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'siswa_id' => 'required|exists:siswa,id',
            'aktif' => 'nullable|boolean',
        ], [
            'guru_id.required' => 'Guru pembimbing wajib dipilih.',
            'guru_id.exists' => 'Guru yang dipilih tidak valid.',
            'siswa_id.required' => 'Siswa bimbingan wajib dipilih.',
            'siswa_id.exists' => 'Siswa yang dipilih tidak valid.',
        ]);

        Bimbingan::create([
            'guru_id' => $validated['guru_id'],
            'siswa_id' => $validated['siswa_id'],
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : true,
        ]);

        return redirect()->back()->with('success', 'Data pembimbing siswa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jadwal = Bimbingan::findOrFail($id);

        $validated = $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'siswa_id' => 'required|exists:siswa,id',
            'aktif' => 'nullable|boolean',
        ], [
            'guru_id.required' => 'Guru pembimbing wajib dipilih.',
            'guru_id.exists' => 'Guru yang dipilih tidak valid.',
            'siswa_id.required' => 'Siswa bimbingan wajib dipilih.',
            'siswa_id.exists' => 'Siswa yang dipilih tidak valid.',
        ]);

        $jadwal->update([
            'guru_id' => $validated['guru_id'],
            'siswa_id' => $validated['siswa_id'],
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : $jadwal->aktif,
        ]);

        return redirect()->back()->with('success', 'Data pembimbing siswa berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $jadwal = Bimbingan::findOrFail($id);

        $jadwal->update([
            'aktif' => !$jadwal->aktif,
        ]);

        $statusStr = $jadwal->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Status bimbingan berhasil {$statusStr}.");
    }

    public function destroy($id)
    {
        $jadwal = Bimbingan::findOrFail($id);
        $jadwal->delete();
        return redirect()->back()->with('success', 'Data pembimbing siswa berhasil dihapus.');
    }
}
