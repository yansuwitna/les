<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    jadwal_hari_ini: Array,
    kegiatan_terjadwal: Array,
    target_terbaru: Array,
});

const todayDate = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
}).format(new Date());

const hariIni = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(new Date());

// Filter jadwal rutin & kegiatan berstatus 'Terjadwal'
const jadwalSekarang = computed(() => {
    const list = [];

    // 1. Dari Jadwal Rutin Bimbingan Hari Ini (Tabel Bimbingan aktif)
    if (props.jadwal_hari_ini) {
        props.jadwal_hari_ini
            .filter(j => j.hari && j.hari.toLowerCase() === hariIni.toLowerCase())
            .forEach(j => {
                list.push({
                    id: 'bimbingan_' + j.id,
                    tipe: 'rutin',
                    jam_mulai: j.jam_mulai,
                    jam_selesai: j.jam_selesai,
                    siswa: j.siswa,
                    materi_nama: 'Bimbingan Rutin (' + j.hari + ')',
                    target_id: null,
                });
            });
    }

    // 2. Dari Kegiatan dengan status 'Terjadwal'
    if (props.kegiatan_terjadwal) {
        props.kegiatan_terjadwal.forEach(k => {
            list.push({
                id: 'kegiatan_' + k.id,
                tipe: 'kegiatan_terjadwal',
                tanggal: k.tanggal_mulai,
                jam_mulai: k.waktu_mulai,
                jam_selesai: k.waktu_selesai,
                siswa: k.target?.siswa,
                materi_nama: k.target?.nama || k.target?.materi?.nama || 'Materi Belajar',
                keterangan: k.keterangan,
                target_id: k.target_id,
            });
        });
    }

    return list;
});
</script>

<template>
    <Head :title="'Dashboard Guru - ' + ($page.props.pengaturan?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">
                        Dashboard Guru Pengajar
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">
                        {{ todayDate }} &bull; Selamat mengajar dengan penuh semangat dan keceriaan!
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto space-y-8 pb-12">
            
            <!-- HERO BANNER GURU -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-8 sm:p-10 text-white shadow-xl shadow-indigo-500/15">
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-pink-400/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="max-w-2xl space-y-2.5">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-amber-300 animate-pulse"></span>
                            <span>Ruang Kerja Instruktur / Pengajar</span>
                        </div>
                        <h2 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                            Bimbing Potensi Siswa <br>
                            Raih Prestasi Terhebat
                        </h2>
                        <p class="text-indigo-100 text-xs sm:text-sm leading-relaxed font-medium max-w-xl">
                            Pantau jadwal mengajar harian, rencanakan target kurikulum tiap siswa, serta catat perkembangan jurnal belajar setiap selesai sesi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- STATISTIC CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <div class="bg-white rounded-3xl p-6 border border-indigo-50 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold text-indigo-500 bg-indigo-50 px-2.5 py-1 rounded-full">Siswa Bimbingan</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ stats?.total_siswa || 0 }}</h3>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Siswa Terjadwal Aktif</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-purple-50 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold text-purple-500 bg-purple-50 px-2.5 py-1 rounded-full">Total Jam</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ stats?.total_jadwal || 0 }}</h3>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Sesi Jadwal Pertemuan</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-pink-50 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold text-pink-500 bg-pink-50 px-2.5 py-1 rounded-full">Target Materi</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ stats?.total_target || 0 }}</h3>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Target Kurikulum Siswa</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-emerald-50 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Log Harian</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ stats?.total_kegiatan || 0 }}</h3>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Kegiatan Telah Dicatat</p>
                    </div>
                </div>

            </div>

            <!-- TWO COLUMN SECTION: JADWAL HARI INI & TARGET TERBARU -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- JADWAL HARI INI (Col 1 & 2) -->
                <div class="lg:col-span-2 bg-white rounded-3xl border border-indigo-100/70 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-indigo-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 tracking-tight">Jadwal Mengajar Hari Ini ({{ hariIni }})</h3>
                                <p class="text-xs text-slate-400 font-medium">Daftar siswa yang belajar dengan Anda hari ini</p>
                            </div>
                        </div>

                        <Link :href="route('guru.jadwal.index')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                            Semua Jadwal &rarr;
                        </Link>
                    </div>

                    <!-- List Jadwal Hari Ini -->
                    <div v-if="jadwalSekarang.length > 0" class="space-y-3">
                        <div 
                            v-for="jadwal in jadwalSekarang" 
                            :key="jadwal.id"
                            class="p-4 rounded-2xl bg-indigo-50/40 hover:bg-indigo-50 border border-indigo-100/60 transition-all flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                        >
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-indigo-200 shadow-sm flex flex-col items-center justify-center shrink-0">
                                    <span class="text-[10px] font-black text-indigo-500 uppercase">{{ jadwal.jam_mulai?.slice(0, 5) }}</span>
                                    <span class="text-[9px] font-bold text-slate-400">s/d</span>
                                    <span class="text-[10px] font-black text-indigo-700 uppercase">{{ jadwal.jam_selesai?.slice(0, 5) }}</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-extrabold text-sm text-slate-900">{{ jadwal.siswa?.nama || 'Nama Siswa' }}</h4>
                                        <span 
                                            v-if="jadwal.tipe === 'kegiatan_terjadwal'" 
                                            class="text-[10px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-lg border border-emerald-200"
                                        >
                                            Kegiatan Terjadwal
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium flex items-center gap-2 mt-0.5">
                                        <span class="inline-flex items-center gap-1 font-semibold text-indigo-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                            {{ jadwal.materi_nama }}
                                        </span>
                                        <span v-if="jadwal.keterangan" class="text-slate-400">&bull; {{ jadwal.keterangan }}</span>
                                        <span v-if="jadwal.tanggal" class="text-emerald-600 font-bold">&bull; {{ new Date(jadwal.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <Link 
                                    :href="route('guru.kegiatan.index', jadwal.target_id ? { target_id: jadwal.target_id } : { cari: jadwal.siswa?.nama })"
                                    class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-indigo-600 hover:text-white border border-indigo-200 text-indigo-700 text-xs font-bold transition-all shadow-sm"
                                >
                                    Catat Log
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State Hari Ini -->
                    <div v-else class="text-center py-10 px-4 rounded-2xl border-2 border-dashed border-indigo-100 bg-indigo-50/20">
                        <div class="w-14 h-14 mx-auto rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-700 text-sm">Tidak Ada Jadwal Mengajar Hari Ini</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Anda bebas jadwal untuk hari {{ hariIni }}. Anda bisa memeriksa persiapan materi atau mereview catatan kegiatan sebelumnya.
                        </p>
                    </div>

                </div>

                <!-- TARGET MATERI TERBARU (Col 3) -->
                <div class="bg-white rounded-3xl border border-purple-100/70 p-6 sm:p-8 shadow-sm space-y-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-purple-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-800 tracking-tight">Target Kurikulum</h3>
                                    <p class="text-xs text-slate-400 font-medium">Target capaian materi siswa</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div 
                                v-for="t in target_terbaru" 
                                :key="t.id"
                                class="p-3.5 rounded-2xl bg-purple-50/40 hover:bg-purple-50 border border-purple-100/60 transition-all"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h4 class="font-bold text-xs text-slate-900">{{ t.nama }}</h4>
                                        <p class="text-[11px] text-purple-600 font-semibold mt-0.5">
                                            {{ t.siswa?.nama }} &bull; {{ t.materi?.nama }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">
                                        {{ t.kegiatan?.length || 0 }} Log
                                    </span>
                                </div>
                            </div>

                            <div v-if="!target_terbaru || target_terbaru.length === 0" class="text-center py-6 text-xs text-slate-400">
                                Belum ada target materi yang ditambahkan.
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-purple-50">
                        <Link 
                            :href="route('guru.target.index')" 
                            class="w-full py-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center gap-2 transition-colors"
                        >
                            <span>Kelola Semua Target Materi</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </Link>
                    </div>
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>

