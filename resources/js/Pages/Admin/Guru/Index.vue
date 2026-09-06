<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    daftar_guru: Object,
    teachers: Object,
    filters: Object,
});

// Support both prop keys
const teachersData = computed(() => props.daftar_guru || props.teachers || { data: [], links: [] });

const search = ref(props.filters?.cari || props.filters?.search || '');

// Debounced search
let searchTimeout = null;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.guru.index'), { cari: value }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 300);
});

// Modal state & form
const showingModal = ref(false);
const modalMode = ref('create');
const selectedGuruId = ref(null);
const fotoPreview = ref(null);
const fileInput = ref(null);

const form = useForm({
    nama: '',
    nik: '',
    email: '',
    kata_sandi: '',
    nip: '',
    no_hp: '',
    alamat: '',
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

const openEditModal = (teacher) => {
    modalMode.value = 'edit';
    selectedGuruId.value = teacher.id;
    form.nama = teacher.nama || teacher.pengguna?.nama || '';
    form.nik = teacher.nik || '';
    form.email = teacher.email || '';
    form.kata_sandi = '';
    form.nip = teacher.nip || '';
    form.no_hp = teacher.no_hp || '';
    form.alamat = teacher.alamat || '';
    form.aktif = teacher.aktif !== undefined ? Boolean(teacher.aktif) : true;
    form.foto = null;
    fotoPreview.value = teacher.foto_url || null;
    if (fileInput.value) fileInput.value.value = '';
    form.clearErrors();
    showingModal.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('admin.guru.store'), {
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
        })).post(route('admin.guru.update', selectedGuruId.value), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
                fotoPreview.value = null;
            },
        });
    }
};

const toggleGuruStatus = (teacher) => {
    const action = teacher.aktif ? 'menonaktifkan' : 'mengaktifkan';
    const guruName = teacher.nama || teacher.pengguna?.nama || 'guru ini';
    if (typeof window !== 'undefined' && window.Swal) {
        window.Swal.fire({
            title: `Konfirmasi Status Akun`,
            text: `Apakah Anda yakin ingin ${action} akun guru ${guruName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: teacher.aktif ? '#d33' : '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: `Ya, ${action}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.patch(route('admin.guru.status', teacher.id), {}, {
                    preserveScroll: true,
                });
            }
        });
    } else if (confirm(`Apakah Anda yakin ingin ${action} akun guru ${guruName}?`)) {
        router.patch(route('admin.guru.status', teacher.id), {}, {
            preserveScroll: true,
        });
    }
};


const deleteTeacher = (teacher) => {
    const guruName = teacher.nama || teacher.pengguna?.nama || 'guru ini';
    if (confirm('Yakin ingin menghapus data guru ' + guruName + ' dari sistem? Semua data jadwal terkait juga akan terpengaruh.')) {
        router.delete(route('admin.guru.destroy', teacher.id));
    }
};

// Name tag state & download
const showingNameTagModal = ref(false);
const selectedGuruNameTag = ref(null);
const isDownloading = ref(false);

const openNameTagModal = (teacher) => {
    selectedGuruNameTag.value = teacher;
    showingNameTagModal.value = true;
};

const downloadNameTag = async () => {
    const cardElement = document.getElementById('nametag-printable-area');
    if (!cardElement) return;

    isDownloading.value = true;

    try {
        const teacher = selectedGuruNameTag.value;
        const cleanName = (teacher?.nama || 'Guru').toLowerCase().replace(/[^a-z0-9]/g, '_');

        if (window.html2canvas) {
            const canvas = await window.html2canvas(cardElement, {
                scale: 3, // High resolution
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
            });

            const imageURL = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.download = `nametag_${cleanName}.png`;
            downloadLink.href = imageURL;
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        } else {
            // Fallback: dynamic load script if not ready
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
            downloadLink.download = `nametag_${cleanName}.png`;
            downloadLink.href = imageURL;
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    } catch (error) {
        console.error('Gagal mengunduh name tag:', error);
    } finally {
        isDownloading.value = false;
    }
};
</script>

<template>
    <Head :title="'Data Guru & Pengajar - ' + ($page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">
                    Data Pengajar & Guru
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola akun instruktur, foto profil, NIK login resmi, dan status keaktifan pengajaran</p>
            </div>
        </template>

        <div class="space-y-6">

            <!-- MAIN DATA CARD -->
            <div class="bg-white rounded-3xl border border-purple-100 shadow-xl shadow-purple-500/5 overflow-hidden">
                
                <!-- Table Header Controls -->
                <div class="p-6 border-b border-purple-50 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gradient-to-r from-purple-50/20 to-pink-50/10">
                    <div class="relative w-full sm:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <TextInput
                            type="text"
                            class="pl-10 block w-full text-xs rounded-2xl bg-white border-purple-100 placeholder-slate-400 text-slate-700 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 shadow-sm"
                            v-model="search"
                            placeholder="Cari nama, NIK, atau NIP guru..."
                        />
                    </div>

                    <!-- Desktop / Tablet Add Button -->
                    <div class="hidden sm:flex items-center gap-3">
                        <button
                            @click="openCreateModal"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-md shadow-purple-500/25 transition-all transform hover:-translate-y-0.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Guru Baru</span>
                        </button>
                    </div>
                </div>

                <!-- Bright Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-purple-50/50 text-[11px] font-black uppercase tracking-wider text-purple-900 border-b border-purple-50">
                            <tr>
                                <th class="px-6 py-4">Guru / Pengajar</th>
                                <th class="px-6 py-4">NIK (Username Login)</th>
                                <th class="px-6 py-4">NIP / Kode</th>
                                <th class="px-6 py-4">Kontak & Domisili</th>
                                <th class="px-6 py-4 text-center">Status Akun</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-50">
                            <tr 
                                v-for="teacher in teachersData.data" 
                                :key="teacher.id" 
                                :class="[
                                    teacher.aktif ? 'hover:bg-purple-50/40' : 'bg-slate-50/60 opacity-85 hover:bg-slate-100/50',
                                    'transition-colors duration-150 group'
                                ]"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="relative shrink-0">
                                            <img 
                                                v-if="teacher.foto_url" 
                                                :src="teacher.foto_url" 
                                                :alt="teacher.nama" 
                                                class="w-11 h-11 rounded-2xl object-cover border border-purple-200 shadow-sm"
                                            />
                                            <div 
                                                v-else 
                                                class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-purple-500 to-pink-500 flex items-center justify-center font-black text-sm text-white shadow-sm"
                                            >
                                                {{ (teacher.nama || 'G').charAt(0) }}
                                            </div>
                                            <span 
                                                :class="teacher.aktif ? 'bg-emerald-500' : 'bg-rose-500'" 
                                                class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 border-2 border-white rounded-full"
                                                :title="teacher.aktif ? 'Akun Aktif' : 'Akun Nonaktif'"
                                            ></span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 group-hover:text-purple-700 transition-colors flex items-center gap-1.5">
                                                <span>{{ teacher.nama }}</span>
                                                <span v-if="!teacher.aktif" class="text-[10px] px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 font-extrabold">
                                                    Nonaktif
                                                </span>
                                            </p>
                                            <p class="text-xs text-slate-500 font-medium">
                                                {{ teacher.email || 'Email belum diisi' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-purple-50 text-purple-700 border border-purple-100 font-mono text-xs font-bold">
                                        <svg class="w-3.5 h-3.5 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        <span>{{ teacher.nik || '-' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700">
                                        {{ teacher.nip || '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="space-y-0.5">
                                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span>{{ teacher.no_hp || '-' }}</span>
                                        </p>
                                        <p class="text-[11px] text-slate-500 truncate max-w-xs font-medium" :title="teacher.alamat">
                                            {{ teacher.alamat || 'Alamat belum diatur' }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Status Column with Interactive Toggle Switch/Button -->
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        type="button"
                                        @click="toggleGuruStatus(teacher)"
                                        :title="teacher.aktif ? 'Klik untuk menonaktifkan guru ini' : 'Klik untuk mengaktifkan guru ini'"
                                        class="group/btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wider transition-all duration-200 shadow-sm"
                                        :class="teacher.aktif 
                                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' 
                                            : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200'"
                                    >
                                        <span 
                                            class="w-2 h-2 rounded-full transition-transform group-hover/btn:scale-125"
                                            :class="teacher.aktif ? 'bg-emerald-500' : 'bg-rose-500'"
                                        ></span>
                                        <span>{{ teacher.aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                        <svg class="w-3 h-3 opacity-60 group-hover/btn:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            @click="openNameTagModal(teacher)"
                                            class="p-2 rounded-xl text-amber-600 hover:bg-amber-50 transition-colors"
                                            title="Cetak Name Tag Guru"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </button>

                                        <button
                                            @click="openEditModal(teacher)"
                                            class="p-2 rounded-xl text-indigo-600 hover:bg-indigo-50 transition-colors"
                                            title="Ubah Data & Foto Guru"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <button
                                            @click="deleteTeacher(teacher)"
                                            class="p-2 rounded-xl text-pink-600 hover:bg-pink-50 transition-colors"
                                            title="Hapus Guru"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="teachersData.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                                    <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-600 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-base font-bold text-slate-800">Belum Ada Data Guru</p>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                        Klik tombol "Tambah Guru Baru" untuk mendaftarkan akun pengajar dengan NIK resmi.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="teachersData.links && teachersData.links.length > 3" class="p-4 border-t border-purple-50 flex flex-wrap items-center justify-center gap-1.5 bg-slate-50/50">
                    <template v-for="(link, idx) in teachersData.links" :key="idx">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                link.active 
                                    ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' 
                                    : 'bg-white text-slate-600 hover:bg-purple-50 hover:text-purple-700 border border-slate-200/70',
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

        <!-- MODAL FORM TAMBAH / UBAH GURU -->
        <Modal :show="showingModal" @close="showingModal = false" max-width="2xl">
            <div class="p-6 sm:p-8 bg-white">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-purple-100">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">
                            {{ modalMode === 'create' ? 'Tambah Guru Baru' : 'Ubah Data & Foto Guru' }}
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            {{ modalMode === 'create' ? 'Daftarkan instruktur pengajar dengan NIK 16 digit dan foto profil.' : 'Perbarui biodata, foto profil, dan status keaktifan akun guru.' }}
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
                    <div class="p-5 rounded-2xl bg-purple-50/40 border border-purple-100 flex flex-col sm:flex-row items-center justify-between gap-5">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="relative shrink-0">
                                <img 
                                    v-if="fotoPreview" 
                                    :src="fotoPreview" 
                                    alt="Preview Foto Guru" 
                                    class="w-16 h-16 rounded-2xl object-cover border-2 border-purple-300 shadow-md"
                                />
                                <div 
                                    v-else 
                                    class="w-16 h-16 rounded-2xl bg-purple-200 text-purple-700 flex items-center justify-center font-black text-xl border-2 border-purple-300"
                                >
                                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="foto" value="Foto Guru / Instruktur" class="text-xs font-bold text-slate-800" />
                                <input 
                                    type="file" 
                                    id="foto"
                                    ref="fileInput"
                                    accept="image/png, image/jpeg, image/jpg, image/webp"
                                    @change="onPhotoSelected"
                                    class="hidden"
                                />
                                <div class="flex items-center gap-2 mt-1.5">
                                    <button 
                                        type="button"
                                        @click="fileInput.click()"
                                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white text-purple-700 border border-purple-200 hover:bg-purple-100 shadow-sm transition-all"
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
                        <div class="flex items-center gap-3 bg-white px-4 py-2.5 rounded-2xl border border-purple-100 shadow-sm w-full sm:w-auto justify-between sm:justify-start">
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

                    <!-- Akun Login Box -->
                    <div class="p-5 rounded-2xl bg-purple-50/50 border border-purple-100 space-y-4">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Kredensial Akun Login (Menggunakan NIK)</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="nama" value="Nama Lengkap Guru" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="nama"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                    v-model="form.nama"
                                    placeholder="Contoh: Ibu Rina Amalia, S.Pd."
                                    required
                                />
                                <InputError class="mt-1" :message="form.errors.nama" />
                            </div>

                            <div>
                                <InputLabel for="nik" value="NIK (Nomor Induk Kependudukan - 16 Digit)" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="nik"
                                    type="text"
                                    maxlength="16"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 font-mono"
                                    v-model="form.nik"
                                    placeholder="16 digit NIK (contoh: 5102010101900001)"
                                    required
                                />
                                <InputError class="mt-1" :message="form.errors.nik" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="email" value="Alamat Email (Opsional)" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                    v-model="form.email"
                                    placeholder="guru@email.com"
                                />
                                <InputError class="mt-1" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel 
                                    for="kata_sandi" 
                                    :value="modalMode === 'create' ? 'Kata Sandi Akun' : 'Kata Sandi Baru (Opsional)'" 
                                    class="text-xs font-bold text-slate-700" 
                                />
                                <TextInput
                                    id="kata_sandi"
                                    type="password"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                    v-model="form.kata_sandi"
                                    :placeholder="modalMode === 'create' ? 'Minimal 8 karakter' : 'Kosongkan jika tidak diubah'"
                                    :required="modalMode === 'create'"
                                />
                                <InputError class="mt-1" :message="form.errors.kata_sandi" />
                            </div>
                        </div>
                    </div>

                    <!-- Biodata Box -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-4">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-pink-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Biodata Guru & Kontak</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="nip" value="NIP / Kode Guru" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="nip"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                    v-model="form.nip"
                                    placeholder="Contoh: G-001"
                                />
                                <InputError class="mt-1" :message="form.errors.nip" />
                            </div>

                            <div>
                                <InputLabel for="no_hp" value="No. WhatsApp / HP" class="text-xs font-bold text-slate-700" />
                                <TextInput
                                    id="no_hp"
                                    type="text"
                                    class="mt-1.5 block w-full bg-white border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                                    v-model="form.no_hp"
                                    placeholder="Contoh: 08123456789"
                                />
                                <InputError class="mt-1" :message="form.errors.no_hp" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="alamat" value="Alamat Domisili" class="text-xs font-bold text-slate-700" />
                            <textarea
                                id="alamat"
                                rows="3"
                                class="mt-1.5 block w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 p-3"
                                v-model="form.alamat"
                                placeholder="Masukkan alamat domisili guru..."
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
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-md shadow-purple-500/20 transition-all transform hover:-translate-y-0.5 disabled:opacity-50"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Data Guru' }}</span>
                        </button>
                    </div>

                </form>
            </div>
        </Modal>

        <!-- MODAL CETAK NAME TAG GURU -->
        <Modal :show="showingNameTagModal" @close="showingNameTagModal = false" max-width="lg">
            <div class="p-6 sm:p-8 bg-white">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-purple-100">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">
                            Cetak Name Tag Pengajar
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Pratinjau kartu pengenal nama guru sebelum dicetak.
                        </p>
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

                <!-- NAME TAG CARD PREVIEW & PRINT AREA -->
                <div class="flex justify-center py-4">
                    <div 
                        id="nametag-printable-area" 
                        class="w-72 bg-white rounded-3xl border-2 border-purple-200 shadow-2xl overflow-hidden text-center relative"
                    >
                        <!-- Card Header Background -->
                        <div class="h-28 bg-gradient-to-tr from-purple-600 via-indigo-600 to-pink-500 p-4 text-white relative flex flex-col items-center">
                            <!-- Institution / Les Header -->
                            <div class="flex items-center gap-1.5 justify-center">
                                <span class="font-black text-sm tracking-tight drop-shadow-sm">
                                    {{ $page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria' }}
                                </span>
                            </div>
                            <span class="text-[9px] uppercase tracking-widest text-amber-200 font-extrabold mt-0.5">
                                KARTU PENGENAL GURU
                            </span>
                        </div>

                        <!-- Teacher Photo Circle -->
                        <div class="relative -mt-12 mb-3 flex justify-center">
                            <div class="w-24 h-24 rounded-2xl bg-white p-1 shadow-lg border-2 border-purple-200 overflow-hidden flex items-center justify-center">
                                <img 
                                    v-if="selectedGuruNameTag?.foto_url" 
                                    :src="selectedGuruNameTag.foto_url" 
                                    :alt="selectedGuruNameTag?.nama"
                                    class="w-full h-full object-cover rounded-xl"
                                />
                                <div 
                                    v-else 
                                    class="w-full h-full bg-gradient-to-tr from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-white font-black text-2xl"
                                >
                                    {{ (selectedGuruNameTag?.nama || 'G').charAt(0) }}
                                </div>
                            </div>
                        </div>

                        <!-- Teacher Details -->
                        <div class="px-6 pb-6 space-y-3">
                            <div>
                                <h4 class="font-black text-base text-slate-900 leading-tight">
                                    {{ selectedGuruNameTag?.nama || 'Nama Guru' }}
                                </h4>
                                <p class="text-xs font-bold text-purple-700 mt-0.5">
                                    Tenaga Pendidik / Guru
                                </p>
                            </div>

                            <div class="py-2.5 px-3 bg-purple-50/60 rounded-2xl border border-purple-100/80 text-left space-y-1.5 text-xs">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">NIK</span>
                                    <span class="font-mono font-bold text-slate-800">{{ selectedGuruNameTag?.nik || '-' }}</span>
                                </div>
                                <div v-if="selectedGuruNameTag?.nip" class="flex items-center justify-between text-slate-600">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">NIP / Kode</span>
                                    <span class="font-mono font-bold text-slate-800">{{ selectedGuruNameTag.nip }}</span>
                                </div>
                                <div v-if="selectedGuruNameTag?.no_hp" class="flex items-center justify-between text-slate-600">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">No. Kontak</span>
                                    <span class="font-bold text-slate-800">{{ selectedGuruNameTag.no_hp }}</span>
                                </div>
                            </div>

                            <div class="pt-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Pengajar Resmi Terverifikasi
                                </span>
                            </div>
                        </div>

                        <!-- Card Bottom Stripe -->
                        <div class="h-2 bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500"></div>
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
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-md shadow-purple-500/20 transition-all transform hover:-translate-y-0.5 disabled:opacity-50"
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
            class="lg:hidden fixed bottom-6 right-6 z-40 w-14 h-14 rounded-full bg-gradient-to-tr from-purple-600 via-indigo-600 to-pink-500 text-white shadow-2xl shadow-purple-600/50 flex items-center justify-center transform active:scale-95 transition-all border-2 border-white"
            title="Tambah Guru Baru"
            aria-label="Tambah Guru Baru"
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
    #nametag-printable-area,
    #nametag-printable-area * {
        visibility: visible !important;
    }
    #nametag-printable-area {
        position: fixed !important;
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
        box-shadow: none !important;
        border: 1.5px solid #c084fc !important;
        margin: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
