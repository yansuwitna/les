<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SiswaController extends Controller
{
    public static function generateNomorSiswa(): string
    {
        $year = date('Y'); // e.g. 2026
        do {
            $random = str_pad((string) mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $nomor = $year . $random; // 10 digits
        } while (
            Siswa::where('nomor_siswa', $nomor)->exists()
        );

        return $nomor;
    }

    public function index(Request $request)
    {
        $cari = $request->input('cari') ?: $request->input('search');

        $daftar_siswa = Siswa::when($cari, function ($query, $cari) {
                $query->where('nama', 'like', "%{$cari}%")
                      ->orWhere('nomor_siswa', 'like', "%{$cari}%")
                      ->orWhere('nama_wali', 'like', "%{$cari}%")
                      ->orWhere('nis', 'like', "%{$cari}%")
                      ->orWhere('no_hp', 'like', "%{$cari}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Siswa/Index', [
            'daftar_siswa' => $daftar_siswa,
            'nomor_siswa_berikutnya' => self::generateNomorSiswa(),
            'filter' => ['cari' => $cari],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'nis' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'kata_sandi' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'aktif' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama siswa wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // Generate automatic 10-digit number
        $nomor_siswa = self::generateNomorSiswa();

        // Default password is the student number if not explicitly set
        $kata_sandi = $validated['kata_sandi'] ?: $nomor_siswa;

        $nama_wali = !empty($validated['nama_wali']) ? $validated['nama_wali'] : ('Wali dari ' . $validated['nama']);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('siswa', 'public');
        }

        Siswa::create([
            'nomor_siswa' => $nomor_siswa,
            'kata_sandi' => Hash::make($kata_sandi),
            'nama' => $validated['nama'],
            'nama_wali' => $nama_wali,
            'nis' => $validated['nis'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'foto' => $fotoPath,
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : true,
        ]);

        return redirect()->back()->with('success', "Siswa {$validated['nama']} berhasil didaftarkan ke tabel siswa. Nomor Siswa login: {$nomor_siswa}");
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'nis' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'kata_sandi' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'aktif' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama siswa wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'nama_wali' => $validated['nama_wali'] ?? null,
            'nis' => $validated['nis'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ];

        if ($request->has('aktif')) {
            $data['aktif'] = $request->boolean('aktif');
        }

        if ($request->filled('kata_sandi')) {
            $data['kata_sandi'] = Hash::make($validated['kata_sandi']);
        }

        if ($request->hasFile('foto')) {
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($data);

        return redirect()->back()->with('success', 'Data Siswa berhasil diperbarui di tabel siswa.');
    }

    public function toggleStatus(Siswa $siswa)
    {
        $siswa->update([
            'aktif' => !$siswa->aktif,
        ]);

        $statusStr = $siswa->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Status akun siswa {$siswa->nama} berhasil {$statusStr}.");
    }

    public function destroy(Siswa $siswa)
    {
        if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $siswa->delete();
        return redirect()->back()->with('success', 'Data Siswa berhasil dihapus.');
    }
}
