<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Target;
use App\Models\Kegiatan;
use App\Models\Bintang;
use App\Models\Sertifikat;
use App\Models\Bimbingan;

class DashboardController extends Controller
{
    private function getSiswa()
    {
        $user = Auth::guard('siswa')->user()
            ?? Auth::guard('ortu')->user()
            ?? Auth::guard('web')->user()
            ?? Auth::user();

        if ($user instanceof Siswa) {
            return $user;
        }

        if ($user && isset($user->id)) {
            return Siswa::find($user->id);
        }

        return Siswa::first();
    }

    public function index(Request $request)
    {
        $siswa = $this->getSiswa();

        if (!$siswa) {
            return redirect('/masuk');
        }

        // Ambil data pembimbing (Guru) dari tabel bimbingan
        $pembimbing = Bimbingan::with('guru')
            ->where('siswa_id', $siswa->id)
            ->where('aktif', true)
            ->get()
            ->pluck('guru')
            ->filter()
            ->unique('id')
            ->values();

        // Ambil jadwal bimbingan belajar siswa dari tabel jadwal
        $daftarJadwal = Jadwal::with('guru')
            ->where('siswa_id', $siswa->id)
            ->where('aktif', true)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get();

        // Ambil daftar capaian target & materi siswa
        $targetList = Target::with(['materi', 'guru'])
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        // Ambil log kegiatan harian siswa (riwayat belajar terbaru)
        $kegiatanList = Kegiatan::with(['target.materi', 'target.guru'])
            ->whereHas('target', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->limit(10)
            ->get();

        // Statistik kemajuan belajar
        $totalTarget = $targetList->count();
        $targetSelesai = $targetList->where('status', 'selesai')->count();
        $targetProses = $totalTarget - $targetSelesai;
        $totalKegiatan = Kegiatan::whereHas('target', function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        })->count();
        $totalBintang = Bintang::where('siswa_id', $siswa->id)->count();
        $totalSertifikat = Sertifikat::where('siswa_id', $siswa->id)->count();
        $progressPersen = $totalTarget > 0 ? round(($targetSelesai / $totalTarget) * 100) : 0;

        return Inertia::render('Ortu/Dashboard', [
            'siswa' => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nomor_siswa' => $siswa->nomor_siswa,
                'nis' => $siswa->nis,
                'nama_wali' => $siswa->nama_wali,
                'no_hp_wali' => $siswa->no_hp_wali,
                'no_hp' => $siswa->no_hp,
                'alamat' => $siswa->alamat,
                'foto_url' => $siswa->foto_url,
                'aktif' => (bool)$siswa->aktif,
            ],
            'pembimbing' => $pembimbing,
            'daftar_jadwal' => $daftarJadwal,
            'target_list' => $targetList,
            'kegiatan_terbaru' => $kegiatanList,
            'stats' => [
                'total_jadwal' => $daftarJadwal->count(),
                'total_target' => $totalTarget,
                'target_selesai' => $targetSelesai,
                'target_proses' => $targetProses,
                'total_kegiatan' => $totalKegiatan,
                'total_bintang' => $totalBintang,
                'total_sertifikat' => $totalSertifikat,
                'progress_persen' => $progressPersen,
            ],
        ]);
    }
}
