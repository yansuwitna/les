<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    daftar_siswa: Object,
    nomor_siswa_berikutnya: String,
    filter: Object,
});

const search = ref(props.filter?.cari || '');

// Debounced search
let searchTimeout = null;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.siswa.index'), { cari: value }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 300);
});

// Modal state & form
const showingModal = ref(false);
const modalMode = ref('create');
const selectedSiswaId = ref(null);
const fotoPreview = ref(null);
const fileInput = ref(null);

const form = useForm({
    nama: '',
    nama_wali: '',
    nis: '',
    no_hp: '',
    alamat: '',
    kata_sandi: '',
    foto: null,
    aktif: true,
});

const onPhotoSelected = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.foto = file;
        fotoPreview.value = URL.createObjectURL(file);
    }
};

const removePhoto = () => {
    form.foto = null;
    fotoPreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    form.aktif = true;
    fotoPreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
    showingModal.value = true;
};

const openEditModal = (siswa) => {
    modalMode.value = 'edit';
    selectedSiswaId.value = siswa.id;
    form.nama = siswa.nama || '';
    form.nama_wali = siswa.nama_wali || '';
    form.nis = siswa.nis || '';
    form.no_hp = siswa.no_hp || '';
    form.alamat = siswa.alamat || '';
    form.aktif = siswa.aktif !== undefined ? Boolean(siswa.aktif) : true;
    form.kata_sandi = '';
    form.foto = null;
    fotoPreview.value = siswa.foto_url || null;
    if (fileInput.value) fileInput.value.value = '';
    form.clearErrors();
    showingModal.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('admin.siswa.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
                fotoPreview.value = null;
            },
        });
    } else {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.siswa.update', selectedSiswaId.value), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
                fotoPreview.value = null;
            },
        });
    }
};

const toggleSiswaStatus = (siswa) => {
    const action = siswa.aktif ? 'menonaktifkan' : 'mengaktifkan';
    const studentName = siswa.nama || 'siswa ini';
    if (typeof window !== 'undefined' && window.Swal) {
        window.Swal.fire({
            title: `Konfirmasi Status Akun`,
            text: `Apakah Anda yakin ingin ${action} akun siswa ${studentName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: siswa.aktif ? '#d33' : '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: `Ya, ${action}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.patch(route('admin.siswa.status', siswa.id), {}, {
                    preserveScroll: true,
                });
            }
        });
    } else if (confirm(`Apakah Anda yakin ingin ${action} akun siswa ${studentName}?`)) {
        router.patch(route('admin.siswa.status', siswa.id), {}, {
            preserveScroll: true,
        });
    }
};

const deleteSiswa = (siswa) => {
    if (confirm(`Yakin ingin menghapus data siswa ${siswa.nama} dari sistem? Akun nomor login 10 digit terkait juga akan dihapus.`)) {
        router.delete(route('admin.siswa.destroy', siswa.id));
    }
};

// Name tag state & download
const showingNameTagModal = ref(false);
const selectedSiswaNameTag = ref(null);
const isDownloading = ref(false);

const openNameTagModal = (siswa) => {
    selectedSiswaNameTag.value = siswa;
    showingNameTagModal.value = true;
};

const downloadNameTag = async () => {
    const cardElement = document.getElementById('nametag-siswa-printable');
    if (!cardElement) return;

    isDownloading.value = true;

    try {
        const siswa = selectedSiswaNameTag.value;
        const cleanName = (siswa?.nama || 'Siswa').toLowerCase().replace(/[^a-z0-9]/g, '_');

        if (window.html2canvas) {
            const canvas = await window.html2canvas(cardElement, {
                scale: 3,
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
            });

            const imageURL = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.download = `nametag_siswa_${cleanName}.png`;
            downloadLink.href = imageURL;
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        } else {
            await new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });

            const canvas = await window.html2canvas(cardElement, {
                scale: 3,
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
            });

            const imageURL = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.download = `nametag_siswa_${cleanName}.png`;
            downloadLink.href = imageURL;
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    } catch (error) {
        console.error('Gagal mengunduh name tag siswa:', error);
    } finally {
        isDownloading.value = false;
    }
};
</script>

<template>
    <Head :title="'Data Siswa & Wali - ' + ($page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">
                    Data Siswa & Wali Murid
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola data siswa, foto profil, nomor akun 10 digit otomatis, dan status keaktifan belajar</p>
            </div>
        </template>

        <div class="space-y-6">

            <!-- MAIN DATA CARD -->
            <div class="bg-white rounded-3xl border border-purple-100 shadow-xl shadow-purple-500/5 overflow-hidden">
                
                <!-- Table Header Controls -->
                <div class="p-6 border-b border-purple-50 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gradient-to-r from-pink-50/20 via-purple-50/20 to-white">
                    <div class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <TextInput
                            type="text"
                            class="pl-10 block w-full text-xs rounded-2xl bg-white border-purple-100 placeholder-slate-400 text-slate-700 focus:border-pink-500 focus:ring-2 focus:ring-pink-200 shadow-sm"
                            v-model="search"
                            placeholder="Cari nama siswa, nomor 10 digit, atau wali..."
                        />
                    </div>

                    <!-- Desktop / Tablet Add Button -->
                    <div class="hidden sm:flex items-center gap-3">
                        <button
                            @click="openCreateModal"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-pink-500 via-purple-600 to-indigo-600 hover:from-pink-600 hover:to-purple-700 shadow-md shadow-pink-500/25 transition-all transform hover:-translate-y-0.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Siswa Baru</span>
                        </button>
                    </div>
                </div>

                <!-- Bright Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-pink-50/40 text-[11px] font-black uppercase tracking-wider text-pink-950 border-b border-pink-100/60">
                            <tr>
                                <th class="px-6 py-4">Nama Siswa & Wali</th>
                                <th class="px-6 py-4">Nomor Siswa (10 Digit Login)</th>
                                <th class="px-6 py-4">NIS Sekolah</th>
                                <th class="px-6 py-4">Kontak & Domisili</th>
                                <th class="px-6 py-4 text-center">Status Akun</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-50">
                            <tr 
                                v-for="siswa in daftar_siswa.data" 
                                :key="siswa.id" 
                                :class="[
                                    siswa.aktif ? 'hover:bg-pink-50/30' : 'bg-slate-50/60 opacity-85 hover:bg-slate-100/50',
                                    'transition-colors duration-150 group'
                                ]"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="relative shrink-0">
                                            <img 
                                                v-if="siswa.foto_url" 
                                                :src="siswa.foto_url" 
                                                :alt="siswa.nama" 
                                                class="w-11 h-11 rounded-2xl object-cover border border-pink-200 shadow-sm"
                                            />
                                            <div 
                                                v-else 
                                                class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-pink-500 to-purple-600 flex items-center justify-center font-black text-sm text-white shadow-sm"
                                            >
                                                {{ (siswa.nama || 'S').charAt(0) }}
                                            </div>
                                            <span 
                                                :class="siswa.aktif ? 'bg-emerald-500' : 'bg-rose-500'" 
                                                class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 border-2 border-white rounded-full"
                                                :title="siswa.aktif ? 'Akun Aktif' : 'Akun Nonaktif'"
                                            ></span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 group-hover:text-pink-600 transition-colors flex items-center gap-1.5">
                                                <span>{{ siswa.nama }}</span>
                                                <span v-if="!siswa.aktif" class="text-[10px] px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 font-extrabold">
                                                    Nonaktif
                                                </span>
                                            </p>
                                            <p class="text-xs text-slate-500 font-medium">
                                                Wali: <span class="text-slate-700 font-semibold">{{ siswa.nama_wali || 'Wali Murid' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-pink-50 text-pink-700 border border-pink-100 font-mono text-xs font-bold shadow-sm">
                                        <svg class="w-3.5 h-3.5 text-pink-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        <span>{{ siswa.nomor_siswa }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700">
                                        {{ siswa.nis || '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="space-y-0.5">
                                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span>{{ siswa.no_hp || '-' }}</span>
                                        </p>
                                        <p class="text-[11px] text-slate-500 truncate max-w-xs font-medium" :title="siswa.alamat">
                                            {{ siswa.alamat || 'Alamat belum diatur' }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Status Column with Interactive Toggle Switch/Button -->
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        type="button"
                                        @click="toggleSiswaStatus(siswa)"
                                        :title="siswa.aktif ? 'Klik untuk menonaktifkan siswa ini' : 'Klik untuk mengaktifkan siswa ini'"
                                        class="group/btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wider transition-all duration-200 shadow-sm"
                                        :class="siswa.aktif 
                                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' 
                                            : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200'"
                                    >
                                        <span 
                                            class="w-2 h-2 rounded-full transition-transform group-hover/btn:scale-125"
                                            :class="siswa.aktif ? 'bg-emerald-500' : 'bg-rose-500'"
                                        ></span>
                                        <span>{{ siswa.aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                        <svg class="w-3 h-3 opacity-60 group-hover/btn:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            @click="openNameTagModal(siswa)"
                                            class="p-2 rounded-xl text-amber-600 hover:bg-amber-50 transition-colors"
                                            title="Cetak Name Tag Siswa"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </button>

                                        <button
                                            @click="openEditModal(siswa)"
                                            class="p-2 rounded-xl text-indigo-600 hover:bg-indigo-50 transition-colors"
                                            title="Ubah Data & Foto Siswa"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <button
                                            @click="deleteSiswa(siswa)"
                                            class="p-2 rounded-xl text-pink-600 hover:bg-pink-50 transition-colors"
                                            title="Hapus Siswa"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="daftar_siswa.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                                    <div class="w-16 h-16 rounded-3xl bg-pink-50 text-pink-600 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-base font-bold text-slate-800">Belum Ada Data Siswa</p>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                        Klik tombol "Tambah Siswa Baru" untuk mendaftarkan akun siswa dengan nomor login 10 digit otomatis.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="daftar_siswa.links && daftar_siswa.links.length > 3" class="p-4 border-t border-purple-50 flex flex-wrap items-center justify-center gap-1.5 bg-slate-50/50">
                    <template v-for="(link, idx) in daftar_siswa.links" :key="idx">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                link.active 
                                    ? 'bg-pink-600 text-white font-bold shadow-md shadow-pink-500/20' 
                                    : 'bg-white text-slate-600 hover:bg-pink-50 hover:text-pink-700 border border-slate-200/70',
                                'px-3.5 py-1.5 rounded-xl text-xs transition-all'
                            ]"
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="px-3.5 py-1.5 rounded-xl text-xs text-slate-400 bg-slate-100 cursor-not-allowed opacity-60"
                        />
                    </template>
                </div>

            </div>

        </div>

        <!-- MODAL FORM TAMBAH / UBAH SISWA -->
        <Modal :show="showingModal" @close="showingModal = false" max-width="2xl">
            <div class="p-6 sm:p-8 bg-white">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-pink-100">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">
                            {{ modalMode === 'create' ? 'Tambah Siswa Baru' : 'Ubah Data & Foto Siswa' }}
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            {{ modalMode === 'create' ? 'Sistem akan otomatis men-generate 10 digit Nomor Siswa unik sebagai username akun login.' : 'Perbarui biodata, foto profil, dan status keaktifan akun siswa.' }}
                        </p>
                    </div>
                    <button
                        @click="showingModal = false"
                        class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    
                    <!-- Foto & Status Header Box -->
                    <div class="p-5 rounded-2xl bg-pink-50/40 border border-pink-100 flex flex-col sm:flex-row items-center justify-between gap-5">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="relative shrink-0">
                                <img 
                                    v-if="fotoPreview" 
                                    :src="fotoPreview" 
                                    alt="Preview Foto Siswa" 
                                    class="w-16 h-16 rounded-2xl object-cover border-2 border-pink-300 shadow-md"
                                />
                                <div 
                                    v-else 
                                    class="w-16 h-16 rounded-2xl bg-pink-100 text-pink-700 flex items-center justify-center font-black text-xl border-2 border-pink-300"
                                >
                                    <svg class="w-8 h-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="foto_siswa" value="Foto Siswa" class="text-xs font-bold text-slate-800" />
                                <input 
                                    type="file" 
                                    id="foto_siswa"
                                    ref="fileInput"
                                    accept="image/png, image/jpeg, image/jpg, image/webp"
                                    @change="onPhotoSelected"
                                    class="hidden"
                                />
                                <div class="flex items-center gap-2 mt-1.5">
                                    <button 
                                        type="button"
                                        @click="fileInput.click()"
                                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white text-pink-700 border border-pink-200 hover:bg-pink-100 shadow-sm transition-all"
                                    >
                                        {{ fotoPreview ? 'Ganti Foto' : 'Pilih Foto' }}
                                    </button>
                                    <button 
                                        v-if="fotoPreview"
                                        type="button"
                                        @click="removePhoto"
                                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition-all"
                                    >
                                        Hapus
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                                <InputError class="mt-1" :message="form.errors.foto" />
                            </div>
                        </div>

                        <!-- Status Aktif Switch -->
                        <div class="flex items-center gap-3 bg-white px-4 py-2.5 rounded-2xl border border-pink-100 shadow-sm w-full sm:w-auto justify-between sm:justify-start">
                            <span class="text-xs font-bold text-slate-700">Status Akun:</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    v-model="form.aktif" 
                                    class="sr-only peer"
                                />
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ml-2 text-xs font-bold" :class="form.aktif ? 'text-emerald-700' : 'text-slate-400'">
                                    {{ form.aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Otomatis Nomor Siswa Info Banner (Hanya saat create) -->
                    <div v-if="modalMode === 'create'" class="p-4 rounded-2xl bg-gradient-to-r from-pink-50 via-purple-50 to-indigo-50 border border-pink-200 flex items-center gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-pink-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800">
                                Nomor Siswa 10 Digit Otomatis
                            </p>
                            <p class="text-[11px] text-slate-600 mt-0.5">
                                Perkiraan nomor login berikutnya: <span class="font-mono font-bold text-pink-700 bg-white px-2 py-0.5 rounded-lg border border-pink-200">{{ nomor_siswa_berikutnya }}</span> (Kata sandi bawaan sama dengan nomor siswa jika tidak ditentukan).
                            </p>
                        </div>
                    </div>

                    <!-- Identitas & Wali Box -->
                    <div class="p-5 rounded-2xl bg-pink-50/30 border border-pink-100 space-y-4">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-pink-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Identitas Siswa & Wali Murid</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="nama" value="Nama Lengkap Siswa" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="nama"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200"
                                    v-model="form.nama"
                                    placeholder="Contoh: Ananda Bintang Pratama"
                                    required
                                />
                                <InputError class="mt-1" :message="form.errors.nama" />
                            </div>

                            <div>
                                <InputLabel for="nama_wali" value="Nama Orang Tua / Wali" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="nama_wali"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200"
                                    v-model="form.nama_wali"
                                    placeholder="Contoh: Bapak Hendra Kusuma"
                                />
                                <InputError class="mt-1" :message="form.errors.nama_wali" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="nis" value="NIS / No. Induk Sekolah" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="nis"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200"
                                    v-model="form.nis"
                                    placeholder="Contoh: 2024101"
                                />
                                <InputError class="mt-1" :message="form.errors.nis" />
                            </div>

                            <div>
                                <InputLabel for="no_hp" value="No. WhatsApp Orang Tua / Wali" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="no_hp"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200"
                                    v-model="form.no_hp"
                                    placeholder="Contoh: 08123456789"
                                />
                                <InputError class="mt-1" :message="form.errors.no_hp" />
                            </div>
                        </div>

                        <div>
                            <InputLabel 
                                for="kata_sandi" 
                                :value="modalMode === 'create' ? 'Kata Sandi Masuk (Opsional)' : 'Kata Sandi Baru (Opsional)'" 
                                class="text-xs font-bold text-slate-700" 
                            />
                            <TextInput
                                id="kata_sandi"
                                type="password"
                                class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200"
                                v-model="form.kata_sandi"
                                :placeholder="modalMode === 'create' ? 'Bawaan: sama dengan nomor siswa' : 'Kosongkan jika tidak diubah'"
                            />
                            <InputError class="mt-1" :message="form.errors.kata_sandi" />
                        </div>

                        <div>
                            <InputLabel for="alamat" value="Alamat Domisili Siswa" class="text-xs font-bold text-slate-700" />
                            <textarea
                                id="alamat"
                                rows="2"
                                class="mt-1.5 block w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:border-pink-500 focus:ring-2 focus:ring-pink-200 p-3"
                                v-model="form.alamat"
                                placeholder="Masukkan alamat domisili lengkap siswa..."
                            ></textarea>
                            <InputError class="mt-1" :message="form.errors.alamat" />
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="showingModal = false"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-pink-500 via-purple-600 to-indigo-600 hover:from-pink-600 hover:to-purple-700 shadow-md shadow-pink-500/20 transition-all transform hover:-translate-y-0.5 disabled:opacity-50"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Data Siswa' }}</span>
                        </button>
                    </div>

                </form>
            </div>
        </Modal>

        <!-- MODAL NAME TAG SISWA -->
        <Modal :show="showingNameTagModal" @close="showingNameTagModal = false" max-width="2xl">
            <div class="p-6 bg-white">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-pink-100">
                    <div>
                        <h3 class="text-base font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-pink-500"></span>
                            Preview Name Tag Siswa (Landscape)
                        </h3>
                        <p class="text-xs text-slate-500 font-medium">Download kartu pengenal siswa resmi format landscape</p>
                    </div>
                    <button
                        @click="showingNameTagModal = false"
                        class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- PREVIEW CONTAINER & PRINTABLE AREA -->
                <div class="py-6 px-4 flex justify-center items-center bg-slate-50 rounded-2xl border border-dashed border-pink-200 overflow-x-auto">
                    <!-- The Printable Card (Landscape) -->
                    <div 
                        id="nametag-siswa-printable"
                        class="w-[500px] h-[290px] bg-white rounded-3xl border-2 border-pink-200 shadow-2xl overflow-hidden relative flex flex-col justify-between"
                        style="min-width: 500px; max-width: 500px; height: 290px;"
                    >
                        <!-- Top Header Bar -->
                        <div class="bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 px-6 py-3.5 text-white flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center font-black text-xs text-white">
                                    {{ ($page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'LC').substring(0, 2).toUpperCase() }}
                                </div>
                                <span class="font-black text-sm tracking-tight drop-shadow-sm">
                                    {{ $page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria' }}
                                </span>
                            </div>
                            <span class="text-[10px] uppercase tracking-widest text-amber-200 font-extrabold px-2.5 py-0.5 rounded-full bg-black/15 border border-white/20">
                                KARTU SISWA
                            </span>
                        </div>

                        <!-- Card Body (Horizontal 2 Columns) -->
                        <div class="px-6 py-4 flex items-center gap-5 flex-1">
                            <!-- Left: Photo Box -->
                            <div class="flex flex-col items-center shrink-0">
                                <div class="w-24 h-28 rounded-2xl bg-white p-1 shadow-md border-2 border-pink-200 overflow-hidden flex items-center justify-center">
                                    <img 
                                        v-if="selectedSiswaNameTag?.foto_url" 
                                        :src="selectedSiswaNameTag.foto_url" 
                                        :alt="selectedSiswaNameTag?.nama"
                                        class="w-full h-full object-cover rounded-xl"
                                    />
                                    <div 
                                        v-else 
                                        class="w-full h-full bg-gradient-to-tr from-pink-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-black text-3xl"
                                    >
                                        {{ (selectedSiswaNameTag?.nama || 'S').charAt(0) }}
                                    </div>
                                </div>
                                <span class="mt-2 text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Terdaftar
                                </span>
                            </div>

                            <!-- Right: Student Information Details -->
                            <div class="flex-1 min-w-0 space-y-2 text-left">
                                <div>
                                    <h4 class="font-black text-lg text-slate-900 leading-tight truncate">
                                        {{ selectedSiswaNameTag?.nama || 'Nama Siswa' }}
                                    </h4>
                                    <p class="text-xs font-bold text-pink-700">
                                        Siswa Bimbingan Belajar
                                    </p>
                                </div>

                                <div class="py-2 px-3 bg-pink-50/60 rounded-2xl border border-pink-100/80 space-y-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">No. Siswa</span>
                                        <span class="font-mono font-black text-pink-700 tracking-wider text-xs">{{ selectedSiswaNameTag?.nomor_siswa || '-' }}</span>
                                    </div>
                                    <div v-if="selectedSiswaNameTag?.nis" class="flex items-center justify-between">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">NIS</span>
                                        <span class="font-mono font-bold text-slate-800 text-xs">{{ selectedSiswaNameTag.nis }}</span>
                                    </div>
                                    <div v-if="selectedSiswaNameTag?.nama_wali" class="flex items-center justify-between">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">Wali</span>
                                        <span class="font-bold text-slate-800 truncate max-w-[170px] text-xs">{{ selectedSiswaNameTag.nama_wali }}</span>
                                    </div>
                                    <div v-if="selectedSiswaNameTag?.no_hp" class="flex items-center justify-between">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">Kontak</span>
                                        <span class="font-bold text-slate-800 text-xs">{{ selectedSiswaNameTag.no_hp }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Footer Strip -->
                        <div class="h-2.5 bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600"></div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        @click="showingNameTagModal = false"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors"
                    >
                        Tutup
                    </button>
                    <button
                        type="button"
                        :disabled="isDownloading"
                        @click="downloadNameTag"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 hover:from-pink-700 hover:to-indigo-700 shadow-md shadow-pink-500/20 transition-all transform hover:-translate-y-0.5 disabled:opacity-50"
                    >
                        <svg v-if="isDownloading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>{{ isDownloading ? 'Menyiapkan Gambar...' : 'Download' }}</span>
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Mobile Floating Action Button (FAB) -->
        <button
            @click="openCreateModal"
            class="lg:hidden fixed bottom-6 right-6 z-40 w-14 h-14 rounded-full bg-gradient-to-tr from-pink-500 via-purple-600 to-indigo-600 text-white shadow-2xl shadow-pink-600/50 flex items-center justify-center transform active:scale-95 transition-all border-2 border-white"
            title="Tambah Siswa Baru"
            aria-label="Tambah Siswa Baru"
        >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
        </button>

    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #nametag-siswa-printable,
    #nametag-siswa-printable * {
        visibility: visible !important;
    }
    #nametag-siswa-printable {
        position: fixed !important;
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
        box-shadow: none !important;
        border: 1.5px solid #f472b6 !important;
        margin: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
