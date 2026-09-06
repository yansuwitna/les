<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    daftar_kegiatan: Object,
    daftar_target: Array,
    target_terpilih: Object,
    filters: Object,
});

const search = ref(props.filters?.cari || '');
const selectedTarget = ref(props.filters?.target_id || '');
const selectedStatus = ref(props.filters?.status || '');

let filterTimeout = null;
const applyFilters = () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        router.get(route('guru.kegiatan.index'), {
            cari: search.value,
            materi_id: props.filters?.materi_id || undefined,
            target_id: selectedTarget.value,
            status: selectedStatus.value,
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 300);
};

watch(search, applyFilters);
watch(selectedTarget, applyFilters);
watch(selectedStatus, applyFilters);

// Modal state & form
const showingModal = ref(false);
const modalMode = ref('create');
const selectedKegiatanId = ref(null);

const todayStr = new Date().toISOString().slice(0, 10);
const nowTimeStr = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).format(new Date()).replace('.', ':');

const form = useForm({
    target_id: '',
    nomor_urut: 1,
    keterangan: '',
    metode: '',
    tanggal_mulai: todayStr,
    tanggal_selesai: todayStr,
    waktu_mulai: '14:00',
    waktu_selesai: '15:30',
    catatan_hasil: '',
    kegiatan_selanjutnya: '',
    status: 'Lanjut',
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    form.tanggal_mulai = todayStr;
    form.tanggal_selesai = todayStr;
    form.waktu_mulai = '14:00';
    form.waktu_selesai = '15:30';
    form.status = 'Lanjut';
    if (props.target_terpilih?.id) {
        form.target_id = props.target_terpilih.id;
    } else if (props.filters?.target_id) {
        form.target_id = props.filters.target_id;
    } else if (props.daftar_target?.length > 0) {
        form.target_id = props.daftar_target[0].id;
    }
    showingModal.value = true;
};

const openEditModal = (kegiatan) => {
    modalMode.value = 'edit';
    selectedKegiatanId.value = kegiatan.id;
    form.clearErrors();
    form.target_id = kegiatan.target_id;
    form.nomor_urut = kegiatan.nomor_urut;
    form.keterangan = kegiatan.keterangan;
    form.metode = kegiatan.metode || '';
    form.tanggal_mulai = kegiatan.tanggal_mulai;
    form.tanggal_selesai = kegiatan.tanggal_selesai || kegiatan.tanggal_mulai;
    form.waktu_mulai = kegiatan.waktu_mulai?.slice(0, 5) || '14:00';
    form.waktu_selesai = kegiatan.waktu_selesai?.slice(0, 5) || '15:30';
    form.catatan_hasil = kegiatan.catatan_hasil || '';
    form.kegiatan_selanjutnya = kegiatan.kegiatan_selanjutnya || '';
    form.status = kegiatan.status || 'Lanjut';
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('guru.kegiatan.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('guru.kegiatan.update', selectedKegiatanId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteKegiatan = (kegiatan) => {
    const judul = kegiatan.keterangan ? `"${kegiatan.keterangan}"` : 'kegiatan ini';
    if (typeof window !== 'undefined' && window.Swal) {
        window.Swal.fire({
            title: 'Hapus Log Kegiatan?',
            text: `Apakah Anda yakin ingin menghapus catatan kegiatan ${judul}? Data yang dihapus tidak dapat dikembalikan.`,
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
                router.delete(route('guru.kegiatan.destroy', kegiatan.id), {
                    preserveScroll: true,
                });
            }
        });
    } else if (confirm(`Hapus catatan kegiatan ${judul}?`)) {
        router.delete(route('guru.kegiatan.destroy', kegiatan.id), {
            preserveScroll: true,
        });
    }
};

const cetakLog = () => {
    const params = new URLSearchParams();
    if (props.filters?.materi_id) params.append('materi_id', props.filters.materi_id);
    else if (props.filters?.target_id) params.append('target_id', props.filters.target_id);
    if (selectedStatus.value) params.append('status', selectedStatus.value);

    const url = route('guru.kegiatan.cetak') + (params.toString() ? '?' + params.toString() : '');
    window.open(url, '_blank');
};
</script>

<template>
    <Head :title="'Log Kegiatan Harian - ' + ($page.props.pengaturan?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">
                        Log Kegiatan & Jurnal Harian Guru
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">
                        Rekam rincian materi diajarkan, catatan evaluasi siswa, dan langkah pembelajaran berikutnya
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6 pb-12 max-w-7xl mx-auto">
            
            <!-- IDENTITAS SISWA & MATERI (DI ATAS TABEL) -->
            <div v-if="target_terpilih" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Identitas Siswa -->
                <div class="bg-white p-5 rounded-3xl border border-emerald-100 shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-pink-500 text-white flex items-center justify-center shadow-md shadow-purple-500/20 shrink-0 font-black text-base overflow-hidden border border-purple-200">
                            <img 
                                v-if="target_terpilih.siswa?.foto_url" 
                                :src="target_terpilih.siswa.foto_url" 
                                :alt="target_terpilih.siswa.nama"
                                class="w-full h-full object-cover"
                            />
                            <span v-else>{{ (target_terpilih.siswa?.nama || 'S').charAt(0) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-200">
                                Identitas Siswa
                            </span>
                            <h3 class="text-base font-black text-slate-900 mt-1">
                                {{ target_terpilih.siswa?.nama || 'Siswa' }}
                            </h3>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                <span v-if="target_terpilih.siswa?.nomor_siswa" class="font-mono text-purple-700 font-bold">No: {{ target_terpilih.siswa.nomor_siswa }}</span>
                                <span v-if="target_terpilih.siswa?.nama_wali" class="text-slate-400"> &bull; Wali: {{ target_terpilih.siswa.nama_wali }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Identitas Materi & Tombol Kembali -->
                <div class="bg-white p-5 rounded-3xl border border-emerald-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0 font-black text-base">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                                Materi Pembelajaran
                            </span>
                            <h3 class="text-base font-black text-slate-900 mt-1">
                                {{ target_terpilih.nama }}
                            </h3>
                            <p v-if="target_terpilih.deskripsi" class="text-xs text-slate-500 line-clamp-1 mt-0.5" :title="target_terpilih.deskripsi">
                                {{ target_terpilih.deskripsi }}
                            </p>
                        </div>
                    </div>

                    <Link
                        :href="target_terpilih.materi_id_enc ? route('guru.target.index', { materi_id: target_terpilih.materi_id_enc }) : (target_terpilih.siswa_id_enc ? route('guru.materi.index', { siswa_id: target_terpilih.siswa_id_enc }) : route('guru.materi.index'))"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-50 hover:bg-emerald-600 hover:text-white border border-emerald-200 text-emerald-700 font-bold text-xs transition-all self-start sm:self-auto shrink-0 shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali</span>
                    </Link>
                </div>
            </div>

            <!-- CONTROLS & CARDS CONTAINER -->
            <div class="bg-white rounded-3xl border border-emerald-100 shadow-xl shadow-emerald-500/5 overflow-hidden">
                
                <!-- Filters Bar -->
                <div class="p-6 border-b border-emerald-50 space-y-4 bg-gradient-to-r from-emerald-50/30 via-purple-50/20 to-white">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
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
                                    placeholder="Cari kegiatan atau materi..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-emerald-100 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none bg-white"
                                />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2.5 w-full sm:w-auto">
                            <!-- Cetak Button -->
                            <button
                                @click="cetakLog"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-extrabold text-xs transition-all shadow-sm flex items-center justify-center gap-2"
                                title="Cetak Log Kegiatan"
                            >
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span>Cetak</span>
                            </button>

                            <!-- Add Button -->
                            <button
                                @click="openCreateModal"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-extrabold text-xs transition-all shadow-md shadow-emerald-500/20 flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Catat Kegiatan Baru</span>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- TABLE VIEW FOR JURNAL KEGIATAN -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-emerald-50 bg-slate-50/60 text-[11px] font-black uppercase tracking-wider text-slate-400">
                                <th class="py-4 px-6 w-16 text-center">Sesi</th>
                                <th class="py-4 px-6">Tanggal & Waktu</th>
                                <th class="py-4 px-6">Topik / Keterangan</th>
                                <th class="py-4 px-6">Metode</th>
                                <th class="py-4 px-6">Catatan Hasil & Rencana</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-right print:hidden">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-50/60 font-medium text-slate-600">
                            <tr 
                                v-for="k in daftar_kegiatan?.data" 
                                :key="k.id"
                                class="hover:bg-emerald-50/30 transition-colors"
                            >
                                <!-- Sesi -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl text-[11px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        #{{ k.nomor_urut }}
                                    </span>
                                </td>

                                <!-- Tanggal & Waktu -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <p class="font-extrabold text-slate-900 text-xs">
                                        {{ new Date(k.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        {{ k.waktu_mulai?.slice(0, 5) }} - {{ k.waktu_selesai?.slice(0, 5) }}
                                    </p>
                                </td>

                                <!-- Topik / Keterangan -->
                                <td class="py-4 px-6 max-w-xs">
                                    <p class="font-extrabold text-slate-900 text-xs">{{ k.keterangan }}</p>
                                    <p v-if="!target_terpilih" class="text-[11px] text-purple-600 font-medium mt-0.5">
                                        {{ k.target?.nama }} &bull; <span class="text-slate-500 font-semibold">{{ k.target?.siswa?.nama }}</span>
                                    </p>
                                </td>

                                <!-- Metode -->
                                <td class="py-4 px-6 whitespace-nowrap text-slate-700 font-semibold">
                                    {{ k.metode || '-' }}
                                </td>

                                <!-- Catatan Hasil & Kegiatan Selanjutnya -->
                                <td class="py-4 px-6 max-w-sm">
                                    <div v-if="k.catatan_hasil" class="text-xs text-slate-700 mb-1">
                                        <span class="font-bold text-emerald-700">Evaluasi:</span> {{ k.catatan_hasil }}
                                    </div>
                                    <div v-if="k.kegiatan_selanjutnya" class="text-[11px] text-slate-500">
                                        <span class="font-bold text-purple-700">Rencana:</span> {{ k.kegiatan_selanjutnya }}
                                    </div>
                                    <span v-if="!k.catatan_hasil && !k.kegiatan_selanjutnya" class="text-slate-400">-</span>
                                </td>

                                <!-- Status Target Materi -->
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    <span 
                                        :class="[
                                            k.status === 'Selesai' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '',
                                            k.status === 'Lanjut' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '',
                                            k.status === 'Diulangi' ? 'bg-amber-50 text-amber-700 border-amber-200' : '',
                                        ]"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold border shadow-xs"
                                    >
                                        {{ k.status }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap print:hidden">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button
                                            @click="openEditModal(k)"
                                            class="p-2 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 transition-colors"
                                            title="Ubah Log Kegiatan"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteKegiatan(k)"
                                            class="p-2 rounded-xl bg-slate-100 hover:bg-pink-50 hover:text-pink-600 text-slate-600 transition-colors"
                                            title="Hapus Log Kegiatan"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Empty State -->
                    <div v-if="!daftar_kegiatan?.data || daftar_kegiatan.data.length === 0" class="text-center py-16 text-slate-400">
                        <div class="w-14 h-14 mx-auto rounded-full bg-emerald-50 text-emerald-400 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <p class="font-bold text-sm text-slate-700">Belum Ada Log Kegiatan</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Catat jurnal pembelajaran harian setiap kali Anda selesai membimbing siswa agar perkembangan belajarnya terpantau.
                        </p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="daftar_kegiatan?.links && daftar_kegiatan.links.length > 3" class="p-4 border-t border-emerald-50 flex items-center justify-between">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan {{ daftar_kegiatan.from || 0 }} sampai {{ daftar_kegiatan.to || 0 }} dari {{ daftar_kegiatan.total || 0 }} catatan kegiatan
                    </div>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, i) in daftar_kegiatan.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                link.active ? 'bg-emerald-600 text-white font-black' : 'bg-white hover:bg-emerald-50 text-slate-600',
                                !link.url ? 'opacity-40 pointer-events-none' : ''
                            ]"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold transition-all"
                            v-html="link.label"
                        />
                    </div>
                </div>

            </div>

        </div>

        <!-- MODAL FORM KEGIATAN -->
        <Modal :show="showingModal" @close="closeModal" maxWidth="2xl">
            <div class="p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-emerald-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-800 tracking-tight">
                                {{ modalMode === 'create' ? 'Catat Log Kegiatan Baru' : 'Ubah Log Kegiatan' }}
                            </h3>
                            <p class="text-xs text-slate-400 font-medium">Isi jurnal aktivitas pembelajaran untuk siswa</p>
                        </div>
                    </div>
                    <button @click="closeModal" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">

                    <!-- Keterangan Kegiatan / Topik -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan / Topik Kegiatan *</label>
                        <input
                            v-model="form.keterangan"
                            type="text"
                            placeholder="Contoh: Latihan Soal Matematika Bab Pecahan & Perkalian"
                            class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            required
                        />
                        <p v-if="form.errors.keterangan" class="text-xs text-pink-600 mt-1 font-medium">{{ form.errors.keterangan }}</p>
                    </div>

                    <!-- Tanggal & Jam Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Pelaksanaan *</label>
                            <input
                                v-model="form.tanggal_mulai"
                                type="date"
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                                required
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Mulai *</label>
                                <input
                                    v-model="form.waktu_mulai"
                                    type="time"
                                    class="w-full px-3 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Selesai</label>
                                <input
                                    v-model="form.waktu_selesai"
                                    type="time"
                                    class="w-full px-3 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Metode / Teknik -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode / Cara Bimbingan</label>
                        <input
                            v-model="form.metode"
                            type="text"
                            placeholder="Contoh: Diskusi, Praktik Mandiri, Drill Soal"
                            class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                        />
                    </div>

                    <!-- Status Kelanjutan Target Materi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Target Materi *</label>
                        <select
                            v-model="form.status"
                            class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none bg-white"
                            required
                        >
                            <option value="Lanjut">Lanjut ke materi berikutnya</option>
                            <option value="Selesai">Selesai (Target Tuntas)</option>
                            <option value="Diulangi">Diulangi (Perlu Pemantapan)</option>
                        </select>
                        <p v-if="form.errors.status" class="text-xs text-pink-600 mt-1 font-medium">{{ form.errors.status }}</p>
                    </div>

                    <!-- Catatan Hasil & Rencana Berikutnya -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Hasil & Sikap Belajar</label>
                            <textarea
                                v-model="form.catatan_hasil"
                                rows="3"
                                placeholder="Bagaimana pemahaman siswa, kendala atau pencapaian..."
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Rencana Sesi Berikutnya</label>
                            <textarea
                                v-model="form.kegiatan_selanjutnya"
                                rows="3"
                                placeholder="Materi atau tugas yang disiapkan untuk pertemuan depan..."
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            ></textarea>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-emerald-50 flex items-center justify-end gap-3">
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
                            class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-black text-xs shadow-md shadow-emerald-500/20 transition-all"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Log Kegiatan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>