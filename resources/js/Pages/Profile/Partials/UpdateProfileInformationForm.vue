<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const role = computed(() => user.value?.peran || user.value?.role || 'admin');

const fotoPreview = ref(user.value?.foto_url || null);
const fotoInput = ref(null);

const form = useForm({
    nama: user.value?.nama || user.value?.name || '',
    name: user.value?.name || user.value?.nama || '',
    username: user.value?.username || '',
    email: user.value?.email || '',
    foto: null,
    telepon: user.value?.telepon || '',
    nama_wali: user.value?.nama_wali || '',
    telepon_wali: user.value?.telepon_wali || '',
});

const handleFotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.foto = file;
        fotoPreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.foto = null;
        },
    });
};
</script>

<template>
    <section>
        <header class="flex items-center gap-4">
            <!-- Avatar Preview with Click to Change -->
            <div class="relative group cursor-pointer shrink-0" @click="fotoInput?.click()">
                <div v-if="fotoPreview" class="w-16 h-16 rounded-2xl overflow-hidden border-2 border-purple-200 shadow-md bg-white p-0.5">
                    <img :src="fotoPreview" :alt="user?.nama" class="w-full h-full object-cover rounded-[14px]" />
                </div>
                <div v-else class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-purple-600 to-pink-500 flex items-center justify-center text-xl font-black text-white shadow-md">
                    {{ (user?.nama || user?.name || 'A').charAt(0).toUpperCase() }}
                </div>
                <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-purple-600 text-white border-2 border-white shadow flex items-center justify-center" title="Ganti Foto">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    </svg>
                </span>
            </div>

            <div>
                <h2 class="text-lg font-black text-slate-800 tracking-tight">
                    Informasi Profil & Akun
                </h2>
                <p class="text-xs text-slate-500 font-medium">
                    Perbarui nama lengkap, username login, dan foto profil akun Anda.
                </p>
            </div>
        </header>

        <form @submit.prevent="submitForm" class="mt-6 space-y-5">
            <!-- Foto Profil File Input -->
            <div>
                <InputLabel for="foto" value="Foto Profil" />
                <div class="flex items-center gap-3 mt-1">
                    <input
                        id="foto"
                        type="file"
                        ref="fotoInput"
                        @change="handleFotoChange"
                        accept="image/png, image/jpeg, image/jpg, image/webp"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer"
                    />
                    <button 
                        v-if="form.foto" 
                        type="button" 
                        @click="form.foto = null; fotoPreview = user?.foto_url || null; if (fotoInput) fotoInput.value = ''"
                        class="p-2 rounded-xl text-pink-600 hover:bg-pink-50 text-xs font-bold shrink-0"
                    >
                        Batal
                    </button>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Format JPG, PNG atau WebP (Maksimal 2MB).</span>
                <InputError class="mt-1" :message="form.errors.foto" />
            </div>


            <!-- Nama Lengkap -->
            <div>
                <InputLabel for="nama" value="Nama Lengkap" />
                <TextInput
                    id="nama"
                    type="text"
                    class="mt-1 block w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500"
                    v-model="form.nama"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Masukkan nama lengkap"
                />
                <InputError class="mt-2" :message="form.errors.nama || form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Alamat Email (Opsional)" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500"
                    v-model="form.email"
                    autocomplete="email"
                    placeholder="admin@contoh.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Tombol Simpan -->
            <div class="flex items-center gap-4 pt-2">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="rounded-2xl px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-500 font-bold text-xs shadow-md shadow-purple-500/20"
                >
                    Simpan Perubahan
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-xs font-bold text-emerald-600">
                        Tersimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
