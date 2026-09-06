<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();

// Roles tabs: admin, guru, ortu (siswa)
const activeRole = ref('admin');

const form = useForm({
    username: '',
    kata_sandi: '',
    password: '',
    peran: 'admin',
    remember: false,
});

const errorMessage = computed(() => {
    return form.errors.username 
        || form.errors.kata_sandi 
        || form.errors.password 
        || form.errors.error 
        || page.props.flash?.error;
});

const errorTitle = computed(() => {
    if (activeRole.value === 'admin') return 'Gagal Masuk Administrator';
    if (activeRole.value === 'guru') return 'Gagal Masuk Guru';
    return 'Gagal Masuk Siswa / Wali Murid';
});

const errorHelpTip = computed(() => {
    if (activeRole.value === 'admin') {
        return 'Petunjuk: Akun bawaan sistem adalah username "admin" dan kata sandi "admin".';
    } else if (activeRole.value === 'guru') {
        return 'Petunjuk: Periksa kembali 16 digit NIK Anda. Jika belum terdaftar, hubungi administrator bimbingan belajar.';
    } else {
        return 'Petunjuk: Periksa kembali 10 digit nomor siswa Anda atau hubungi guru pembimbing.';
    }
});

watch(activeRole, (newRole) => {
    form.peran = newRole;
    form.username = '';
    form.kata_sandi = '';
    form.password = '';
    form.clearErrors();
});

const roleConfig = computed(() => {
    if (activeRole.value === 'admin') {
        return {
            title: 'Portal Administrator',
            subtitle: 'Masuk dengan username dan kata sandi admin Anda.',
            label: 'Username Admin',
            placeholder: 'Contoh: admin',
            helper: 'Gunakan username resmi admin untuk mengelola sistem (Default: admin / admin).',
            iconType: 'admin'
        };
    } else if (activeRole.value === 'guru') {
        return {
            title: 'Portal Guru & Pengajar',
            subtitle: 'Masuk dengan NIK (16 digit) dan kata sandi instruktur.',
            label: 'Nomor Induk Kependudukan (NIK)',
            placeholder: 'Contoh: 5102010101900001',
            helper: 'Masukkan 16 digit NIK terdaftar sebagai identitas masuk.',
            iconType: 'guru'
        };
    } else {
        return {
            title: 'Portal Siswa & Wali Murid',
            subtitle: 'Masuk dengan 10 digit nomor siswa otomatis dari sistem.',
            label: 'Nomor Siswa (10 Digit Otomatis)',
            placeholder: 'Contoh: 2026000001',
            helper: 'Masukkan 10 digit nomor identitas siswa yang terdaftar.',
            iconType: 'siswa'
        };
    }
});

const submit = () => {
    form.password = form.kata_sandi;
    form.post(route('login'), {
        onFinish: () => {
            form.reset('kata_sandi', 'password');
        },
    });
};
</script>

<template>
    <Head :title="'Masuk - Portal Pendidikan ' + ($page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria')" />

    <div class="min-h-screen bg-gradient-to-br from-purple-50/80 via-slate-50 to-pink-50/70 flex items-center justify-center p-4 relative overflow-hidden font-sans selection:bg-purple-200 selection:text-purple-900">
        
        <!-- Cheerful Floating Blur Elements -->
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-purple-200/50 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
        <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-pink-200/50 rounded-full blur-3xl pointer-events-none animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-amber-100/60 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Main Card Container -->
        <div class="max-w-4xl w-full rounded-3xl bg-white border border-purple-100 shadow-2xl shadow-purple-500/10 overflow-hidden flex flex-col md:flex-row z-10">
            
            <!-- Left Side: Cheerful Gradient Panel -->
            <div class="md:w-5/12 p-8 lg:p-10 bg-gradient-to-br from-purple-600 via-indigo-600 to-pink-500 text-white flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -top-16 -right-16 w-48 h-48 bg-white/10 rounded-full blur-xl"></div>
                <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-pink-300/20 rounded-full blur-xl"></div>

                <div class="relative z-10 space-y-6">
                    <Link href="/" class="inline-flex items-center gap-3 group">
                        <div v-if="$page.props.pengaturan?.logo_url" class="w-11 h-11 rounded-2xl bg-white p-1 shadow-md transform -rotate-3 group-hover:rotate-0 transition-transform overflow-hidden flex items-center justify-center">
                            <img :src="$page.props.pengaturan.logo_url" :alt="$page.props.pengaturan?.nama_les || 'Logo'" class="w-full h-full object-contain">
                        </div>
                        <div v-else class="w-11 h-11 rounded-2xl bg-white text-purple-600 flex items-center justify-center shadow-md transform -rotate-3 group-hover:rotate-0 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <span class="font-black text-xl tracking-tight text-white">{{ $page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria' }}</span>
                            <span class="block text-[9px] uppercase font-extrabold tracking-widest text-amber-200">Executive Academy</span>
                        </div>
                    </Link>

                    <div class="space-y-3 pt-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-amber-200 text-xs font-extrabold">
                            <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span>Autentikasi Terpisah & Aman</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-snug">
                            Masuk Sesuai <br>Peran Anda
                        </h2>
                        <p class="text-purple-100 text-xs leading-relaxed font-medium">
                            Setiap peran memiliki jalur autentikasi khusus: Username untuk Admin, NIK untuk Guru, dan Nomor 10 Digit untuk Siswa & Wali Murid.
                        </p>
                    </div>

                    <!-- Role Badges List -->
                    <div class="pt-3 space-y-2.5 border-t border-white/20">
                        <div class="flex items-center gap-2.5 text-xs text-purple-100 font-medium">
                            <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white shrink-0 font-bold text-[10px]">
                                1
                            </div>
                            <span><strong>Admin:</strong> Masuk dengan Username</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-xs text-purple-100 font-medium">
                            <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white shrink-0 font-bold text-[10px]">
                                2
                            </div>
                            <span><strong>Guru:</strong> Masuk dengan 16 Digit NIK</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-xs text-purple-100 font-medium">
                            <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white shrink-0 font-bold text-[10px]">
                                3
                            </div>
                            <span><strong>Siswa:</strong> Masuk dengan 10 Digit Nomor Siswa</span>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 pt-6 text-[11px] text-purple-200 font-medium">
                    &copy; 2026 {{ $page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria' }}. Sistem Akademik Terpadu.
                </div>
            </div>

            <!-- Right Side: Clean White Form Panel -->
            <div class="md:w-7/12 p-6 sm:p-10 bg-white flex flex-col justify-center">
                
                <!-- Role Selector Tabs -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Pilih Peran Masuk</p>
                        <Link 
                            href="/" 
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-slate-600 hover:text-purple-700 bg-slate-100/90 hover:bg-purple-50 border border-slate-200/80 transition-all group"
                        >
                            <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-purple-600 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Kembali ke Home</span>
                        </Link>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100/80 rounded-2xl border border-slate-200/70">
                        <button
                            type="button"
                            @click="activeRole = 'admin'"
                            :class="[
                                activeRole === 'admin' 
                                    ? 'bg-white text-purple-700 shadow-sm font-black' 
                                    : 'text-slate-600 hover:text-slate-900 font-bold'
                            ]"
                            class="py-2.5 px-2 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Admin</span>
                        </button>

                        <button
                            type="button"
                            @click="activeRole = 'guru'"
                            :class="[
                                activeRole === 'guru' 
                                    ? 'bg-white text-indigo-700 shadow-sm font-black' 
                                    : 'text-slate-600 hover:text-slate-900 font-bold'
                            ]"
                            class="py-2.5 px-2 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Guru</span>
                        </button>

                        <button
                            type="button"
                            @click="activeRole = 'ortu'"
                            :class="[
                                activeRole === 'ortu' 
                                    ? 'bg-white text-pink-700 shadow-sm font-black' 
                                    : 'text-slate-600 hover:text-slate-900 font-bold'
                            ]"
                            class="py-2.5 px-2 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Siswa/Wali</span>
                        </button>
                    </div>
                </div>

                <!-- Form Header -->
                <div class="mb-5">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">{{ roleConfig.title }}</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ roleConfig.subtitle }}</p>
                </div>

                <div v-if="status" class="mb-4 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 p-3 rounded-2xl flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ status }}</span>
                </div>

                <!-- Prominent Failed Login Alert Banner -->
                <transition
                    enter-active-class="transition ease-out duration-300 transform"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200 transform"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div 
                        v-if="errorMessage" 
                        class="mb-5 p-4 rounded-2xl bg-rose-50/90 border border-rose-200 text-rose-800 shadow-sm relative overflow-hidden"
                    >
                        <div class="flex items-start gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-black text-rose-900 tracking-tight flex items-center gap-2">
                                        <span>{{ errorTitle }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-200 text-rose-800 uppercase tracking-wide">Gagal Masuk</span>
                                    </h4>
                                    <button 
                                        type="button" 
                                        @click="form.clearErrors()" 
                                        class="text-rose-400 hover:text-rose-700 transition-colors p-1 -mr-1"
                                        title="Tutup pesan error"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs font-semibold text-rose-800 leading-relaxed">
                                    {{ errorMessage }}
                                </p>
                                <p class="text-[11px] font-medium text-rose-600/90 pt-1 border-t border-rose-200/60 mt-1.5">
                                    {{ errorHelpTip }}
                                </p>
                            </div>
                        </div>
                    </div>
                </transition>

                <form @submit.prevent="submit" class="space-y-4">
                    
                    <!-- Identifier Field (Username / NIK / 10-Digit) -->
                    <div>
                        <InputLabel for="username" :value="roleConfig.label" class="text-xs font-bold text-slate-700 mb-1.5" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg v-if="activeRole === 'admin'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <svg v-else-if="activeRole === 'guru'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <TextInput
                                id="username"
                                type="text"
                                :class="[
                                    form.errors.username
                                        ? '!border-rose-400 !bg-rose-50/30 focus:!ring-rose-200 focus:!border-rose-500'
                                        : 'border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-500 focus:ring-purple-200'
                                ]"
                                class="pl-10 block w-full text-xs rounded-2xl text-slate-800 placeholder-slate-400 focus:ring-2 font-medium transition-all"
                                v-model="form.username"
                                required
                                autofocus
                                :placeholder="roleConfig.placeholder"
                                @input="form.clearErrors('username')"
                            />
                        </div>
                        <p class="text-[11px] text-slate-400 font-medium mt-1">{{ roleConfig.helper }}</p>
                        <InputError class="mt-1" :message="form.errors.username" />
                    </div>

                    <!-- Password / Kata Sandi Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <InputLabel for="kata_sandi" value="Kata Sandi" class="text-xs font-bold text-slate-700" />
                            <Link v-if="canResetPassword && activeRole === 'admin'" :href="route('password.request')" class="text-xs font-bold text-purple-600 hover:text-pink-600 transition-colors">
                                Lupa sandi?
                            </Link>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <TextInput
                                id="kata_sandi"
                                type="password"
                                :class="[
                                    (form.errors.kata_sandi || form.errors.password)
                                        ? '!border-rose-400 !bg-rose-50/30 focus:!ring-rose-200 focus:!border-rose-500'
                                        : 'border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-500 focus:ring-purple-200'
                                ]"
                                class="pl-10 block w-full text-xs rounded-2xl text-slate-800 placeholder-slate-400 focus:ring-2 transition-all"
                                v-model="form.kata_sandi"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                @input="form.clearErrors('kata_sandi', 'password')"
                            />
                        </div>
                        <InputError class="mt-1" :message="form.errors.kata_sandi || form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center cursor-pointer">
                            <Checkbox name="remember" v-model:checked="form.remember" class="rounded text-purple-600 focus:ring-purple-500 border-slate-300" />
                            <span class="ml-2.5 text-xs text-slate-600 font-semibold">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <div class="pt-2 flex flex-col gap-2.5">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex justify-center items-center py-3.5 px-4 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-lg shadow-purple-500/25 transition-all transform hover:-translate-y-0.5 disabled:opacity-50"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Memverifikasi Data...' : 'Masuk ke Portal Sekarang &rarr;' }}</span>
                        </button>

                        <Link
                            href="/"
                            class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-2xl text-xs font-bold text-slate-600 hover:text-purple-700 bg-slate-100/90 hover:bg-purple-50 border border-slate-200/80 transition-all text-center group"
                        >
                            <svg class="w-4 h-4 text-slate-500 group-hover:text-purple-600 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Kembali ke Home</span>
                        </Link>
                    </div>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500">
                        Butuh bantuan login atau lupa nomor akun? 
                        <span class="text-purple-700 font-bold">Hubungi Admin Bimbingan Les.</span>
                    </p>
                </div>
            </div>

        </div>
    </div>
</template>
