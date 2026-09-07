<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    siswa: Object,
    pembimbing: Array,
    daftar_jadwal: Array,
    target_list: Array,
    kegiatan_terbaru: Array,
    stats: Object,
});

const activeTab = ref('ringkasan'); // 'ringkasan', 'jadwal', 'target', 'kegiatan'

const todayDate = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
}).format(new Date());

const hariIni = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(new Date());

const formatJam = (jam) => {
    if (!jam) return '00:00';
    return jam.substring(0, 5);
};

const formatTanggal = (tgl) => {
    if (!tgl) return '-';
    try {
        const d = new Date(tgl);
        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        }).format(d);
    } catch (e) {
        return tgl;
    }
};

// Jadwal untuk hari ini
const jadwalHariIni = computed(() => {
    if (!props.daftar_jadwal) return [];
    return props.daftar_jadwal.filter(
        j => j.hari && j.hari.toLowerCase() === hariIni.toLowerCase() && j.aktif
    );
});
</script>

<template>
    <Head :title="'Dashboard Wali - ' + ($page.props.pengaturan?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">
                            Dashboard Pemantauan Siswa
                        </h1>
                        <p class="text-xs text-slate-500 font-medium">
                            {{ todayDate }} &bull; Pantau perkembangan dan jadwal belajar putra/putri Anda
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto space-y-8 pb-14">
            
            <!-- HERO CARD SISWA & WALI -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-600 via-rose-500 to-indigo-600 p-6 sm:p-8 text-white shadow-xl shadow-pink-500/15">
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-indigo-400/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <!-- Data Siswa & Wali -->
                    <div class="flex items-center gap-5">
                        <!-- Foto Siswa -->
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/20 backdrop-blur-md p-1 border border-white/30 shrink-0 shadow-lg overflow-hidden flex items-center justify-center">
                            <img 
                                v-if="siswa?.foto_url" 
                                :src="siswa.foto_url" 
                                :alt="siswa?.nama" 
                                class="w-full h-full object-cover rounded-2xl"
                            />
                            <div 
                                v-else 
                                class="w-full h-full rounded-2xl bg-gradient-to-tr from-white/30 to-white/10 flex items-center justify-center text-white text-3xl font-black"
                            >
                                {{ (siswa?.nama || 'S').charAt(0) }}
                            </div>
                        </div>

                        <!-- Identitas -->
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-md text-white text-[11px] font-bold">
                                <span>Portal Wali Murid</span>
                            </div>
                            <h2 class="text-xl sm:text-3xl font-black tracking-tight leading-tight">
                                {{ siswa?.nama_wali || 'Wali Murid' }}
                            </h2>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-pink-100 font-medium">
                                <span class="flex items-center gap-1 font-mono">
                                    <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                    No: {{ siswa?.nomor_siswa || '-' }}
                                </span>
                                <span v-if="siswa?.nis" class="flex items-center gap-1 font-mono">
                                    NIS: {{ siswa.nis }}
                                </span>
                                <span v-if="siswa?.alamat" class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ siswa.alamat }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Cepat Progress -->
                    <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/20 flex flex-col justify-center min-w-[220px]">
                        <div class="flex items-center justify-between text-xs font-bold mb-2">
                            <span>Kemajuan Target Belajar</span>
                            <span class="text-amber-300 font-extrabold text-sm">{{ stats?.progress_persen || 0 }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-black/20 rounded-full overflow-hidden p-0.5">
                            <div 
                                class="h-full bg-gradient-to-r from-amber-300 to-emerald-300 rounded-full transition-all duration-500 shadow-sm"
                                :style="{ width: (stats?.progress_persen || 0) + '%' }"
                            ></div>
                        </div>
                        <p class="text-[11px] text-pink-100 font-medium mt-2">
                            {{ stats?.target_selesai || 0 }} dari {{ stats?.total_target || 0 }} target materi terselesaikan
                        </p>
                    </div>
                </div>
            </div>

            <!-- STATISTIC CARDS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <!-- Total Jadwal -->
                <div class="bg-white rounded-3xl p-5 border border-pink-100/80 shadow-xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                            Jadwal
                        </span>
                    </div>
                    <p class="text-2xl font-black text-slate-900">{{ stats?.total_jadwal || 0 }}</p>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Sesi Kursus per Minggu</p>
                </div>

                <!-- Capaian Target -->
                <div class="bg-white rounded-3xl p-5 border border-pink-100/80 shadow-xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                            Target
                        </span>
                    </div>
                    <p class="text-2xl font-black text-slate-900">
                        {{ stats?.target_selesai || 0 }} <span class="text-xs font-bold text-slate-400 font-normal">/ {{ stats?.total_target || 0 }}</span>
                    </p>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Target Materi Tuntas</p>
                </div>

                <!-- Log Pertemuan Belajar -->
                <div class="bg-white rounded-3xl p-5 border border-pink-100/80 shadow-xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-pink-600 bg-pink-50 px-2 py-0.5 rounded-full">
                            Kegiatan
                        </span>
                    </div>
                    <p class="text-2xl font-black text-slate-900">{{ stats?.total_kegiatan || 0 }}</p>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Catatan Pertemuan Belajar</p>
                </div>

                <!-- Reward & Bintang -->
                <div class="bg-white rounded-3xl p-5 border border-pink-100/80 shadow-xs hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                            Prestasi
                        </span>
                    </div>
                    <p class="text-2xl font-black text-slate-900">{{ stats?.total_bintang || 0 }}</p>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Bintang Apresiasi Diraih</p>
                </div>
            </div>

            <!-- GURU PEMBIMBING & JADWAL HARI INI -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Card Guru Pembimbing -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Guru Pembimbing
                        </h3>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Instruktur</span>
                    </div>

                    <div v-if="pembimbing && pembimbing.length > 0" class="space-y-3">
                        <div 
                            v-for="guru in pembimbing" 
                            :key="guru.id" 
                            class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-indigo-50/40 transition-all"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm shadow-xs overflow-hidden shrink-0">
                                    <img v-if="guru.foto_url" :src="guru.foto_url" :alt="guru.nama" class="w-full h-full object-cover" />
                                    <span v-else>{{ (guru.nama || 'G').charAt(0) }}</span>
                                </div>
                                <div>
                                    <p class="font-bold text-xs text-slate-800">{{ guru.nama }}</p>
                                    <p class="text-[10px] font-mono text-slate-400">NIK: {{ guru.nik || '-' }}</p>
                                </div>
                            </div>

                            <a 
                                v-if="guru.no_hp" 
                                :href="'https://wa.me/' + guru.no_hp.replace(/[^0-9]/g, '')" 
                                target="_blank"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 text-[11px] font-bold transition-all shadow-xs"
                                title="Hubungi Guru via WhatsApp"
                            >
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                                </svg>
                                <span>Kontak</span>
                            </a>
                        </div>
                    </div>
                    <div v-else class="text-center py-6 text-slate-400 text-xs">
                        Belum ada guru pembimbing yang dialokasikan.
                    </div>
                </div>

                <!-- Card Jadwal Hari Ini / Terdekat -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            Jadwal Belajar Hari Ini ({{ hariIni }})
                        </h3>
                        <span class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full">
                            {{ jadwalHariIni.length }} Sesi
                        </span>
                    </div>

                    <div v-if="jadwalHariIni.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div 
                            v-for="j in jadwalHariIni" 
                            :key="j.id"
                            class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/60 space-y-2"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-xs text-amber-900 font-mono bg-white px-2 py-0.5 rounded-lg border border-amber-200/50 shadow-2xs">
                                    {{ formatJam(j.jam_mulai) }} - {{ formatJam(j.jam_selesai) }} WIB
                                </span>
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            </div>
                            <p class="font-bold text-xs text-slate-900">
                                Bersama: {{ j.guru?.nama || 'Guru Pembimbing' }}
                            </p>
                            <p v-if="j.keterangan" class="text-[11px] text-slate-500">
                                Catatan: {{ j.keterangan }}
                            </p>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-slate-400 text-xs bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        Tidak ada sesi jadwal bimbingan pada hari {{ hariIni }}.
                    </div>
                </div>
            </div>

            <!-- TABS DETAIL PEMANTAUAN -->
            <div class="space-y-5">
                <!-- Tab Buttons Navigation -->
                <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto">
                    <button
                        @click="activeTab = 'ringkasan'"
                        class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0"
                        :class="activeTab === 'ringkasan' ? 'bg-pink-600 text-white shadow-md shadow-pink-500/20' : 'bg-white hover:bg-pink-50 text-slate-600 border border-slate-200/70'"
                    >
                        Semua Jadwal Mingguan ({{ daftar_jadwal?.length || 0 }})
                    </button>
                    <button
                        @click="activeTab = 'target'"
                        class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0"
                        :class="activeTab === 'target' ? 'bg-pink-600 text-white shadow-md shadow-pink-500/20' : 'bg-white hover:bg-pink-50 text-slate-600 border border-slate-200/70'"
                    >
                        Target & Materi Belajar ({{ target_list?.length || 0 }})
                    </button>
                    <button
                        @click="activeTab = 'kegiatan'"
                        class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0"
                        :class="activeTab === 'kegiatan' ? 'bg-pink-600 text-white shadow-md shadow-pink-500/20' : 'bg-white hover:bg-pink-50 text-slate-600 border border-slate-200/70'"
                    >
                        Catatan Kegiatan Guru ({{ kegiatan_terbaru?.length || 0 }})
                    </button>
                </div>

                <!-- TAB 1: JADWAL BELAJAR MINGGUAN -->
                <div v-if="activeTab === 'ringkasan'" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 tracking-tight">Jadwal Kursus Mingguan Siswa</h3>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Daftar sesi belajar bimbingan siswa per minggu</p>
                        </div>
                        <span class="text-xs font-bold text-pink-600 bg-pink-50 px-3 py-1 rounded-full">
                            Total: {{ daftar_jadwal?.length || 0 }} Jadwal
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-black uppercase tracking-wider text-slate-400">
                                    <th class="py-3.5 px-6">Hari</th>
                                    <th class="py-3.5 px-6">Waktu Sesi</th>
                                    <th class="py-3.5 px-6">Guru Pengajar</th>
                                    <th class="py-3.5 px-6">Keterangan</th>
                                    <th class="py-3.5 px-6 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-600">
                                <tr v-for="j in daftar_jadwal" :key="j.id" class="hover:bg-pink-50/30 transition-colors">
                                    <td class="py-4 px-6 font-extrabold text-slate-900 text-xs">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            <span>{{ j.hari }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-amber-50 text-amber-900 border border-amber-200/70 font-mono text-xs font-bold shadow-2xs">
                                            {{ formatJam(j.jam_mulai) }} - {{ formatJam(j.jam_selesai) }} WIB
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                                {{ (j.guru?.nama || 'G').charAt(0) }}
                                            </div>
                                            <span class="font-bold text-slate-800 text-xs">{{ j.guru?.nama || 'Belum Ditentukan' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 text-xs">
                                        {{ j.keterangan || '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span 
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border"
                                            :class="j.aktif ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full" :class="j.aktif ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                            {{ j.aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                </tr>

                                <tr v-if="!daftar_jadwal || daftar_jadwal.length === 0">
                                    <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                        Belum ada jadwal bimbingan yang tercatat.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: TARGET & MATERI BELAJAR -->
                <div v-if="activeTab === 'target'" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 tracking-tight">Rencana Target & Materi Belajar</h3>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Target kompetensi dan kurikulum materi yang diberikan instruktur</p>
                        </div>
                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                            Total: {{ target_list?.length || 0 }} Target
                        </span>
                    </div>

                    <div class="p-6">
                        <div v-if="target_list && target_list.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div 
                                v-for="t in target_list" 
                                :key="t.id"
                                class="p-5 rounded-3xl border transition-all space-y-3"
                                :class="t.status === 'selesai' ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50/70 border-slate-200/70 hover:border-indigo-300'"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-indigo-100/80 text-indigo-800 text-[10px] font-bold uppercase tracking-wider mb-1">
                                            {{ t.materi?.nama || 'Materi Pembelajaran' }}
                                        </span>
                                        <h4 class="font-black text-sm text-slate-900">{{ t.nama }}</h4>
                                    </div>
                                    <span 
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border shrink-0"
                                        :class="t.status === 'selesai' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-amber-100 text-amber-800 border-amber-300'"
                                    >
                                        {{ t.status === 'selesai' ? 'Tuntas' : 'Sedang Berjalan' }}
                                    </span>
                                </div>

                                <p v-if="t.deskripsi" class="text-xs text-slate-600 leading-relaxed font-medium">
                                    {{ t.deskripsi }}
                                </p>

                                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-200/60 font-medium">
                                    <span>Guru: {{ t.guru?.nama || '-' }}</span>
                                    <span>Ditetapkan: {{ formatTanggal(t.created_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12 text-slate-400 text-xs">
                            Belum ada target pembelajaran yang didaftarkan oleh guru.
                        </div>
                    </div>
                </div>

                <!-- TAB 3: CATATAN KEGIATAN GURU -->
                <div v-if="activeTab === 'kegiatan'" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 tracking-tight">Catatan Harian Belajar (Jurnal Guru)</h3>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Hasil evaluasi, teknik belajar, dan catatan perkembangan dari instruktur</p>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                            {{ kegiatan_terbaru?.length || 0 }} Catatan Sesi
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <div 
                            v-for="k in kegiatan_terbaru" 
                            :key="k.id"
                            class="p-6 hover:bg-slate-50/60 transition-colors space-y-3"
                        >
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-pink-100 text-pink-700 flex items-center justify-center font-bold text-xs font-mono">
                                        #{{ k.nomor_urut || 1 }}
                                    </span>
                                    <div>
                                        <h4 class="font-extrabold text-sm text-slate-900">{{ k.keterangan }}</h4>
                                        <p class="text-[11px] text-indigo-600 font-bold">
                                            Target: {{ k.target?.nama || '-' }} ({{ k.target?.materi?.nama || '-' }})
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 text-xs">
                                    <span class="font-mono text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded-lg">
                                        {{ formatTanggal(k.tanggal_mulai) }}
                                    </span>
                                    <span 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 border-emerald-200': k.status === 'Selesai',
                                            'bg-blue-50 text-blue-700 border-blue-200': k.status === 'Lanjut',
                                            'bg-amber-50 text-amber-700 border-amber-200': k.status === 'Diulangi'
                                        }"
                                    >
                                        Status: {{ k.status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Detail Catatan Hasil & Rencana Selanjutnya -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                                <div v-if="k.catatan_hasil" class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100 text-xs">
                                    <p class="font-bold text-emerald-900 mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Catatan Hasil Belajar:
                                    </p>
                                    <p class="text-slate-700 leading-relaxed font-medium">{{ k.catatan_hasil }}</p>
                                </div>

                                <div v-if="k.kegiatan_selanjutnya" class="p-3.5 rounded-2xl bg-blue-50/50 border border-blue-100 text-xs">
                                    <p class="font-bold text-blue-900 mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                        Rencana Sesi Selanjutnya:
                                    </p>
                                    <p class="text-slate-700 leading-relaxed font-medium">{{ k.kegiatan_selanjutnya }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="!kegiatan_terbaru || kegiatan_terbaru.length === 0" class="text-center py-12 text-slate-400 text-xs">
                            Belum ada catatan kegiatan belajar yang diinput oleh guru.
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
