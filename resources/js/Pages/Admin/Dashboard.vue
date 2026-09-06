<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object
});

const todayDate = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
}).format(new Date());
</script>

<template>
    <Head :title="'Beranda Admin - ' + ($page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">
                    Pusat Kendali Admin
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ todayDate }} &bull; Semester Belajar 2026</p>
            </div>
        </template>

        <div class="max-w-7xl mx-auto space-y-8">
            
            <!-- CHEERFUL & BRIGHT LUXURY BANNER -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 p-8 sm:p-10 shadow-xl shadow-purple-500/15 text-white">
                <!-- Soft background pattern circles -->
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-pink-400/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="max-w-2xl space-y-2.5">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-amber-300 animate-pulse"></span>
                            <span>Selamat Datang, Administrator!</span>
                        </div>
                        <h2 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                            Ruang Pengelolaan Belajar <br>
                            Yang Hangat, Ceria & Nyaman
                        </h2>
                        <p class="text-purple-100 text-xs sm:text-sm leading-relaxed font-medium max-w-xl">
                            Semua data pengajar, perkembangan siswa, dan kurikulum dapat dipantau dengan mudah, rapi, dan transparan.
                        </p>
                    </div>

                    <div class="hidden lg:flex flex-col gap-3 shrink-0">
                        <div class="p-4 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-amber-300">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-purple-100 font-bold">Prestasi Siswa</p>
                                <p class="text-base font-extrabold text-white">100% Terpantau</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3 METRIC CARDS (BRIGHT & CHEERFUL) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Guru Stat -->
                <div class="p-6 rounded-3xl bg-white border border-purple-100/90 shadow-sm hover:shadow-xl hover:border-purple-200 transition-all duration-300 flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-purple-600">Total Pengajar</p>
                        <h3 class="text-4xl font-black text-slate-800 mt-1 group-hover:text-purple-600 transition-colors">
                            {{ stats.teachers || 0 }}
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Guru profesional & ceria</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Siswa Stat -->
                <div class="p-6 rounded-3xl bg-white border border-pink-100/90 shadow-sm hover:shadow-xl hover:border-pink-200 transition-all duration-300 flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-pink-600">Siswa Terdaftar</p>
                        <h3 class="text-4xl font-black text-slate-800 mt-1 group-hover:text-pink-600 transition-colors">
                            {{ stats.students || 0 }}
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Anak-anak hebat & aktif</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center group-hover:scale-110 group-hover:bg-pink-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Jadwal Stat -->
                <div class="p-6 rounded-3xl bg-white border border-amber-100/90 shadow-sm hover:shadow-xl hover:border-amber-200 transition-all duration-300 flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-600">Jadwal Aktif</p>
                        <h3 class="text-4xl font-black text-slate-800 mt-1 group-hover:text-amber-600 transition-colors">
                            {{ stats.schedules || 0 }}
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Sesi pertemuan pekan ini</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

            </div>

            <!-- QUICK ACTIONS MENU -->
            <div>
                <div class="mb-5">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Aksi Cepat Menu Admin</span>
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola data penting dengan satu kali klik</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <!-- Menu 1: Data Guru -->
                    <Link
                        :href="route('admin.guru.index')"
                        class="p-6 rounded-3xl bg-white border border-purple-100 hover:border-purple-300 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
                    >
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-base text-slate-800 group-hover:text-purple-700 transition-colors">Data Guru</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed font-medium">
                                Tambah, edit, upload foto, dan atur status akun pengajar.
                            </p>
                        </div>
                        <div class="mt-5 pt-3 border-t border-purple-50 flex items-center justify-between text-xs font-bold text-purple-600 group-hover:text-purple-800">
                            <span>Buka Data Guru</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </Link>

                    <!-- Menu 2: Siswa & Ortu -->
                    <Link
                        :href="route('admin.siswa.index')"
                        class="p-6 rounded-3xl bg-white border border-pink-100 hover:border-pink-300 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
                    >
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center group-hover:bg-pink-500 group-hover:text-white transition-all duration-300 mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-base text-slate-800 group-hover:text-pink-600 transition-colors">Data Siswa</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed font-medium">
                                Kelola siswa 10 digit nomor akun, foto siswa, dan status aktif.
                            </p>
                        </div>
                        <div class="mt-5 pt-3 border-t border-pink-50 flex items-center justify-between text-xs font-bold text-pink-600 group-hover:text-pink-700">
                            <span>Kelola Siswa</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </Link>

                    <!-- Menu 3: Pembimbing -->
                    <Link
                        :href="route('admin.jadwal.index')"
                        class="p-6 rounded-3xl bg-white border border-amber-100 hover:border-amber-300 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
                    >
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-base text-slate-800 group-hover:text-amber-600 transition-colors">Pembimbing</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed font-medium">
                                Penentuan guru pembimbing untuk setiap siswa bimbingan.
                            </p>
                        </div>
                        <div class="mt-5 pt-3 border-t border-amber-50 flex items-center justify-between text-xs font-bold text-amber-600 group-hover:text-amber-700">
                            <span>Atur Pembimbing</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </Link>

                    <!-- Menu 4: Identitas & Web -->
                    <Link
                        :href="route('admin.identitas.index')"
                        class="p-6 rounded-3xl bg-white border border-blue-100 hover:border-blue-300 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group"
                    >
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-base text-slate-800 group-hover:text-blue-600 transition-colors">Identitas & Web</h4>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed font-medium">
                                Konfigurasi nama les, logo favicon, slide banner, dan kontak resmi.
                            </p>
                        </div>
                        <div class="mt-5 pt-3 border-t border-blue-50 flex items-center justify-between text-xs font-bold text-blue-600 group-hover:text-blue-700">
                            <span>Kelola Identitas</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </Link>

                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
