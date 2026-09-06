<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    daftar_target: Object,
    materi_terpilih: Object,
    filters: Object,
});

const search = ref(props.filters?.cari || '');

let filterTimeout = null;
const applyFilters = () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        router.get(route('guru.target.index'), {
            materi_id: props.filters?.materi_id,
            cari: search.value,
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 300);
};

watch(search, applyFilters);

// Modal state & form
const showingModal = ref(false);
const modalMode = ref('create');
const selectedTargetId = ref(null);

const form = useForm({
    materi_id: props.materi_terpilih?.id || '',
    siswa_id: props.materi_terpilih?.siswa_id || '',
    nama: '',
    deskripsi: '',
    status: 'aktif',
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    form.materi_id = props.materi_terpilih?.id || '';
    form.siswa_id = props.materi_terpilih?.siswa_id || '';
    form.status = 'aktif';
    showingModal.value = true;
};

const openEditModal = (target) => {
    modalMode.value = 'edit';
    selectedTargetId.value = target.id;
    form.clearErrors();
    form.materi_id = target.materi_id;
    form.siswa_id = target.siswa_id;
    form.nama = target.nama;
    form.deskripsi = target.deskripsi || '';
    form.status = target.status || 'aktif';
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('guru.target.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('guru.target.update', selectedTargetId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteTarget = (target) => {
    if (target.kegiatan_count > 0) {
        if (typeof window !== 'undefined' && window.Swal) {
            window.Swal.fire({
                icon: 'warning',
                title: 'Tidak Dapat Dihapus',
                text: `Target "${target.nama}" tidak dapat dihapus karena masih memiliki ${target.kegiatan_count} log kegiatan pembelajaran.`,
                confirmButtonColor: '#4f46e5',
                customClass: {
                    popup: 'rounded-3xl shadow-xl',
                    confirmButton: 'rounded-2xl px-5 py-2.5 font-bold text-xs',
                }
            });
        } else {
            alert(`Target "${target.nama}" tidak dapat dihapus karena masih memiliki ${target.kegiatan_count} log kegiatan pembelajaran.`);
        }
        return;
    }

    if (typeof window !== 'undefined' && window.Swal) {
        window.Swal.fire({
            title: 'Hapus Target Capaian?',
            text: `Apakah Anda yakin ingin menghapus target "${target.nama}"? Data yang dihapus tidak dapat dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl shadow-xl',
                confirmButton: 'rounded-2xl px-5 py-2.5 font-bold text-xs',
                cancelButton: 'rounded-2xl px-5 py-2.5 font-bold text-xs',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('guru.target.destroy', target.id), {
                    preserveScroll: true,
                });
            }
        });
    } else if (confirm(`Apakah Anda yakin ingin menghapus target "${target.nama}"?`)) {
        router.delete(route('guru.target.destroy', target.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="'Target Capaian - ' + ($page.props.pengaturan?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">
                        Target Capaian Pembelajaran
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">
                        Kelola target dan indikator capaian belajar siswa pada materi yang dipilih
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6 pb-12 max-w-7xl mx-auto">
            
            <!-- INFORMASI SISWA & MATERI TERPILIH + TOMBOL KEMBALI -->
            <div v-if="materi_terpilih" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Identitas Siswa -->
                <div class="bg-white p-5 rounded-3xl border border-indigo-100 shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-pink-500 text-white flex items-center justify-center shadow-md shadow-purple-500/20 shrink-0 font-black text-base overflow-hidden border border-purple-200">
                            <img 
                                v-if="materi_terpilih.siswa?.foto_url" 
                                :src="materi_terpilih.siswa.foto_url" 
                                :alt="materi_terpilih.siswa.nama"
                                class="w-full h-full object-cover"
                            />
                            <span v-else>{{ (materi_terpilih.siswa?.nama || 'S').charAt(0) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-200">
                                Siswa Bimbingan
                            </span>
                            <h3 class="text-base font-black text-slate-900 mt-1">
                                {{ materi_terpilih.siswa?.nama || 'Siswa' }}
                            </h3>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                <span v-if="materi_terpilih.siswa?.nomor_siswa" class="font-mono text-purple-700 font-bold">No: {{ materi_terpilih.siswa.nomor_siswa }}</span>
                                <span v-if="materi_terpilih.siswa?.nama_wali" class="text-slate-400"> &bull; Wali: {{ materi_terpilih.siswa.nama_wali }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Identitas Materi & Tombol Kembali -->
                <div class="bg-white p-5 rounded-3xl border border-indigo-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 text-white flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0 font-black text-base">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-200">
                                Materi Pembelajaran
                            </span>
                            <h3 class="text-base font-black text-slate-900 mt-1">
                                {{ materi_terpilih.nama }}
                            </h3>
                            <p v-if="materi_terpilih.deskripsi" class="text-xs text-slate-500 line-clamp-1 mt-0.5" :title="materi_terpilih.deskripsi">
                                {{ materi_terpilih.deskripsi }}
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Kembali ke Halaman Materi Siswa -->
                    <Link
                        :href="route('guru.materi.index', materi_terpilih.siswa_id_enc ? { siswa_id: materi_terpilih.siswa_id_enc } : {})"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-50 hover:bg-indigo-600 hover:text-white border border-indigo-200 text-indigo-700 font-bold text-xs transition-all self-start sm:self-auto shrink-0 shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali ke Materi</span>
                    </Link>
                </div>
            </div>
            
            <!-- MAIN TABLE CARD -->
            <div class="bg-white rounded-3xl border border-indigo-100 shadow-xl shadow-indigo-500/5 overflow-hidden">
                
                <!-- Controls & Header -->
                <div class="p-6 border-b border-indigo-50 space-y-4 bg-gradient-to-r from-indigo-50/30 via-purple-50/20 to-white">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                            <!-- Search -->
                            <div class="relative w-full sm:w-80">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Cari target capaian..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-indigo-100 text-xs font-semibold focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none bg-white"
                                />
                            </div>
                        </div>

                        <!-- Add Button -->
                        <button
                            @click="openCreateModal"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-extrabold text-xs transition-all shadow-md shadow-indigo-500/20 flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Target Capaian</span>
                        </button>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-indigo-50 bg-slate-50/50 text-[11px] font-black uppercase tracking-wider text-slate-400">
                                <th class="py-4 px-6">Nama Target</th>
                                <th class="py-4 px-6">Indikator / Deskripsi</th>
                                <th class="py-4 px-6 text-center">Jml Kegiatan</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-50/60 font-medium text-slate-600">
                            <tr 
                                v-for="target in daftar_target?.data" 
                                :key="target.id"
                                class="hover:bg-indigo-50/30 transition-colors"
                            >
                                <!-- Nama Target -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 font-black text-xs flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-sm">{{ target.nama }}</p>
                                            <p class="text-[10px] text-slate-400">Dibuat: {{ new Date(target.created_at).toLocaleDateString('id-ID') }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Deskripsi -->
                                <td class="py-4 px-6 max-w-md text-slate-500">
                                    <p class="truncate" :title="target.deskripsi">{{ target.deskripsi || '-' }}</p>
                                </td>

                                <!-- Jml Kegiatan -->
                                <td class="py-4 px-6 text-center">
                                    <span 
                                        :class="target.kegiatan_count > 0 ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-black border"
                                        :title="`${target.kegiatan_count || 0} Kegiatan Tercatat`"
                                    >
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <span>{{ target.kegiatan_count || 0 }} Kegiatan</span>
                                    </span>
                                </td>

                                <!-- Status (Aktif / Selesai) -->
                                <td class="py-4 px-6 text-center">
                                    <span 
                                        :class="target.status === 'selesai' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border shadow-xs"
                                    >
                                        <span :class="target.status === 'selesai' ? 'bg-emerald-500' : 'bg-blue-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                        {{ target.status === 'selesai' ? 'Selesai' : 'Aktif' }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Tombol Kegiatan: guru/kegiatan?target_id=... -->
                                        <Link 
                                            :href="route('guru.kegiatan.index', { target_id: target.enc_id || target.id })"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white border border-emerald-200 text-emerald-700 font-bold text-xs transition-all shadow-sm"
                                            title="Buka Jurnal Kegiatan Target Ini"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                            <span>Kegiatan</span>
                                        </Link>
                                        <button 
                                            @click="openEditModal(target)"
                                            class="p-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors"
                                            title="Ubah Target"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <!-- Tombol Hapus (Disabled jika jumlah kegiatan > 0) -->
                                        <button 
                                            @click="deleteTarget(target)"
                                            :disabled="target.kegiatan_count > 0"
                                            :class="[
                                                target.kegiatan_count > 0 
                                                    ? 'bg-slate-100 text-slate-300 cursor-not-allowed opacity-60' 
                                                    : 'bg-pink-50 text-pink-600 hover:bg-pink-100'
                                            ]"
                                            class="p-2 rounded-xl transition-colors"
                                            :title="target.kegiatan_count > 0 ? 'Target tidak dapat dihapus karena masih memiliki log kegiatan' : 'Hapus Target'"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!daftar_target?.data || daftar_target.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-indigo-50 text-indigo-400 flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-sm text-slate-600">Belum Ada Target Capaian</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Silakan klik "Tambah Target Capaian" untuk menambahkan target pembelajaran pada materi ini.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="daftar_target?.links && daftar_target.links.length > 3" class="p-4 border-t border-indigo-50 flex items-center justify-between">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan {{ daftar_target.from || 0 }} sampai {{ daftar_target.to || 0 }} dari {{ daftar_target.total || 0 }} target
                    </div>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, i) in daftar_target.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                link.active ? 'bg-indigo-600 text-white font-black' : 'bg-white hover:bg-indigo-50 text-slate-600',
                                !link.url ? 'opacity-40 pointer-events-none' : ''
                            ]"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold transition-all"
                            v-html="link.label"
                        />
                    </div>
                </div>

            </div>

        </div>

        <!-- MODAL FORM TARGET -->
        <Modal :show="showingModal" @close="closeModal" maxWidth="lg">
            <div class="p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-indigo-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-800 tracking-tight">
                                {{ modalMode === 'create' ? 'Tambah Target Capaian' : 'Ubah Target Capaian' }}
                            </h3>
                            <p class="text-xs text-slate-400 font-medium">Tentukan capaian target untuk materi pembelajaran ini</p>
                        </div>
                    </div>
                    <button @click="closeModal" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Nama Target -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Target Capaian *</label>
                        <input
                            v-model="form.nama"
                            type="text"
                            placeholder="Contoh: Menguasai tabel perkalian 1 sampai 5"
                            class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                            required
                        />
                        <p v-if="form.errors.nama" class="text-xs text-pink-600 mt-1 font-medium">{{ form.errors.nama }}</p>
                    </div>

                    <!-- Deskripsi / Indikator -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi / Indikator</label>
                        <textarea
                            v-model="form.deskripsi"
                            rows="3"
                            placeholder="Tuliskan indikator pencapaian atau kriteria penilaian..."
                            class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                        ></textarea>
                        <p v-if="form.errors.deskripsi" class="text-xs text-pink-600 mt-1 font-medium">{{ form.errors.deskripsi }}</p>
                    </div>

                    <!-- Status Target (Aktif / Selesai) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Target *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label 
                                :class="form.status === 'aktif' ? 'bg-blue-50/80 border-blue-500 text-blue-800 ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100/80'"
                                class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition-all"
                            >
                                <input 
                                    type="radio" 
                                    value="aktif" 
                                    v-model="form.status" 
                                    class="text-blue-600 focus:ring-blue-500" 
                                />
                                <div>
                                    <p class="font-extrabold text-xs">Aktif</p>
                                    <p class="text-[10px] text-slate-500">Target berjalan</p>
                                </div>
                            </label>

                            <label 
                                :class="form.status === 'selesai' ? 'bg-emerald-50/80 border-emerald-500 text-emerald-800 ring-2 ring-emerald-500/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100/80'"
                                class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition-all"
                            >
                                <input 
                                    type="radio" 
                                    value="selesai" 
                                    v-model="form.status" 
                                    class="text-emerald-600 focus:ring-emerald-500" 
                                />
                                <div>
                                    <p class="font-extrabold text-xs">Selesai</p>
                                    <p class="text-[10px] text-slate-500">Target tercapai</p>
                                </div>
                            </label>
                        </div>
                        <p v-if="form.errors.status" class="text-xs text-pink-600 mt-1 font-medium">{{ form.errors.status }}</p>
                    </div>

                    <div class="pt-4 border-t border-indigo-50 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-5 py-2.5 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black text-xs shadow-md shadow-indigo-500/20 transition-all"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Target' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>