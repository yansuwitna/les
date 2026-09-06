<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    daftar_jadwal: Object,
    daftar_guru: Array,
    daftar_siswa: Array,
    daftar_materi: Array,
    filters: Object,
});

const search = ref(props.filters?.cari || '');

// Debounced filter handler
let filterTimeout = null;
const applyFilters = () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        router.get(route('admin.jadwal.index'), {
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
const selectedJadwalId = ref(null);

const form = useForm({
    guru_id: '',
    siswa_id: '',
    aktif: true,
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    form.guru_id = '';
    form.siswa_id = '';
    form.aktif = true;
    showingModal.value = true;
};

const openEditModal = (jadwal) => {
    modalMode.value = 'edit';
    selectedJadwalId.value = jadwal.id;
    form.guru_id = jadwal.guru_id;
    form.siswa_id = jadwal.siswa_id;
    form.aktif = jadwal.aktif !== undefined ? Boolean(jadwal.aktif) : true;
    form.clearErrors();
    showingModal.value = true;
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('admin.jadwal.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('admin.jadwal.update', selectedJadwalId.value), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const toggleJadwalStatus = (jadwal) => {
    const action = jadwal.aktif ? 'menonaktifkan' : 'mengaktifkan';
    if (confirm(`Apakah Anda yakin ingin ${action} pembimbing untuk siswa ini?`)) {
        router.patch(route('admin.jadwal.status', jadwal.id), {}, {
            preserveScroll: true,
        });
    }
};

const deleteJadwal = (jadwal) => {
    if (confirm(`Hapus data pembimbing ${jadwal.guru?.nama || ''} untuk siswa ${jadwal.siswa?.nama || ''}?`)) {
        router.delete(route('admin.jadwal.destroy', jadwal.id));
    }
};
</script>

<template>
    <Head :title="'Pembimbing - ' + ($page.props.pengaturan?.nama_les || $page.props.settings?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">
                    Data Guru Pembimbing Siswa
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Tentukan dan kelola alokasi guru pembimbing untuk setiap siswa bimbingan</p>
            </div>
        </template>

        <div class="space-y-6">

            <!-- MAIN DATA CARD -->
            <div class="bg-white rounded-3xl border border-purple-100 shadow-xl shadow-purple-500/5 overflow-hidden">
                
                <!-- Table Header Controls -->
                <div class="p-6 border-b border-purple-50 space-y-4 bg-gradient-to-r from-purple-50/30 via-pink-50/20 to-white">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
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
                                placeholder="Cari nama guru, siswa, atau catatan..."
                            />
                        </div>

                        <!-- Desktop Add Button -->
                        <div class="hidden sm:flex items-center gap-3">
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 shadow-md shadow-purple-500/25 transition-all transform hover:-translate-y-0.5"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Pilih Pembimbing Baru</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-purple-50/50 text-[11px] font-black uppercase tracking-wider text-purple-900 border-b border-purple-50">
                            <tr>
                                <th class="px-6 py-4">Guru Pembimbing</th>
                                <th class="px-6 py-4">Siswa Bimbingan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-50">
                            <tr 
                                v-for="jadwal in daftar_jadwal.data" 
                                :key="jadwal.id" 
                                :class="[
                                    jadwal.aktif ? 'hover:bg-purple-50/40' : 'bg-slate-50/60 opacity-80 hover:bg-slate-100/50',
                                    'transition-colors duration-150 group'
                                ]"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                            {{ (jadwal.guru?.nama || 'G').charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs">
                                                {{ jadwal.guru?.nama || 'Guru Belum Dipilih' }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 font-mono">
                                                NIK: {{ jadwal.guru?.nik || '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-pink-100 text-pink-700 flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                            {{ (jadwal.siswa?.nama || 'S').charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-xs">
                                                {{ jadwal.siswa?.nama || 'Siswa Belum Dipilih' }}
                                            </p>
                                            <p class="text-[10px] text-slate-400 font-mono">
                                                No: {{ jadwal.siswa?.nomor_siswa || '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Column -->
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        type="button"
                                        @click="toggleJadwalStatus(jadwal)"
                                        :title="jadwal.aktif ? 'Klik untuk nonaktifkan bimbingan ini' : 'Klik untuk aktifkan bimbingan ini'"
                                        class="group/btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wider transition-all duration-200 shadow-sm"
                                        :class="jadwal.aktif 
                                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' 
                                            : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200'"
                                    >
                                        <span 
                                            class="w-2 h-2 rounded-full transition-transform group-hover/btn:scale-125"
                                            :class="jadwal.aktif ? 'bg-emerald-500' : 'bg-rose-500'"
                                        ></span>
                                        <span>{{ jadwal.aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            @click="openEditModal(jadwal)"
                                            class="p-2 rounded-xl text-indigo-600 hover:bg-indigo-50 transition-colors"
                                            title="Ubah Pembimbing"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <button
                                            @click="deleteJadwal(jadwal)"
                                            class="p-2 rounded-xl text-pink-600 hover:bg-pink-50 transition-colors"
                                            title="Hapus Pembimbing"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="daftar_jadwal.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                                    <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-600 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-base font-bold text-slate-800">Belum Ada Pembimbing Siswa</p>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                        Silakan tentukan guru yang menjadi pembimbing untuk setiap siswa.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="daftar_jadwal.links && daftar_jadwal.links.length > 3" class="p-4 border-t border-purple-50 flex flex-wrap items-center justify-center gap-1.5 bg-slate-50/50">
                    <template v-for="(link, idx) in daftar_jadwal.links" :key="idx">
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

        <!-- MODAL FORM PILIH / UBAH PEMBIMBING -->
        <Modal :show="showingModal" @close="showingModal = false" max-width="xl">
            <div class="p-6 sm:p-8 bg-white">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-purple-100">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">
                            {{ modalMode === 'create' ? 'Tentukan Guru Pembimbing' : 'Ubah Guru Pembimbing' }}
                        </h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Pilih guru yang bertugas menjadi pembimbing bagi siswa.
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

                <form @submit.prevent="submitForm" class="space-y-5">
                    
                    <div class="space-y-4">
                        <!-- Siswa Selector -->
                        <div>
                            <InputLabel for="siswa_id" value="Pilih Siswa Bimbingan *" class="text-xs font-bold text-slate-700" />
                            <select
                                id="siswa_id"
                                v-model="form.siswa_id"
                                required
                                class="mt-1.5 block w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 p-2.5"
                            >
                                <option value="" disabled>-- Pilih Siswa --</option>
                                <option v-for="siswa in daftar_siswa" :key="siswa.id" :value="siswa.id">
                                    {{ siswa.nama }} (No: {{ siswa.nomor_siswa }})
                                </option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.siswa_id" />
                        </div>

                        <!-- Guru Selector -->
                        <div>
                            <InputLabel for="guru_id" value="Pilih Guru Pembimbing *" class="text-xs font-bold text-slate-700" />
                            <select
                                id="guru_id"
                                v-model="form.guru_id"
                                required
                                class="mt-1.5 block w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 p-2.5"
                            >
                                <option value="" disabled>-- Pilih Guru Pembimbing --</option>
                                <option v-for="guru in daftar_guru" :key="guru.id" :value="guru.id">
                                    {{ guru.nama }} (NIK: {{ guru.nik }})
                                </option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.guru_id" />
                        </div>

                        <!-- Status Aktif Switch -->
                        <div>
                            <InputLabel value="Status Bimbingan" class="text-xs font-bold text-slate-700 mb-2" />
                            <div class="flex items-center gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        v-model="form.aktif" 
                                        class="sr-only peer"
                                    />
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    <span class="ml-2 text-xs font-bold" :class="form.aktif ? 'text-emerald-700' : 'text-slate-400'">
                                        {{ form.aktif ? 'Status Aktif' : 'Status Nonaktif' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
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
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Data Pembimbing' }}</span>
                        </button>
                    </div>

                </form>
            </div>
        </Modal>

        <!-- Mobile Floating Action Button (FAB) -->
        <button
            @click="openCreateModal"
            class="lg:hidden fixed bottom-6 right-6 z-40 w-14 h-14 rounded-full bg-gradient-to-tr from-purple-600 via-indigo-600 to-pink-500 text-white shadow-2xl shadow-purple-600/50 flex items-center justify-center transform active:scale-95 transition-all border-2 border-white"
            title="Pilih Pembimbing Baru"
            aria-label="Pilih Pembimbing Baru"
        >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
        </button>

    </AuthenticatedLayout>
</template>
