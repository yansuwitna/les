<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari') ?: $request->input('search');

        $daftar_guru = Guru::when($cari, function ($query, $cari) {
                $query->where('nama', 'like', "%{$cari}%")
                      ->orWhere('nik', 'like', "%{$cari}%")
                      ->orWhere('nip', 'like', "%{$cari}%")
                      ->orWhere('email', 'like', "%{$cari}%")
                      ->orWhere('no_hp', 'like', "%{$cari}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Guru/Index', [
            'daftar_guru' => $daftar_guru,
            'teachers' => $daftar_guru,
            'filters' => ['cari' => $cari, 'search' => $cari],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|numeric|digits:16|unique:guru,nik',
            'kata_sandi' => 'required|string|min:8',
            'email' => 'nullable|string|email|max:255|unique:guru,email',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'aktif' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama lengkap guru wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus berjumlah 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar sebagai guru.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 8 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh guru lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('guru', 'public');
        }

        Guru::create([
            'nama' => $validated['nama'],
            'nik' => $validated['nik'],
            'kata_sandi' => Hash::make($validated['kata_sandi']),
            'email' => $validated['email'] ?? null,
            'nip' => $validated['nip'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'foto' => $fotoPath,
            'aktif' => $request->has('aktif') ? $request->boolean('aktif') : true,
        ]);

        return redirect()->back()->with('success', 'Data Guru berhasil didaftarkan ke tabel guru dengan NIK sebagai username login.');
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => [
                'required',
                'numeric',
                'digits:16',
                Rule::unique('guru', 'nik')->ignore($guru->id),
            ],
            'kata_sandi' => 'nullable|string|min:8',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('guru', 'email')->ignore($guru->id),
            ],
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'aktif' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus berjumlah 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar untuk guru lain.',
            'kata_sandi.min' => 'Kata sandi minimal 8 karakter.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'nik' => $validated['nik'],
            'email' => $validated['email'] ?? null,
            'nip' => $validated['nip'] ?? null,
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
            if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
                Storage::disk('public')->delete($guru->foto);
            }
            $data['foto'] = $request->file('foto')->store('guru', 'public');
        }

        $guru->update($data);

        return redirect()->back()->with('success', 'Data Guru berhasil diperbarui di tabel guru.');
    }

    public function toggleStatus(Guru $guru)
    {
        $guru->update([
            'aktif' => !$guru->aktif,
        ]);

        $statusStr = $guru->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Status akun guru {$guru->nama} berhasil {$statusStr}.");
    }

    public function destroy(Guru $guru)
    {
        if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
            Storage::disk('public')->delete($guru->foto);
        }

        $guru->delete();
        return redirect()->back()->with('success', 'Data Guru berhasil dihapus.');
    }
}
