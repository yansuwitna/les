<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    settings: Object,
    testimonials: Array,
    schedules: Array,
    kegiatan_terjadwal: Array,
    teachers_count: Number,
    students_count: Number,
});

const page = usePage();

import { ref } from 'vue';

const selectedDay = ref('Semua');
const daftarHari = ['Semua', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

const semuaJadwalGabungan = computed(() => {
    const hasil = [];

    // 1. Dari Jadwal Rutin
    if (props.schedules) {
        props.schedules.forEach(s => {
            hasil.push({
                id: 'jadwal_' + s.id,
                tipe: 'rutin',
                hari: s.hari || s.day || 'Senin',
                jam_mulai: s.jam_mulai || s.start_time,
                jam_selesai: s.jam_selesai || s.end_time,
                materi: s.materi?.nama || s.material || 'Materi Belajar',
                guru: s.guru?.nama || s.teacher?.name || 'Guru Les',
                siswa: s.siswa?.nama || s.student?.name || 'Siswa',
                tanggal: null,
            });
        });
    }

    // 2. Dari Kegiatan Terjadwal
    if (props.kegiatan_terjadwal) {
        props.kegiatan_terjadwal.forEach(k => {
            const hariNama = k.tanggal_mulai ? new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(new Date(k.tanggal_mulai)) : 'Senin';
            hasil.push({
                id: 'kegiatan_' + k.id,
                tipe: 'kegiatan_terjadwal',
                hari: hariNama,
                jam_mulai: k.waktu_mulai,
                jam_selesai: k.waktu_selesai,
                materi: k.target?.nama || k.target?.materi?.nama || k.keterangan || 'Materi Pembelajaran',
                guru: k.target?.guru?.nama || k.target?.guru?.name || 'Guru Les',
                siswa: k.target?.siswa?.nama || 'Siswa',
                tanggal: k.tanggal_mulai,
                keterangan: k.keterangan,
            });
        });
    }

    if (selectedDay.value === 'Semua') {
        return hasil;
    }

    return hasil.filter(item => item.hari?.toLowerCase() === selectedDay.value.toLowerCase());
});

const dashboardUrl = computed(() => {
    const user = page.props.auth?.user;
    if (!user) return '/masuk';
    const role = user.peran || user.role;
    if (role === 'admin') return '/admin';
    if (role === 'guru') return '/guru';
    if (role === 'ortu' || role === 'siswa') return '/ortu';
    return '/dashboard';
});
</script>

<template>
    <Head :title="(settings?.nama_les || settings?.les_name || 'Les Ceria') + ' - Bimbingan Belajar Modern, Ramah & Berprestasi'" />

    <div class="min-h-screen bg-[#FDFDFE] text-slate-800 font-sans selection:bg-purple-200 selection:text-purple-900 relative overflow-hidden">
        
        <!-- Cheerful Ambient Background Gradients -->
        <div class="absolute -top-32 -left-32 w-[36rem] h-[36rem] bg-purple-200/40 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute top-96 -right-32 w-[36rem] h-[36rem] bg-pink-200/40 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute top-[80rem] left-1/4 w-[30rem] h-[30rem] bg-amber-100/50 rounded-full blur-[120px] pointer-events-none"></div>

        <!-- STICKY BRIGHT NAVBAR -->
        <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-purple-100/80 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                
                <!-- Brand Logo -->
                <Link href="/" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-purple-600 via-pink-500 to-amber-400 p-[2px] shadow-lg shadow-purple-500/20 group-hover:scale-105 transition-transform duration-300">
                        <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center overflow-hidden p-1">
                            <img v-if="settings?.logo_url" :src="settings.logo_url" :alt="settings?.nama_les || 'Logo'" class="w-full h-full object-contain" />
                            <svg v-else class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-black text-xl tracking-tight text-slate-900 group-hover:text-purple-600 transition-colors">
                                {{ settings?.nama_les || settings?.les_name || 'Les Ceria' }}
                            </span>
                            <span class="w-2 h-2 rounded-full bg-pink-500 animate-pulse"></span>
                        </div>
                        <span class="text-[9px] uppercase font-extrabold tracking-widest text-purple-600">Bimbingan Belajar Unggul</span>
                    </div>
                </Link>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-xs font-bold text-slate-600">
                    <a href="#keunggulan" class="hover:text-purple-600 transition-colors">Keunggulan</a>
                    <a href="#jadwal" class="hover:text-purple-600 transition-colors">Jadwal Belajar</a>
                    <a href="#testimoni" class="hover:text-purple-600 transition-colors">Testimoni Wali</a>
                    <a href="#kontak" class="hover:text-purple-600 transition-colors">Lokasi & Kontak</a>
                </nav>

                <!-- CTA Portal Masuk Button -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="dashboardUrl"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-md shadow-purple-500/20 transition-all transform hover:-translate-y-0.5"
                    >
                        <span>Ke Dashboard ({{ $page.props.auth.user.nama || $page.props.auth.user.name }})</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>

                    <Link
                        v-else
                        href="/masuk"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-md shadow-purple-500/20 transition-all transform hover:-translate-y-0.5"
                    >
                        <span>Portal Masuk</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>
                </div>

            </div>
        </header>

        <!-- HERO SECTION -->
        <section class="relative pt-12 pb-20 lg:pt-20 lg:pb-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    
                    <!-- Left Hero Text -->
                    <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-100 text-purple-700 text-xs font-extrabold shadow-sm">
                            <svg class="w-3.5 h-3.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span>Cara Baru Belajar Menyenangkan & Berprestasi</span>
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15]">
                            Bermain, Belajar & <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500">
                                Raih Prestasi Hebat
                            </span>
                            Bersama Kami!
                        </h1>

                        <p class="text-slate-600 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                            Pendampingan belajar privat dan semi-privat yang ramah anak, terarah dengan target kurikulum jelas, serta dilengkapi laporan harian langsung kepada orang tua siswa.
                        </p>

                        <!-- CTA Actions -->
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                            <a 
                                href="#jadwal" 
                                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl text-xs font-black text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-xl shadow-purple-500/25 transition-all transform hover:-translate-y-0.5 text-center"
                            >
                                Lihat Jadwal Belajar &rarr;
                            </a>
                            <Link 
                                v-if="$page.props.auth?.user"
                                :href="dashboardUrl" 
                                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl text-xs font-black text-slate-700 hover:text-purple-700 bg-white hover:bg-purple-50 border border-slate-200 transition-all text-center shadow-sm"
                            >
                                Buka Dashboard
                            </Link>
                            <Link 
                                v-else
                                href="/masuk" 
                                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl text-xs font-black text-slate-700 hover:text-purple-700 bg-white hover:bg-purple-50 border border-slate-200 transition-all text-center shadow-sm"
                            >
                                Masuk ke Portal Siswa
                            </Link>
                        </div>

                        <!-- Cheerful Stats Counters -->
                        <div class="pt-8 border-t border-purple-100 grid grid-cols-3 gap-4 max-w-lg mx-auto lg:mx-0">
                            <div>
                                <p class="text-3xl font-black text-purple-600">{{ teachers_count || 12 }}</p>
                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold mt-0.5">Guru Ramah</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-pink-600">{{ students_count || 150 }}+</p>
                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold mt-0.5">Siswa Ceria</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-amber-500">4.9/5</p>
                                <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold mt-0.5">Rating Orang Tua</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Hero Illustration -->
                    <div class="lg:col-span-5 relative">
                        <div class="relative rounded-3xl p-3 bg-gradient-to-tr from-purple-200 via-pink-100 to-amber-100 shadow-2xl border border-white">
                            <div class="relative rounded-2xl overflow-hidden aspect-[4/3] bg-slate-100">
                                <img 
                                    :src="settings?.slide_url || 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=2022&auto=format&fit=crop'" 
                                    :alt="settings?.nama_les || 'Anak anak belajar ceria'" 
                                    class="w-full h-full object-cover"
                                />
                            </div>

                            <!-- Cheerful Floating Badge 1 -->
                            <div class="absolute -bottom-6 -left-6 p-4 rounded-3xl bg-white border border-purple-100 shadow-xl flex items-center gap-3.5 max-w-xs">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800">Apresiasi Bintang</p>
                                    <p class="text-[11px] text-slate-500 font-medium">Anak termotivasi raih target</p>
                                </div>
                            </div>

                            <!-- Cheerful Floating Badge 2 -->
                            <div class="absolute -top-6 -right-6 p-3.5 rounded-2xl bg-white border border-pink-100 shadow-xl hidden sm:flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-extrabold text-slate-700">Kelas Aktif Berjalan</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- KEUNGGULAN SECTION -->
        <section id="keunggulan" class="py-20 bg-gradient-to-b from-white via-purple-50/30 to-white border-y border-purple-100/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-purple-100 text-purple-700 text-xs font-black">
                        <svg class="w-3.5 h-3.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span>Keistimewaan Belajar</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        Mengapa {{ settings?.nama_les || settings?.les_name || 'Les Ceria' }} Jadi Pilihan Terbaik?
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base font-medium">
                        Kami menciptakan lingkungan belajar yang menggembirakan, tidak membosankan, dan terstruktur demi perkembangan buah hati Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Card 1 -->
                    <div class="p-8 rounded-3xl bg-white border border-purple-100 hover:border-purple-300 shadow-sm hover:shadow-xl transition-all duration-300 space-y-4 group">
                        <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 group-hover:text-purple-600 transition-colors">Target Belajar Adaptif</h3>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                            Setiap anak menerima target materi yang disesuaikan secara personal, memastikan materi terserap maksimal tanpa membuat anak tertekan.
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="p-8 rounded-3xl bg-white border border-pink-100 hover:border-pink-300 shadow-sm hover:shadow-xl transition-all duration-300 space-y-4 group">
                        <div class="w-14 h-14 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center group-hover:bg-pink-500 group-hover:text-white transition-all duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 group-hover:text-pink-600 transition-colors">Laporan Transparan Wali Murid</h3>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                            Orang tua memiliki akses langsung ke portal untuk memantau log kegiatan, tata cara teknik ajar, serta catatan hasil belajar setiap pertemuan.
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div class="p-8 rounded-3xl bg-white border border-amber-100 hover:border-amber-300 shadow-sm hover:shadow-xl transition-all duration-300 space-y-4 group">
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 group-hover:text-amber-600 transition-colors">Bintang & Sertifikat Prestasi</h3>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                            Setiap keberhasilan pencapaian materi dianugerahi bintang kebanggaan dan sertifikat resmi cetak sebagai bukti penguasaan materi anak.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- JADWAL BELAJAR SECTION -->
        <section id="jadwal" class="py-20 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold mb-2">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Jadwal Kelas</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                            Jadwal Pembelajaran Pekan Ini
                        </h2>
                        <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">
                            Informasi waktu pertemuan belajar aktif antara siswa dan pengajar.
                        </p>
                    </div>

                    <Link 
                        href="/masuk" 
                        class="text-xs font-bold text-purple-600 hover:text-pink-600 flex items-center gap-1.5"
                    >
                        <span>Lihat Jadwal Lengkap di Portal Siswa</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- Filter Tab Hari -->
                <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 no-scrollbar">
                    <button
                        v-for="hari in daftarHari"
                        :key="hari"
                        @click="selectedDay = hari"
                        :class="[
                            selectedDay === hari 
                                ? 'bg-purple-600 text-white shadow-md shadow-purple-500/20' 
                                : 'bg-white hover:bg-purple-50 text-slate-600 border border-purple-100'
                        ]"
                        class="px-4 py-2 rounded-2xl text-xs font-bold transition-all whitespace-nowrap"
                    >
                        {{ hari }}
                    </button>
                </div>

                <!-- Schedule Cards Grid -->
                <div v-if="semuaJadwalGabungan && semuaJadwalGabungan.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div 
                        v-for="item in semuaJadwalGabungan" 
                        :key="item.id"
                        class="p-6 rounded-3xl bg-white border border-purple-100 hover:border-purple-300 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group hover:-translate-y-1 duration-200"
                    >
                        <div>
                            <!-- Header Bar (Hari & Jam) -->
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-purple-100 text-purple-700">
                                        {{ item.hari }}
                                    </span>
                                    <span 
                                        v-if="item.tipe === 'kegiatan_terjadwal'" 
                                        class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200"
                                    >
                                        Terjadwal
                                    </span>
                                </div>
                                <span class="text-xs font-mono font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                                    {{ item.jam_mulai?.slice(0, 5) || '14:00' }} - {{ item.jam_selesai?.slice(0, 5) || '15:30' }}
                                </span>
                            </div>

                            <!-- Materi Pembelajaran -->
                            <h4 class="text-base font-black text-slate-900 group-hover:text-purple-600 transition-colors line-clamp-1 mb-1">
                                {{ item.materi }}
                            </h4>
                            <p v-if="item.keterangan && item.keterangan !== item.materi" class="text-xs text-slate-500 line-clamp-2 mb-4 font-medium">
                                {{ item.keterangan }}
                            </p>
                            <div v-else class="mb-4"></div>
                        </div>

                        <!-- Info Siswa & Pengajar -->
                        <div class="pt-4 border-t border-purple-50 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ (item.siswa || 'S').charAt(0) }}
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Siswa</p>
                                    <p class="font-bold text-slate-800 line-clamp-1">{{ item.siswa }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-slate-400 font-bold uppercase">Pengajar</p>
                                <p class="font-bold text-purple-700 line-clamp-1">{{ item.guru }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="p-12 rounded-3xl bg-white border border-purple-100 text-center max-w-xl mx-auto space-y-3 shadow-sm">
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Tidak Ada Jadwal Kelas untuk Hari Ini</h3>
                    <p class="text-xs text-slate-500 font-medium">
                        Belum ada sesi pertemuan bimbingan untuk filter hari yang Anda pilih. Pilih hari lain atau hubungi admin kami untuk informasi pendaftaran.
                    </p>
                </div>

            </div>
        </section>

        <!-- TESTIMONI SECTION -->
        <section id="testimoni" class="py-20 bg-gradient-to-b from-white via-pink-50/20 to-white border-y border-purple-100/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-pink-100 text-pink-700 text-xs font-black">
                        <svg class="w-3.5 h-3.5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        <span>Ulasan & Kepercayaan</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        Apa Kata Orang Tua Murid?
                    </h2>
                    <p class="text-slate-500 text-xs sm:text-sm font-medium">
                        Kisah keberhasilan nyata siswa-siswi ceria kami bersama para instruktur terbaik.
                    </p>
                </div>

                <div v-if="testimonials && testimonials.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div 
                        v-for="item in testimonials" 
                        :key="item.id"
                        class="p-8 rounded-3xl bg-white border border-purple-100 shadow-sm relative flex flex-col justify-between space-y-6 hover:shadow-md transition-shadow"
                    >
                        <div class="space-y-4">
                            <div class="flex items-center gap-1 text-amber-400">
                                <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed italic font-medium">
                                "{{ item.pesan || item.message }}"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 border-t border-purple-50">
                            <img v-if="item.foto || item.photo" :src="item.foto || item.photo" class="w-10 h-10 rounded-2xl object-cover" />
                            <div v-else class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 font-black flex items-center justify-center text-xs">
                                {{ (item.nama || item.name || 'W').substring(0, 2).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ item.nama || item.name }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold">Wali Murid</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="p-8 rounded-3xl bg-white border border-purple-100 shadow-sm relative flex flex-col justify-between space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-1 text-amber-400">
                                <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed italic font-medium">
                                "Perkembangan anak saya sangat pesat semenjak belajar di {{ settings?.nama_les || settings?.les_name || 'Les Ceria' }}. Laporan harian dan catatan guru memudahkan saya memantau materinya setiap malam."
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 border-t border-purple-50">
                            <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 font-black flex items-center justify-center text-xs">
                                IW
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">Ibu Wulandari</p>
                                <p class="text-[10px] text-slate-400 font-semibold">Wali Murid Kelas 4 SD</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 rounded-3xl bg-white border border-purple-100 shadow-sm relative flex flex-col justify-between space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-1 text-amber-400">
                                <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed italic font-medium">
                                "Pengajarnya sangat sabar dan komunikatif. Anak saya yang tadinya enggan belajar matematika sekarang justru selalu bersemangat menantikan jadwal lesnya."
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 border-t border-purple-50">
                            <div class="w-10 h-10 rounded-2xl bg-pink-100 text-pink-700 font-black flex items-center justify-center text-xs">
                                BP
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">Bapak Pratama</p>
                                <p class="text-[10px] text-slate-400 font-semibold">Wali Murid Kelas 6 SD</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 rounded-3xl bg-white border border-purple-100 shadow-sm relative flex flex-col justify-between space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-1 text-amber-400">
                                <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed italic font-medium">
                                "Sistem bintang prestasi dan sertifikat memberi motivasi luar biasa. Anak merasa pencapaian belajarnya benar-benar dihargai secara profesional."
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-4 border-t border-purple-50">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 font-black flex items-center justify-center text-xs">
                                IS
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">Ibu Sulistyo</p>
                                <p class="text-[10px] text-slate-400 font-semibold">Wali Murid Kelas 2 SMP</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- FOOTER & KONTAK -->
        <footer id="kontak" class="py-16 bg-slate-900 text-white text-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800">
                    
                    <div class="space-y-4 md:col-span-2">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 p-[2px]">
                                <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center overflow-hidden p-1">
                                    <img v-if="settings?.logo_url" :src="settings.logo_url" :alt="settings?.nama_les || 'Logo'" class="w-full h-full object-contain" />
                                    <span v-else class="font-black text-xs text-white">L</span>
                                </div>
                            </div>
                            <span class="font-black text-lg tracking-tight text-white">{{ settings?.nama_les || settings?.les_name || 'Les Ceria' }}</span>
                        </div>
                        <p class="text-slate-400 leading-relaxed max-w-sm font-medium">
                            Lembaga bimbingan belajar modern dengan suasana ceria, berorientasi prestasi akademik dan pembentukan karakter unggul anak.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Alamat & Lokasi</h4>
                        <p class="text-slate-400 leading-relaxed font-medium">
                            {{ settings?.alamat_les || settings?.les_address || 'Jl. Pendidikan No. 1, Kota Belajar' }}
                        </p>
                    </div>

                    <div class="space-y-3">
                        <h4 class="font-bold text-white uppercase tracking-wider text-[11px]">Kontak & Bantuan</h4>
                        <p class="text-slate-400 font-medium">WhatsApp: <span class="text-white font-bold">{{ settings?.kontak_les || settings?.les_contact || '0812-3456-7890' }}</span></p>
                        <p class="text-slate-400 font-medium">Email: <span class="text-white font-bold">kontak@lesceria.com</span></p>
                    </div>

                </div>

                <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400">
                    <p>&copy; 2026 {{ settings?.nama_les || settings?.les_name || 'Les Ceria' }}. Seluruh Hak Cipta Dilindungi.</p>
                    <div class="flex items-center gap-6 font-semibold">
                        <Link v-if="$page.props.auth?.user" :href="dashboardUrl" class="hover:text-white transition-colors">Buka Dashboard</Link>
                        <Link v-else href="/masuk" class="hover:text-white transition-colors">Portal Masuk</Link>
                        <a href="#keunggulan" class="hover:text-white transition-colors">Keunggulan Belajar</a>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</template>
