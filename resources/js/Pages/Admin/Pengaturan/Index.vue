<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    pengaturan: Object,
});

const logoPreview = ref(props.pengaturan?.logo_url || null);
const slidePreview = ref(props.pengaturan?.slide_url || null);
const logoInput = ref(null);
const slideInput = ref(null);

const form = useForm({
    nama_les: props.pengaturan?.nama_les || 'Les Ceria',
    alamat_les: props.pengaturan?.alamat_les || '',
    kontak_les: props.pengaturan?.kontak_les || '',
    logo_les: null,
    slide_les: null,
});

const onLogoSelected = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo_les = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const onSlideSelected = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.slide_les = file;
        slidePreview.value = URL.createObjectURL(file);
    }
};

const removeLogo = () => {
    form.logo_les = null;
    logoPreview.value = null;
    if (logoInput.value) logoInput.value.value = '';
};

const removeSlide = () => {
    form.slide_les = null;
    slidePreview.value = null;
    if (slideInput.value) slideInput.value.value = '';
};

const submitForm = () => {
    form.post(route('admin.identitas.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="'Identitas & Web - ' + ($page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">
                    Identitas Lembaga & Website
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola identitas resmi lembaga bimbingan belajar, kontak publik, logo, dan banner slide promo</p>
            </div>
        </template>

        <div class="max-w-5xl space-y-6">

            <!-- Card Form & Live Preview -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left: Form Controls -->
                <div class="lg:col-span-7 bg-white rounded-3xl border border-purple-100 shadow-xl shadow-purple-500/5 p-6 sm:p-8">
                    <form @submit.prevent="submitForm" class="space-y-6">

                        <!-- Brand Header -->
                        <div class="border-b border-purple-50 pb-4">
                            <h2 class="text-base font-black text-slate-900">Profil & Kontak Lembaga</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Informasi ini akan ditampilkan pada halaman depan pengunjung dan laporan hasil belajar.</p>
                        </div>

                        <!-- Nama Les -->
                        <div>
                            <InputLabel for="nama_les" value="Nama Lembaga Bimbingan Belajar *" class="text-xs font-bold text-slate-700" />
                            <TextInput
                                id="nama_les"
                                type="text"
                                class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                v-model="form.nama_les"
                                placeholder="Contoh: Les Ceria Bimbel Unggul"
                                required
                            />
                            <InputError class="mt-1" :message="form.errors.nama_les" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Kontak WhatsApp / HP -->
                            <div>
                                <InputLabel for="kontak_les" value="Nomor Kontak WhatsApp / Telp" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="kontak_les"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                    v-model="form.kontak_les"
                                    placeholder="Contoh: 0812-3456-7890"
                                />
                                <InputError class="mt-1" :message="form.errors.kontak_les" />
                            </div>

                            <!-- Alamat Kantor -->
                            <div>
                                <InputLabel for="alamat_les" value="Alamat Kantor / Pusat Belajar" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="alamat_les"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                    v-model="form.alamat_les"
                                    placeholder="Contoh: Jl. Pendidikan No. 1, Kota Belajar"
                                />
                                <InputError class="mt-1" :message="form.errors.alamat_les" />
                            </div>
                        </div>

                        <!-- Media Box: Logo & Slide -->
                        <div class="p-5 rounded-2xl bg-purple-50/40 border border-purple-100 space-y-5">
                            <h3 class="text-xs font-black uppercase tracking-wider text-purple-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Logo & Media Visual Website</span>
                            </h3>

                            <!-- Upload Logo Les -->
                            <div>
                                <InputLabel for="logo_les" value="Logo Resmi Lembaga" class="text-xs font-bold text-slate-700" />
                                <input
                                    type="file"
                                    id="logo_les"
                                    ref="logoInput"
                                    accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml"
                                    @change="onLogoSelected"
                                    class="hidden"
                                />
                                <div class="flex items-center gap-4 mt-2">
                                    <div class="w-14 h-14 rounded-2xl border-2 border-purple-200 bg-white flex items-center justify-center overflow-hidden shrink-0 shadow-sm p-1">
                                        <img v-if="logoPreview" :src="logoPreview" alt="Logo Preview" class="w-full h-full object-contain" />
                                        <div v-else class="text-purple-600 font-black text-xl">L</div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="logoInput.click()"
                                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white text-purple-700 border border-purple-200 hover:bg-purple-100 shadow-sm transition-all"
                                            >
                                                {{ logoPreview ? 'Ganti Logo' : 'Upload Logo' }}
                                            </button>
                                            <button
                                                v-if="logoPreview"
                                                type="button"
                                                @click="removeLogo"
                                                class="px-2.5 py-1.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-all"
                                            >
                                                Batal
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-slate-400">Rekomendasi format PNG/SVG transparan. Maks 2MB.</p>
                                    </div>
                                </div>
                                <InputError class="mt-1" :message="form.errors.logo_les" />
                            </div>

                            <!-- Upload Gambar Halaman Home -->
                            <div>
                                <InputLabel for="slide_les" value="Gambar Utama Halaman Home (Banner / Foto)" class="text-xs font-bold text-slate-700" />
                                <input
                                    type="file"
                                    id="slide_les"
                                    ref="slideInput"
                                    accept="image/png, image/jpeg, image/jpg, image/webp"
                                    @change="onSlideSelected"
                                    class="hidden"
                                />
                                <div class="mt-2 space-y-2">
                                    <div v-if="slidePreview" class="relative rounded-2xl border-2 border-purple-200 overflow-hidden max-h-48 bg-slate-900 shadow-md">
                                        <img :src="slidePreview" alt="Slide Preview" class="w-full h-full object-cover" />
                                        <button
                                            type="button"
                                            @click="removeSlide"
                                            class="absolute top-2 right-2 px-2 py-1 rounded-lg bg-black/60 text-white text-[10px] font-bold hover:bg-black/80 transition-all"
                                        >
                                            Ganti / Batal
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="slideInput.click()"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white text-purple-700 border border-purple-200 hover:bg-purple-100 shadow-sm transition-all"
                                        >
                                            {{ slidePreview ? 'Ganti Gambar Home' : 'Upload Gambar Home' }}
                                        </button>
                                        <span class="text-[10px] text-slate-400">Gambar yang tampil di halaman depan (Home). Rasio 4:3 atau 16:9. Maks 4MB.</span>
                                    </div>
                                </div>
                                <InputError class="mt-1" :message="form.errors.slide_les" />
                            </div>
                        </div>

                        <!-- Action Submit -->
                        <div class="flex items-center justify-end pt-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-lg shadow-purple-500/25 transition-all transform hover:-translate-y-0.5 disabled:opacity-50"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Menyimpan Perubahan...' : 'Simpan Identitas & Website' }}</span>
                            </button>
                        </div>

                    </form>
                </div>

                <!-- Right: Cheerful Live Brand Preview -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <div class="bg-gradient-to-br from-purple-600 via-indigo-600 to-pink-500 rounded-3xl p-6 sm:p-7 text-white shadow-xl shadow-purple-500/15 relative overflow-hidden">
                        <div class="absolute -top-16 -right-16 w-40 h-40 bg-white/10 rounded-full blur-xl"></div>
                        <div class="absolute -bottom-16 -left-16 w-40 h-40 bg-pink-300/20 rounded-full blur-xl"></div>

                        <div class="relative z-10 space-y-5">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[11px] font-extrabold text-amber-200">
                                <span>Pratinjau Tampilan Publik</span>
                            </div>

                            <!-- Header Bar Mockup -->
                            <div class="p-3.5 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-sm overflow-hidden p-1">
                                        <img v-if="logoPreview" :src="logoPreview" alt="Logo" class="w-full h-full object-contain" />
                                        <span v-else class="text-purple-700 font-black text-sm">L</span>
                                    </div>
                                    <div>
                                        <p class="font-black text-sm tracking-tight text-white leading-tight">
                                            {{ form.nama_les || 'Les Ceria' }}
                                        </p>
                                        <span class="text-[9px] uppercase font-bold text-amber-200 tracking-wider">
                                            Bimbingan Belajar Unggul
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Banner Card Mockup -->
                            <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 overflow-hidden">
                                <div v-if="slidePreview" class="h-32 w-full overflow-hidden">
                                    <img :src="slidePreview" alt="Slide" class="w-full h-full object-cover" />
                                </div>
                                <div v-else class="h-28 w-full bg-gradient-to-tr from-purple-800/60 to-pink-600/60 flex items-center justify-center text-center p-4">
                                    <p class="text-xs font-bold text-purple-100">Banner Slide Promo Belum Diupload</p>
                                </div>
                                <div class="p-4 space-y-2">
                                    <h4 class="font-black text-sm text-white">
                                        Bermain, Belajar & Raih Prestasi Bersama {{ form.nama_les || 'Les Ceria' }}
                                    </h4>
                                    <p class="text-[11px] text-purple-100 line-clamp-2">
                                        Pendampingan belajar privat dan semi-privat berkarakter unggul dengan laporan langsung ke wali murid.
                                    </p>
                                </div>
                            </div>

                            <!-- Footer Mockup -->
                            <div class="p-4 rounded-2xl bg-slate-950/40 border border-white/10 space-y-2 text-[11px]">
                                <p class="text-slate-300 font-bold uppercase tracking-wider text-[10px]">Kontak & Alamat</p>
                                <p class="text-white flex items-center gap-1.5">
                                    <span class="text-purple-300">WA:</span> {{ form.kontak_les || '0812-3456-7890' }}
                                </p>
                                <p class="text-slate-300 leading-snug">
                                    <span class="text-purple-300">Alamat:</span> {{ form.alamat_les || 'Jl. Pendidikan No. 1, Kota Belajar' }}
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </AuthenticatedLayout>
</template>
