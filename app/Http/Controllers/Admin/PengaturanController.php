<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::firstOrCreate([], [
            'nama_les' => 'Les Ceria',
            'alamat_les' => 'Jl. Pendidikan No. 1, Kota Belajar',
            'kontak_les' => '0812-3456-7890',
        ]);

        return Inertia::render('Admin/Pengaturan/Index', [
            'pengaturan' => $pengaturan,
        ]);
    }

    public function update(Request $request)
    {
        $pengaturan = Pengaturan::firstOrCreate([]);

        $validated = $request->validate([
            'nama_les' => 'required|string|max:255',
            'alamat_les' => 'nullable|string',
            'kontak_les' => 'nullable|string|max:100',
            'logo_les' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'slide_les' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'nama_les.required' => 'Nama lembaga bimbingan belajar wajib diisi.',
            'logo_les.image' => 'File logo harus berupa gambar.',
            'logo_les.max' => 'Ukuran logo maksimal 2MB.',
            'slide_les.image' => 'File slide/banner harus berupa gambar.',
            'slide_les.max' => 'Ukuran slide/banner maksimal 4MB.',
        ]);

        $data = [
            'nama_les' => $validated['nama_les'],
            'alamat_les' => $validated['alamat_les'] ?? null,
            'kontak_les' => $validated['kontak_les'] ?? null,
        ];

        if ($request->hasFile('logo_les')) {
            if ($pengaturan->logo_les && Storage::disk('public')->exists($pengaturan->logo_les)) {
                Storage::disk('public')->delete($pengaturan->logo_les);
            }
            $data['logo_les'] = $request->file('logo_les')->store('pengaturan', 'public');
        }

        if ($request->hasFile('slide_les')) {
            if ($pengaturan->slide_les && Storage::disk('public')->exists($pengaturan->slide_les)) {
                Storage::disk('public')->delete($pengaturan->slide_les);
            }
            $data['slide_les'] = $request->file('slide_les')->store('pengaturan', 'public');
        }

        $pengaturan->update($data);

        return redirect()->back()->with('success', 'Identitas lembaga dan website berhasil diperbarui.');
    }
}
