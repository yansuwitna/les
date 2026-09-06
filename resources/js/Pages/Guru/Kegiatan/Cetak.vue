<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    daftar_kegiatan: Array,
    target_terpilih: Object,
    guru: Object,
});

onMounted(() => {
    setTimeout(() => {
        window.print();
    }, 400);
});
</script>

<template>
    <Head :title="'Cetak Log Kegiatan - ' + (target_terpilih?.siswa?.nama || 'Siswa')" />

    <div class="min-h-screen bg-white text-slate-900 font-sans p-6 sm:p-10 max-w-5xl mx-auto">
        
        <!-- Action Bar (Hanya tampil di layar, tidak ikut tercetak) -->
        <div class="print:hidden mb-8 flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-200">
            <div class="flex items-center gap-2 text-slate-600 text-xs font-semibold">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Halaman ini khusus format cetak. Gunakan tombol di samping untuk mencetak ulang atau menutup tab.</span>
            </div>
            <div class="flex items-center gap-2">
                <button
                    onclick="window.print()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Sekarang</span>
                </button>
                <button
                    onclick="window.close()"
                    class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition-all"
                >
                    Tutup Halaman
                </button>
            </div>
        </div>

        <!-- HEADER DOKUMEN CETAK -->
        <div class="border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 uppercase">
                        {{ $page.props.pengaturan?.nama_les || 'LEMBAGA BIMBINGAN BELAJAR' }}
                    </h1>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        {{ $page.props.pengaturan?.alamat || 'Laporan Jurnal & Catatan Capaian Pembelajaran Siswa' }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 text-xs font-extrabold rounded-md uppercase tracking-wider">
                        Log Kegiatan Pembelajaran
                    </span>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Dicetak: {{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- IDENTITAS SISWA & MATERI BELAJAR -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50/80 p-4 rounded-xl border border-slate-200 text-xs mb-6">
            <div class="space-y-1.5">
                <div class="flex">
                    <span class="w-32 text-slate-500 font-medium">Nama Siswa</span>
                    <span class="font-bold text-slate-900">: {{ target_terpilih?.siswa?.nama || '-' }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 font-medium">Nomor Siswa</span>
                    <span class="font-semibold text-slate-700">: {{ target_terpilih?.siswa?.nomor_siswa || '-' }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 font-medium">Orang Tua / Wali</span>
                    <span class="text-slate-700">: {{ target_terpilih?.siswa?.nama_wali || '-' }}</span>
                </div>
            </div>

            <div class="space-y-1.5">
                <div class="flex">
                    <span class="w-32 text-slate-500 font-medium">Nama Materi</span>
                    <span class="font-bold text-slate-900">: {{ target_terpilih?.nama || '-' }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 font-medium">Deskripsi Capaian</span>
                    <span class="text-slate-700">: {{ target_terpilih?.deskripsi || '-' }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-500 font-medium">Guru Pengajar</span>
                    <span class="font-bold text-slate-900">: {{ guru?.nama || guru?.name || '-' }}</span>
                </div>
            </div>
        </div>

        <!-- TABEL RINCIAN LOG KEGIATAN -->
        <table class="w-full text-left text-xs border-collapse border border-slate-300 mb-8">
            <thead>
                <tr class="bg-slate-100 text-[11px] font-black uppercase text-slate-700 border-b border-slate-300">
                    <th class="py-2.5 px-3 border-r border-slate-300 w-12 text-center">Sesi</th>
                    <th class="py-2.5 px-3 border-r border-slate-300 w-28">Tanggal & Jam</th>
                    <th class="py-2.5 px-3 border-r border-slate-300">Materi / Topik Kegiatan</th>
                    <th class="py-2.5 px-3 border-r border-slate-300 w-28">Metode</th>
                    <th class="py-2.5 px-3 border-r border-slate-300">Catatan Hasil & Rencana</th>
                    <th class="py-2.5 px-3 w-20 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-300 text-slate-700">
                <tr v-for="k in daftar_kegiatan" :key="k.id" class="align-top">
                    <!-- Sesi -->
                    <td class="py-2.5 px-3 border-r border-slate-300 text-center font-bold">
                        #{{ k.nomor_urut }}
                    </td>

                    <!-- Tanggal -->
                    <td class="py-2.5 px-3 border-r border-slate-300 whitespace-nowrap">
                        <p class="font-bold text-slate-900">
                            {{ new Date(k.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </p>
                        <p class="text-[10px] text-slate-500 font-mono mt-0.5">
                            {{ k.waktu_mulai?.slice(0, 5) }} - {{ k.waktu_selesai?.slice(0, 5) }}
                        </p>
                    </td>

                    <!-- Topik -->
                    <td class="py-2.5 px-3 border-r border-slate-300">
                        <p class="font-bold text-slate-900">{{ k.keterangan }}</p>
                    </td>

                    <!-- Metode -->
                    <td class="py-2.5 px-3 border-r border-slate-300">
                        {{ k.metode || '-' }}
                    </td>

                    <!-- Catatan Hasil -->
                    <td class="py-2.5 px-3 border-r border-slate-300">
                        <div v-if="k.catatan_hasil" class="mb-1">
                            <span class="font-bold text-slate-800">Evaluasi:</span> {{ k.catatan_hasil }}
                        </div>
                        <div v-if="k.kegiatan_selanjutnya" class="text-[11px] text-slate-600">
                            <span class="font-bold text-slate-800">Rencana:</span> {{ k.kegiatan_selanjutnya }}
                        </div>
                        <span v-if="!k.catatan_hasil && !k.kegiatan_selanjutnya" class="text-slate-400">-</span>
                    </td>

                    <!-- Status -->
                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                        <span class="font-bold text-slate-900">{{ k.status }}</span>
                    </td>
                </tr>

                <tr v-if="!daftar_kegiatan || daftar_kegiatan.length === 0">
                    <td colspan="6" class="py-8 text-center text-slate-400 font-medium">
                        Tidak ada catatan kegiatan.
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <div class="grid grid-cols-2 gap-8 text-xs text-center mt-12 break-inside-avoid">
            <div>
                <p class="text-slate-500 mb-16">Mengetahui,<br>Orang Tua / Wali Siswa</p>
                <p class="font-bold text-slate-900 border-t border-slate-400 pt-1.5 inline-block min-w-[160px]">
                    ( {{ target_terpilih?.siswa?.nama_wali || '................................' }} )
                </p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">
                    Guru Pembimbing,
                </p>
                <p class="font-bold text-slate-900 border-t border-slate-400 pt-1.5 inline-block min-w-[160px]">
                    ( {{ guru?.nama || guru?.name || '................................' }} )
                </p>
            </div>
        </div>

    </div>
</template>

<style>
@media print {
    body {
        background-color: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    @page {
        margin: 1.5cm;
        size: A4 portrait;
    }
}
</style>