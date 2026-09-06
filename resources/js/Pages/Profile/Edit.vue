<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: null,
    },
    userData: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const user = computed(() => props.userData?.id ? props.userData : (page.props.auth?.user || {}));
const role = computed(() => user.value?.peran || user.value?.role || 'admin');

// Form 1: Ubah Informasi Profil
const fotoPreview = ref(user.value?.foto_url || null);
const fotoInput = ref(null);

const profileForm = useForm({
    nama: user.value?.nama || user.value?.name || '',
    username: user.value?.username || '',
    email: user.value?.email || '',
    foto: null,
    telepon: user.value?.telepon || '',
    nama_wali: user.value?.nama_wali || '',
    telepon_wali: user.value?.telepon_wali || '',
});

watch(() => user.value, (newVal) => {
    if (newVal) {
        if (!profileForm.isDirty) {
            profileForm.nama = newVal.nama || newVal.name || '';
            profileForm.username = newVal.username || '';
            profileForm.email = newVal.email || '';
            profileForm.telepon = newVal.telepon || '';
            profileForm.nama_wali = newVal.nama_wali || '';
            profileForm.telepon_wali = newVal.telepon_wali || '';
        }
        fotoPreview.value = newVal.foto_url || null;
    }
}, { deep: true, immediate: true });

const handleFotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        profileForm.foto = file;
        fotoPreview.value = URL.createObjectURL(file);
    }
};

const updateProfile = () => {
    profileForm.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            profileForm.foto = null;
        },
    });
};

// Form 2: Ubah Kata Sandi
const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};

const getRoleBadge = computed(() => {
    if (role.value === 'admin') {
        return { label: 'Administrator', bg: 'bg-purple-100 text-purple-700 border-purple-200' };
    }
    if (role.value === 'guru') {
        return { label: 'Guru / Pengajar', bg: 'bg-indigo-100 text-indigo-700 border-indigo-200' };
    }
    return { label: 'Siswa & Wali Murid', bg: 'bg-pink-100 text-pink-700 border-pink-200' };
});

const userIdentifier = computed(() => {
    if (role.value === 'admin') return user.value?.username || 'admin';
    if (role.value === 'guru') return user.value?.nik || '-';
    return user.value?.nomor_siswa || '-';
});

const identifierLabel = computed(() => {
    if (role.value === 'admin') return 'Username';
    if (role.value === 'guru') return 'NIK (16 Digit)';
    return 'Nomor Siswa (10 Digit)';
});
</script>

<template>
    <Head :title="'Ubah Profil - ' + ($page.props.pengaturan?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">
                        Ubah Profil & Akun
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">
                        Kelola data informasi identitas dan keamanan kata sandi akun Anda
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-6xl mx-auto space-y-8 pb-12">
            
            <!-- HEADER HERO PROFILE CARD -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 p-6 sm:p-8 text-white shadow-xl shadow-purple-500/10">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute top-0 right-1/3 w-48 h-48 bg-pink-300/20 rounded-full blur-xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
                    <!-- Photo or Avatar Initial with Click-to-Change -->
                    <div class="relative group cursor-pointer" @click="fotoInput?.click()">
                        <div v-if="fotoPreview" class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl overflow-hidden border-4 border-white/40 shadow-xl bg-white p-1">
                            <img :src="fotoPreview" :alt="user?.nama" class="w-full h-full object-cover rounded-2xl" />
                        </div>
                        <div v-else class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-white/20 backdrop-blur-md border-4 border-white/30 flex items-center justify-center text-3xl sm:text-4xl font-black text-white shadow-xl">
                            {{ (user?.nama || user?.name || 'U').charAt(0).toUpperCase() }}
                        </div>

                        <!-- Hover overlay -->
                        <div class="absolute inset-0 rounded-3xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[10px] font-bold gap-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Ganti Foto</span>
                        </div>

                        <span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-purple-600 text-white border-2 border-white shadow flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                    </div>

                    <!-- Profile Bio Info -->
                    <div class="space-y-2 flex-1">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <span :class="['px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border shadow-sm', getRoleBadge.bg]">
                                {{ getRoleBadge.label }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-purple-100 border border-white/10">
                                Akun Terverifikasi
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            {{ user?.nama || user?.name || 'Pengguna' }}
                        </h2>

                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs font-semibold text-purple-100 pt-1">
                            <div class="flex items-center gap-1.5 bg-black/10 px-3 py-1 rounded-xl backdrop-blur-sm">
                                <span class="text-purple-200">{{ identifierLabel }}:</span>
                                <span class="font-bold text-white font-mono">{{ userIdentifier }}</span>
                            </div>

                            <div v-if="user?.email" class="flex items-center gap-1.5 bg-black/10 px-3 py-1 rounded-xl backdrop-blur-sm">
                                <svg class="w-3.5 h-3.5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ user.email }}</span>
                            </div>

                            <div v-if="user?.telepon || user?.telepon_wali" class="flex items-center gap-1.5 bg-black/10 px-3 py-1 rounded-xl backdrop-blur-sm">
                                <svg class="w-3.5 h-3.5 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ user.telepon || user.telepon_wali }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TWO COLUMN GRID FOR FORMS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- CARD 1: INFORMASI PROFIL -->
                <div class="bg-white rounded-3xl border border-purple-100/80 shadow-sm p-6 sm:p-8 space-y-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 pb-5 border-b border-purple-50">
                            <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 tracking-tight">Informasi Akun</h3>
                                <p class="text-xs text-slate-400 font-medium">Perbarui data nama dan detail kontak akun Anda</p>
                            </div>
                        </div>

                        <form @submit.prevent="updateProfile" class="mt-6 space-y-4">
                            <!-- Identifier (ID Akun - Readonly untuk Guru & Siswa) -->
                            <div v-if="role !== 'admin'">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">{{ identifierLabel }} (ID Akun)</label>
                                <input
                                    type="text"
                                    :value="userIdentifier"
                                    disabled
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 text-slate-500 text-sm font-mono font-bold cursor-not-allowed"
                                />
                                <span class="text-[10px] text-slate-400 mt-1 block">ID ini digenerate sistem dan digunakan sebagai identitas login utama.</span>
                            </div>

                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                                <input
                                    type="text"
                                    v-model="profileForm.nama"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none"
                                    required
                                />
                                <p v-if="profileForm.errors.nama" class="text-xs text-pink-600 mt-1 font-semibold">
                                    {{ profileForm.errors.nama }}
                                </p>
                            </div>

                            <!-- Foto Profil -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Foto Profil</label>
                                <div class="flex items-center gap-3">
                                    <input
                                        type="file"
                                        ref="fotoInput"
                                        @change="handleFotoChange"
                                        accept="image/png, image/jpeg, image/jpg, image/webp"
                                        class="w-full px-3 py-2 rounded-2xl border border-slate-200 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer"
                                    />
                                    <button 
                                        v-if="profileForm.foto" 
                                        type="button" 
                                        @click="profileForm.foto = null; fotoPreview = user?.foto_url || null; if (fotoInput) fotoInput.value = ''"
                                        class="p-2 rounded-xl text-pink-600 hover:bg-pink-50 text-xs font-bold shrink-0"
                                        title="Batal pilih foto"
                                    >
                                        Batal
                                    </button>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Format JPG, PNG atau WebP (Maksimal 2MB).</span>
                                <p v-if="profileForm.errors.foto" class="text-xs text-pink-600 mt-1 font-semibold">
                                    {{ profileForm.errors.foto }}
                                </p>
                            </div>

                            <!-- Email Khusus Admin -->
                            <div v-if="role === 'admin'">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Administrator (Opsional)</label>
                                <input
                                    type="email"
                                    v-model="profileForm.email"
                                    placeholder="admin@contoh.com"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none"
                                />
                                <p v-if="profileForm.errors.email" class="text-xs text-pink-600 mt-1 font-semibold">
                                    {{ profileForm.errors.email }}
                                </p>
                            </div>

                            <!-- Additional Fields for Guru -->
                            <template v-if="role === 'guru'">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                                    <input
                                        type="email"
                                        v-model="profileForm.email"
                                        placeholder="nama@contoh.com"
                                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none"
                                    />
                                    <p v-if="profileForm.errors.email" class="text-xs text-pink-600 mt-1 font-semibold">
                                        {{ profileForm.errors.email }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Telepon / WhatsApp</label>
                                    <input
                                        type="text"
                                        v-model="profileForm.telepon"
                                        placeholder="081234567890"
                                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none"
                                    />
                                    <p v-if="profileForm.errors.telepon" class="text-xs text-pink-600 mt-1 font-semibold">
                                        {{ profileForm.errors.telepon }}
                                    </p>
                                </div>
                            </template>

                            <!-- Additional Fields for Siswa / Ortu -->
                            <template v-if="role === 'ortu' || role === 'siswa'">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Orang Tua / Wali</label>
                                    <input
                                        type="text"
                                        v-model="profileForm.nama_wali"
                                        placeholder="Nama wali murid"
                                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none"
                                    />
                                    <p v-if="profileForm.errors.nama_wali" class="text-xs text-pink-600 mt-1 font-semibold">
                                        {{ profileForm.errors.nama_wali }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">No. WhatsApp Wali</label>
                                    <input
                                        type="text"
                                        v-model="profileForm.telepon_wali"
                                        placeholder="081234567890"
                                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all outline-none"
                                    />
                                    <p v-if="profileForm.errors.telepon_wali" class="text-xs text-pink-600 mt-1 font-semibold">
                                        {{ profileForm.errors.telepon_wali }}
                                    </p>
                                </div>
                            </template>

                            <div class="pt-3">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="w-full py-3 px-6 rounded-2xl bg-gradient-to-r from-purple-600 to-pink-500 text-white font-bold text-xs shadow-md shadow-purple-500/20 hover:shadow-lg hover:shadow-purple-500/30 hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50 flex items-center justify-center gap-2"
                                >
                                    <svg v-if="profileForm.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Simpan Perubahan Profil</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- CARD 2: UBAH KATA SANDI (PASSWORD) -->
                <div class="bg-white rounded-3xl border border-purple-100/80 shadow-sm p-6 sm:p-8 space-y-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 pb-5 border-b border-purple-50">
                            <div class="w-10 h-10 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 tracking-tight">Keamanan & Password</h3>
                                <p class="text-xs text-slate-400 font-medium">Perbarui kata sandi untuk menjaga keamanan akun Anda</p>
                            </div>
                        </div>

                        <!-- Password advice tip -->
                        <div class="mt-4 p-3.5 rounded-2xl bg-purple-50/70 border border-purple-100 flex items-start gap-2.5 text-xs text-purple-800">
                            <svg class="w-4 h-4 text-purple-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Gunakan minimal 8 karakter dengan kombinasi huruf dan angka agar akun tetap aman.</span>
                        </div>

                        <form @submit.prevent="updatePassword" class="mt-4 space-y-4">
                            <!-- Current Password -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Saat Ini</label>
                                <input
                                    type="password"
                                    ref="currentPasswordInput"
                                    v-model="passwordForm.current_password"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-pink-500 focus:ring-4 focus:ring-pink-500/10 transition-all outline-none"
                                    required
                                    autocomplete="current-password"
                                />
                                <p v-if="passwordForm.errors.current_password" class="text-xs text-pink-600 mt-1 font-semibold">
                                    {{ passwordForm.errors.current_password }}
                                </p>
                            </div>

                            <!-- New Password -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                                <input
                                    type="password"
                                    ref="passwordInput"
                                    v-model="passwordForm.password"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-pink-500 focus:ring-4 focus:ring-pink-500/10 transition-all outline-none"
                                    required
                                    autocomplete="new-password"
                                />
                                <p v-if="passwordForm.errors.password" class="text-xs text-pink-600 mt-1 font-semibold">
                                    {{ passwordForm.errors.password }}
                                </p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                                <input
                                    type="password"
                                    v-model="passwordForm.password_confirmation"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-semibold focus:border-pink-500 focus:ring-4 focus:ring-pink-500/10 transition-all outline-none"
                                    required
                                    autocomplete="new-password"
                                />
                                <p v-if="passwordForm.errors.password_confirmation" class="text-xs text-pink-600 mt-1 font-semibold">
                                    {{ passwordForm.errors.password_confirmation }}
                                </p>
                            </div>

                            <div class="pt-3">
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="w-full py-3 px-6 rounded-2xl bg-gradient-to-r from-pink-500 to-indigo-600 text-white font-bold text-xs shadow-md shadow-pink-500/20 hover:shadow-lg hover:shadow-pink-500/30 hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50 flex items-center justify-center gap-2"
                                >
                                    <svg v-if="passwordForm.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span>Perbarui Kata Sandi</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
