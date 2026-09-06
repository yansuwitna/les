<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Bimbingan;
use App\Models\Jadwal;
use App\Models\Target;
use App\Models\Kegiatan;
use App\Models\Siswa;
use App\Models\Materi;

class DashboardController extends Controller
{
    private function getGuruId()
    {
        $user = auth()->guard('guru')->user() ?? auth()->user();
        return $user?->id;
    }

    public function index()
    {
        $guruId = $this->getGuruId();

        // Ambil jadwal bimbingan aktif milik guru ini dari tabel bimbingan
        $jadwalHariIni = Bimbingan::with('siswa')
            ->where('guru_id', $guruId)
            ->where('aktif', true)
            ->get();

        // Ambil kegiatan terbaru milik guru ini
        $kegiatanTerjadwal = Kegiatan::with(['target.siswa', 'target.materi'])
            ->whereHas('target', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->limit(5)
            ->get();

        $totalSiswa = Bimbingan::where('guru_id', $guruId)->where('aktif', true)->distinct('siswa_id')->count('siswa_id');
        $totalJadwal = Bimbingan::where('guru_id', $guruId)->where('aktif', true)->count();
        $totalTarget = Target::where('guru_id', $guruId)->count();
        $totalKegiatan = Kegiatan::whereHas('target', function ($q) use ($guruId) {
            $q->where('guru_id', $guruId);
        })->count();

        // Target aktif & kegiatan terbaru
        $targetTerbaru = Target::with(['siswa', 'materi', 'kegiatan'])
            ->where('guru_id', $guruId)
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Guru/Dashboard', [
            'stats' => [
                'total_siswa' => $totalSiswa,
                'total_jadwal' => $totalJadwal,
                'total_target' => $totalTarget,
                'total_kegiatan' => $totalKegiatan,
            ],
            'jadwal_hari_ini' => $jadwalHariIni,
            'kegiatan_terjadwal' => $kegiatanTerjadwal,
            'target_terbaru' => $targetTerbaru,
        ]);
    }

    // Siswa Bimbingan Guru
    public function jadwal(Request $request)
    {
        $guruId = $this->getGuruId();

        // Ambil ID siswa unik yang dialokasikan ke guru ini
        $siswaIds = Jadwal::where('guru_id', $guruId)
            ->distinct()
            ->pluck('siswa_id');

        $query = Siswa::with(['bimbingan' => function ($q) use ($guruId) {
            $q->where('guru_id', $guruId);
        }])->whereIn('id', $siswaIds);

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                  ->orWhere('nomor_siswa', 'like', "%{$cari}%")
                  ->orWhere('nis', 'like', "%{$cari}%")
                  ->orWhere('nama_wali', 'like', "%{$cari}%")
                  ->orWhere('no_hp', 'like', "%{$cari}%");
            });
        }

        $daftarSiswa = $query->orderBy('nama', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Guru/Jadwal/Index', [
            'daftar_siswa' => $daftarSiswa,
            'filters' => $request->only(['cari']),
        ]);
    }

    // Update Jadwal Bimbingan Siswa (Hari, Jam Mulai, Jam Selesai)
    public function updateJadwalBimbingan(Request $request, $bimbingan = null)
    {
        $guruId = $this->getGuruId();

        $validated = $request->validate([
            'siswa_id' => 'nullable',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|string',
            'jam_selesai' => 'required|string',
        ], [
            'hari.required' => 'Hari wajib dipilih.',
            'hari.in' => 'Pilihan hari tidak valid.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
        ]);

        $bimbinganModel = null;
        if ($bimbingan instanceof \App\Models\Bimbingan) {
            $bimbinganModel = $bimbingan;
        } elseif (is_numeric($bimbingan) && $bimbingan > 0) {
            $bimbinganModel = \App\Models\Bimbingan::find($bimbingan);
        }

        if (!$bimbinganModel && $request->filled('siswa_id')) {
            $bimbinganModel = \App\Models\Bimbingan::where('guru_id', $guruId)
                ->where('siswa_id', $request->siswa_id)
                ->first();
        }

        if (!$bimbinganModel) {
            if ($request->filled('siswa_id')) {
                $bimbinganModel = \App\Models\Bimbingan::create([
                    'guru_id' => $guruId,
                    'siswa_id' => $request->siswa_id,
                    'hari' => $validated['hari'],
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_selesai' => $validated['jam_selesai'],
                    'aktif' => true,
                ]);
                return redirect()->back()->with('success', 'Jadwal bimbingan siswa berhasil disimpan.');
            }
            return redirect()->back()->with('error', 'Data bimbingan siswa tidak ditemukan.');
        }

        if ($bimbinganModel->guru_id != $guruId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $bimbinganModel->update([
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()->back()->with('success', 'Jadwal bimbingan siswa berhasil disimpan.');
    }

    // Materi Pembelajaran Siswa (Tabel Materi)
    public function materi(Request $request)
    {
        $guruId = $this->getGuruId();

        $rawSiswaId = $request->siswa_id;
        $realSiswaId = null;

        if (!empty($rawSiswaId)) {
            try {
                $realSiswaId = \Illuminate\Support\Facades\Crypt::decryptString($rawSiswaId);
            } catch (\Exception $e) {
                $realSiswaId = is_numeric($rawSiswaId) ? $rawSiswaId : null;
            }
        }

        $query = Materi::with(['siswa'])
            ->withCount('target')
            ->where('guru_id', $guruId);

        if ($realSiswaId) {
            $query->where('siswa_id', $realSiswaId);
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                  ->orWhere('deskripsi', 'like', "%{$cari}%")
                  ->orWhereHas('siswa', function ($sq) use ($cari) {
                      $sq->where('nama', 'like', "%{$cari}%");
                  });
            });
        }

        $daftarMateri = $query->latest()->paginate(10)->withQueryString();
        $daftarMateri->getCollection()->transform(function ($t) {
            $t->enc_id = \Illuminate\Support\Facades\Crypt::encryptString($t->id);
            return $t;
        });

        // Ambil daftar siswa bimbingan guru ini
        $siswaIds = \App\Models\Bimbingan::where('guru_id', $guruId)->pluck('siswa_id')->unique();
        $daftarSiswa = Siswa::whereIn('id', $siswaIds)->orderBy('nama')->get();
        if ($daftarSiswa->isEmpty()) {
            $daftarSiswa = Siswa::where('aktif', true)->orderBy('nama')->get();
        }
        $siswaTerpilih = $realSiswaId ? Siswa::find($realSiswaId) : null;

        return Inertia::render('Guru/Materi/Index', [
            'daftar_materi' => $daftarMateri,
            'daftar_siswa' => $daftarSiswa,
            'siswa_terpilih' => $siswaTerpilih,
            'filters' => [
                'cari' => $request->cari,
                'siswa_id' => $rawSiswaId,
            ],
        ]);
    }

    public function storeMateri(Request $request)
    {
        $guruId = $this->getGuruId();

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,selesai',
        ], [
            'siswa_id.required' => 'Pilih siswa terlebih dahulu.',
            'nama.required' => 'Nama materi harus diisi.',
            'status.required' => 'Status materi harus dipilih.',
            'status.in' => 'Status materi harus aktif atau selesai.',
        ]);

        $validated['guru_id'] = $guruId;

        Materi::create($validated);

        return back()->with('success', 'Materi berhasil ditambahkan!');
    }

    public function updateMateri(Request $request, $materi)
    {
        $guruId = $this->getGuruId();

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,selesai',
        ], [
            'siswa_id.required' => 'Pilih siswa terlebih dahulu.',
            'nama.required' => 'Nama materi harus diisi.',
            'status.required' => 'Status materi harus dipilih.',
            'status.in' => 'Status materi harus aktif atau selesai.',
        ]);

        $materiModel = $materi instanceof Materi ? $materi : Materi::find($materi);

        if ($materiModel && $materiModel->guru_id && $materiModel->guru_id != $guruId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($materiModel) {
            $materiModel->update($validated);
        }

        return back()->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroyMateri($materi)
    {
        $guruId = $this->getGuruId();

        $materiId = is_object($materi) ? $materi->id : $materi;
        $materiModel = Materi::withCount('target')->find($materiId);

        if ($materiModel) {
            if ($materiModel->guru_id && $materiModel->guru_id != $guruId) {
                abort(403, 'Akses tidak diizinkan.');
            }

            if ($materiModel->target_count > 0) {
                return back()->with('error', 'Materi tidak dapat dihapus karena masih memiliki target capaian pembelajaran.');
            }

            $materiModel->delete();
        }

        return back()->with('success', 'Materi berhasil dihapus!');
    }

    // Target Capaian Pembelajaran Berdasarkan Materi & Siswa (Tabel Target)
    // URL: guru/target?materi_id=kode enkripsi
    public function target(Request $request)
    {
        $guruId = $this->getGuruId();

        $rawMateriId = $request->materi_id;
        $realMateriId = null;

        if (!empty($rawMateriId)) {
            try {
                $realMateriId = \Illuminate\Support\Facades\Crypt::decryptString($rawMateriId);
            } catch (\Exception $e) {
                $realMateriId = is_numeric($rawMateriId) ? $rawMateriId : null;
            }
        }

        $materiTerpilih = null;
        if ($realMateriId) {
            $materiTerpilih = Materi::with('siswa')->where('guru_id', $guruId)->find($realMateriId);
            if (!$materiTerpilih) {
                $materiTerpilih = Materi::with('siswa')->find($realMateriId);
            }
            if ($materiTerpilih && $materiTerpilih->siswa_id) {
                $materiTerpilih->siswa_id_enc = \Illuminate\Support\Facades\Crypt::encryptString($materiTerpilih->siswa_id);
            }
        }

        $query = Target::with(['siswa', 'materi'])
            ->withCount('kegiatan')
            ->where('guru_id', $guruId);

        if ($realMateriId) {
            $query->where('materi_id', $realMateriId);
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                  ->orWhere('deskripsi', 'like', "%{$cari}%");
            });
        }

        $daftarTarget = $query->latest()->paginate(10)->withQueryString();
        $daftarTarget->getCollection()->transform(function ($t) {
            $t->enc_id = \Illuminate\Support\Facades\Crypt::encryptString($t->id);
            return $t;
        });

        return Inertia::render('Guru/Target/Index', [
            'daftar_target' => $daftarTarget,
            'materi_terpilih' => $materiTerpilih,
            'filters' => [
                'cari' => $request->cari,
                'materi_id' => $rawMateriId,
            ],
        ]);
    }

    public function storeTarget(Request $request)
    {
        $guruId = $this->getGuruId();

        $validated = $request->validate([
            'materi_id' => 'required|exists:materi,id',
            'siswa_id' => 'required|exists:siswa,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,selesai',
        ], [
            'materi_id.required' => 'Materi harus dipilih.',
            'siswa_id.required' => 'Siswa harus dipilih.',
            'nama.required' => 'Nama target capaian harus diisi.',
            'status.required' => 'Status target harus dipilih.',
            'status.in' => 'Status target harus aktif atau selesai.',
        ]);

        $validated['guru_id'] = $guruId;

        Target::create($validated);

        return back()->with('success', 'Target capaian berhasil ditambahkan!');
    }

    public function updateTarget(Request $request, $target)
    {
        $guruId = $this->getGuruId();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,selesai',
        ], [
            'nama.required' => 'Nama target capaian harus diisi.',
            'status.required' => 'Status target harus dipilih.',
            'status.in' => 'Status target harus aktif atau selesai.',
        ]);

        $targetModel = Target::where('guru_id', $guruId)->find($target instanceof Target ? $target->id : $target);
        if (!$targetModel) {
            $targetModel = Target::find($target instanceof Target ? $target->id : $target);
        }

        if ($targetModel && $targetModel->guru_id && $targetModel->guru_id != $guruId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($targetModel) {
            $targetModel->update($validated);
        }

        return back()->with('success', 'Target capaian berhasil diperbarui!');
    }

    public function destroyTarget($target)
    {
        $guruId = $this->getGuruId();

        $targetId = is_object($target) ? $target->id : $target;
        $targetModel = Target::withCount('kegiatan')->where('guru_id', $guruId)->find($targetId);
        if (!$targetModel) {
            $targetModel = Target::withCount('kegiatan')->find($targetId);
        }

        if ($targetModel) {
            if ($targetModel->guru_id && $targetModel->guru_id != $guruId) {
                abort(403, 'Akses tidak diizinkan.');
            }

            if ($targetModel->kegiatan_count > 0) {
                return back()->with('error', 'Target tidak dapat dihapus karena masih memiliki kegiatan pembelajaran.');
            }

            $targetModel->delete();
        }

        return back()->with('success', 'Target capaian berhasil dihapus!');
    }

    // Log Kegiatan Harian
    public function kegiatan(Request $request)
    {
        $guruId = $this->getGuruId();

        $query = Kegiatan::with(['target.siswa', 'target.materi'])
            ->whereHas('target', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            });

        $rawMateriId = $request->materi_id ?? $request->target_id;
        $realTargetId = null;
        if (!empty($rawMateriId)) {
            try {
                $realTargetId = \Illuminate\Support\Facades\Crypt::decryptString($rawMateriId);
            } catch (\Exception $e) {
                $realTargetId = is_numeric($rawMateriId) ? $rawMateriId : null;
            }
        }

        if ($realTargetId) {
            $query->where(function ($q) use ($realTargetId) {
                $q->where('target_id', $realTargetId)
                  ->orWhereHas('target', function ($tq) use ($realTargetId) {
                      $tq->where('materi_id', $realTargetId);
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('keterangan', 'like', "%{$cari}%")
                  ->orWhere('catatan_hasil', 'like', "%{$cari}%")
                  ->orWhereHas('target', function ($tq) use ($cari) {
                      $tq->where('nama', 'like', "%{$cari}%")
                         ->orWhereHas('siswa', function ($sq) use ($cari) {
                             $sq->where('nama', 'like', "%{$cari}%");
                         });
                  });
            });
        }

        $daftarKegiatan = $query->orderBy('tanggal_mulai', 'desc')->paginate(12)->withQueryString();

        $daftarTarget = Target::with(['siswa', 'materi'])
            ->where('guru_id', $guruId)
            ->orderBy('nama')
            ->get();

        $targetTerpilih = $realTargetId ? Target::with(['siswa', 'materi'])->find($realTargetId) : null;
        if ($targetTerpilih) {
            if ($targetTerpilih->siswa_id) {
                $targetTerpilih->siswa_id_enc = \Illuminate\Support\Facades\Crypt::encryptString($targetTerpilih->siswa_id);
            }
            if ($targetTerpilih->materi_id) {
                $targetTerpilih->materi_id_enc = \Illuminate\Support\Facades\Crypt::encryptString($targetTerpilih->materi_id);
            }
        }

        return Inertia::render('Guru/Kegiatan/Index', [
            'daftar_kegiatan' => $daftarKegiatan,
            'daftar_target' => $daftarTarget,
            'target_terpilih' => $targetTerpilih,
            'filters' => [
                'cari' => $request->cari,
                'status' => $request->status,
                'materi_id' => $rawMateriId,
                'target_id' => $realTargetId,
            ],
        ]);
    }

    public function cetakKegiatan(Request $request)
    {
        $guruId = $this->getGuruId();
        $rawMateriId = $request->materi_id ?? $request->target_id;
        $realTargetId = null;

        if (!empty($rawMateriId)) {
            try {
                $realTargetId = \Illuminate\Support\Facades\Crypt::decryptString($rawMateriId);
            } catch (\Exception $e) {
                $realTargetId = is_numeric($rawMateriId) ? $rawMateriId : null;
            }
        }

        $query = Kegiatan::with(['target.siswa', 'target.materi'])
            ->whereHas('target', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            });

        if ($realTargetId) {
            $query->where('target_id', $realTargetId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $daftarKegiatan = $query->orderBy('nomor_urut', 'asc')->orderBy('tanggal_mulai', 'asc')->get();
        $targetTerpilih = $realTargetId ? Target::with(['siswa', 'materi'])->find($realTargetId) : null;
        $guru = auth()->guard('guru')->user() ?? auth()->user();

        return Inertia::render('Guru/Kegiatan/Cetak', [
            'daftar_kegiatan' => $daftarKegiatan,
            'target_terpilih' => $targetTerpilih,
            'guru' => $guru,
        ]);
    }

    public function storeKegiatan(Request $request)
    {
        $guruId = $this->getGuruId();

        $validated = $request->validate([
            'target_id' => 'required|exists:target,id',
            'nomor_urut' => 'nullable|integer',
            'keterangan' => 'required|string',
            'metode' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable',
            'catatan_hasil' => 'nullable|string',
            'kegiatan_selanjutnya' => 'nullable|string',
            'status' => 'required|in:Diulangi,Lanjut,Selesai',
        ], [
            'target_id.required' => 'Pilih target materi.',
            'keterangan.required' => 'Keterangan kegiatan harus diisi.',
            'tanggal_mulai.required' => 'Tanggal pelaksanaan harus diisi.',
            'waktu_mulai.required' => 'Waktu mulai harus diisi.',
        ]);

        $target = Target::findOrFail($validated['target_id']);
        if ($target->guru_id != $guruId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (empty($validated['nomor_urut'])) {
            $lastNomor = Kegiatan::where('target_id', $validated['target_id'])->max('nomor_urut') ?? 0;
            $validated['nomor_urut'] = $lastNomor + 1;
        }

        Kegiatan::create($validated);

        return back()->with('success', 'Catatan kegiatan harian berhasil disimpan!');
    }

    public function updateKegiatan(Request $request, Kegiatan $kegiatan)
    {
        $guruId = $this->getGuruId();
        if ($kegiatan->target->guru_id != $guruId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'target_id' => 'required|exists:target,id',
            'nomor_urut' => 'required|integer',
            'keterangan' => 'required|string',
            'metode' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable',
            'catatan_hasil' => 'nullable|string',
            'kegiatan_selanjutnya' => 'nullable|string',
            'status' => 'required|in:Diulangi,Lanjut,Selesai',
        ], [
            'target_id.required' => 'Pilih target materi.',
            'keterangan.required' => 'Keterangan kegiatan harus diisi.',
            'tanggal_mulai.required' => 'Tanggal pelaksanaan harus diisi.',
            'waktu_mulai.required' => 'Waktu mulai harus diisi.',
        ]);

        $kegiatan->update($validated);

        return back()->with('success', 'Catatan kegiatan harian berhasil diperbarui!');
    }

    public function destroyKegiatan(Kegiatan $kegiatan)
    {
        $guruId = $this->getGuruId();
        if ($kegiatan->target->guru_id != $guruId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $kegiatan->delete();

        return back()->with('success', 'Catatan kegiatan berhasil dihapus!');
    }
}
