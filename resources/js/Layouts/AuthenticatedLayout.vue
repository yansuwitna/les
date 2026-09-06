<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingSidebar = ref(false);
const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const userRole = computed(() => user.value?.peran || user.value?.role || 'admin');
const userName = computed(() => user.value?.nama || user.value?.name || 'Pengguna');
const settings = computed(() => page.props.pengaturan || page.props.settings || {});
const appName = computed(() => settings.value?.nama_les || settings.value?.les_name || 'Les Ceria');
const appLogo = computed(() => settings.value?.logo_url || null);

const adminMenus = [
    {
        name: 'Beranda Admin',
        route: 'admin.dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    {
        name: 'Data Guru',
        route: 'admin.guru.index',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    },
    {
        name: 'Data Siswa (Wali)',
        route: 'admin.siswa.index',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    },
    {
        name: 'Pembimbing',
        route: 'admin.jadwal.index',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    },
    {
        name: 'Identitas & Web',
        route: 'admin.identitas.index',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
    },
    {
        name: 'Ubah Profil',
        route: 'profile.edit',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
    },
];

const guruMenus = [
    {
        name: 'Dashboard Guru',
        route: 'guru.dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    {
        name: 'Siswa Bimbingan',
        route: 'guru.bimbingan.index',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    },
    {
        name: 'Ubah Profil',
        route: 'profile.edit',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
    },
];

const ortuMenus = [
    {
        name: 'Dashboard Wali',
        route: 'ortu.dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    {
        name: 'Jadwal Kursus Anak',
        route: 'ortu.dashboard',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
    },
    {
        name: 'Kemajuan Belajar',
        route: 'ortu.dashboard',
        icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'
    },
    {
        name: 'Ubah Profil',
        route: 'profile.edit',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
    },
];

const currentMenus = computed(() => {
    if (userRole.value === 'guru') return guruMenus;
    if (userRole.value === 'ortu') return ortuMenus;
    return adminMenus;
});

const roleBadge = computed(() => {
    if (userRole.value === 'admin') return { label: 'Administrator', bg: 'bg-purple-100 text-purple-700 border-purple-200' };
    if (userRole.value === 'guru') return { label: 'Guru / Instruktur', bg: 'bg-indigo-100 text-indigo-700 border-indigo-200' };
    return { label: 'Wali Murid', bg: 'bg-pink-100 text-pink-700 border-pink-200' };
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 flex font-sans antialiased selection:bg-purple-200 selection:text-purple-900">
        
        <!-- Mobile Top Navigation Bar -->
        <header class="lg:hidden fixed top-0 inset-x-0 h-16 bg-white/90 backdrop-blur-xl border-b border-purple-100/80 z-30 flex items-center justify-between px-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-purple-600 via-pink-500 to-amber-400 p-[2px] shadow-md shadow-purple-500/20">
                    <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center overflow-hidden p-1">
                        <img v-if="appLogo" :src="appLogo" :alt="appName" class="w-full h-full object-contain" />
                        <span v-else class="font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500">L</span>
                    </div>
                </div>
                <div>
                    <span class="font-black text-base tracking-tight text-slate-900">{{ appName }}</span>
                    <span class="block text-[9px] uppercase font-bold tracking-widest text-purple-600">Portal Edukasi</span>
                </div>
            </div>

            <button 
                @click="showingSidebar = true" 
                class="p-2.5 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 transition-all focus:outline-none"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>

        <!-- Backdrop for mobile sidebar -->
        <div 
            v-if="showingSidebar" 
            @click="showingSidebar = false" 
            class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm z-40 lg:hidden transition-opacity duration-300"
        ></div>

        <!-- LUXURY BRIGHT LEFT SIDEBAR -->
        <aside 
            :class="[showingSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']"
            class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-purple-100 flex flex-col h-screen transition-transform duration-300 ease-out shadow-xl lg:shadow-[4px_0_24px_rgba(147,51,234,0.03)]"
        >
            <!-- Brand Header -->
            <div class="p-6 border-b border-purple-50 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-purple-600 via-pink-500 to-amber-400 p-[2px] shadow-lg shadow-purple-500/20 group-hover:scale-105 transition-transform duration-300">
                        <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center overflow-hidden p-1">
                            <img v-if="appLogo" :src="appLogo" :alt="appName" class="w-full h-full object-contain" />
                            <svg v-else class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-black text-lg tracking-tight text-slate-900 group-hover:text-purple-600 transition-colors">{{ appName }}</span>
                            <span class="w-2 h-2 rounded-full bg-pink-500 animate-pulse"></span>
                        </div>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-purple-600">Portal Pendidikan</span>
                    </div>
                </Link>

                <button 
                    @click="showingSidebar = false" 
                    class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Profile Summary Card -->
            <div class="p-4 mx-4 my-4 rounded-2xl bg-gradient-to-br from-purple-50/70 via-pink-50/40 to-white border border-purple-100/90 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 flex items-center justify-center font-black text-sm text-white shadow-md shadow-purple-500/25 shrink-0 overflow-hidden">
                        <img v-if="user?.foto_url" :src="user.foto_url" :alt="userName" class="w-full h-full object-cover" />
                        <span v-else>{{ userName.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-sm text-slate-800 truncate">{{ userName }}</p>
                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border mt-0.5', roleBadge.bg]">
                            {{ roleBadge.label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-2 space-y-1.5">
                <div class="px-3 pb-2 pt-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Menu Navigasi
                </div>

                <Link
                    v-for="menu in currentMenus"
                    :key="menu.name"
                    :href="route(menu.route)"
                    :class="[
                        route().current(menu.route) 
                            ? 'bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold shadow-md shadow-purple-500/20' 
                            : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50 font-semibold'
                    ]"
                    class="group flex items-center gap-3.5 px-4 py-3 rounded-2xl transition-all duration-200"
                >
                    <div 
                        :class="[
                            route().current(menu.route)
                                ? 'text-white' 
                                : 'text-slate-400 group-hover:text-purple-600 group-hover:scale-110'
                        ]"
                        class="transition-all duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menu.icon" />
                        </svg>
                    </div>
                    <span class="text-sm tracking-wide">{{ menu.name }}</span>
                </Link>
            </nav>

            <!-- Bottom Account & Logout Section -->
            <div class="p-4 border-t border-purple-50 space-y-2 bg-slate-50/50">
                <Link 
                    :href="route('profile.edit')" 
                    :class="[
                        route().current('profile.edit')
                            ? 'bg-purple-100 text-purple-700 font-bold'
                            : 'text-slate-600 hover:text-purple-700 hover:bg-purple-50 font-semibold'
                    ]"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors text-xs"
                >
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Ubah Profil</span>
                </Link>

                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button" 
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-pink-600 hover:text-pink-700 hover:bg-pink-50 transition-colors text-xs font-bold text-left"
                >
                    <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar Aplikasi</span>
                </Link>
            </div>
        </aside>

        <!-- MAIN BRIGHT CONTENT AREA -->
        <div class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden bg-[#FAFBFD]">
            <!-- Top Navbar Bar (Desktop) -->
            <header class="hidden lg:flex items-center justify-between px-8 py-4 bg-white/80 backdrop-blur-xl border-b border-purple-100/80 sticky top-0 z-20 shadow-sm">
                <div v-if="$slots.header" class="flex items-center">
                    <slot name="header" />
                </div>
                <div v-else class="text-xs text-slate-500 font-semibold">
                    Portal Manajemen Belajar Ceria &bull; Suasana Hangat & Bersahabat
                </div>

                <div v-if="userRole !== 'guru'" class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs text-emerald-700 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span class="w-2 h-2 -ml-3 rounded-full bg-emerald-500"></span>
                        <span>Sistem Aktif</span>
                    </div>

                    <Link 
                        :href="route('profile.edit')" 
                        class="flex items-center gap-2 p-1 pl-2 pr-3 rounded-2xl bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-all group"
                        title="Ubah Profil"
                    >
                        <div class="w-8 h-8 rounded-xl overflow-hidden bg-white shadow-sm flex items-center justify-center border border-purple-200">
                            <img v-if="user?.foto_url" :src="user.foto_url" :alt="userName" class="w-full h-full object-cover" />
                            <span v-else class="text-xs font-black text-purple-700">{{ userName.charAt(0).toUpperCase() }}</span>
                        </div>
                        <div class="text-left hidden sm:block">
                            <span class="block text-xs font-bold text-slate-800 group-hover:text-purple-700">{{ userName }}</span>
                            <span class="block text-[9px] font-semibold text-purple-500 uppercase">{{ roleBadge.label }}</span>
                        </div>
                    </Link>

                    <Link 
                        href="/" 
                        target="_blank"
                        class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-all"
                    >
                        <span>Lihat Website</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </Link>
                </div>
            </header>

            <!-- Scrollable Content Canvas -->
            <main class="flex-1 overflow-y-auto pt-16 lg:pt-0">
                <!-- Mobile Header Sub-Bar (Visible on mobile screens) -->
                <div v-if="$slots.header" class="lg:hidden p-4 bg-white/95 backdrop-blur-md border-b border-purple-100 shadow-sm">
                    <slot name="header" />
                </div>

                <div class="min-h-full p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-slate-50 via-purple-50/30 to-pink-50/20">
                    <slot />
                </div>
            </main>
        </div>

    </div>
</template>
