<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    daftar_siswa: Object,
    filters: Object,
});

const search = ref(props.filters?.cari || '');

let filterTimeout = null;
const applyFilters = () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        router.get(route('guru.bimbingan.index'), {
            cari: search.value,
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 300);
};

watch(search, applyFilters);

// Modal Jadwal State
const showingJadwalModal = ref(false);
const selectedSiswa = ref(null);
const selectedBimbinganId = ref(null);

const formJadwal = useForm({
    siswa_id: null,
    hari: 'Senin',
    jam_mulai: '08:00',
    jam_selesai: '09:30',
});

const openJadwalModal = (siswa) => {
    selectedSiswa.value = siswa;
    const bimbingan = (siswa.bimbingan && siswa.bimbingan.length > 0) ? siswa.bimbingan[0] : null;
    formJadwal.siswa_id = siswa.id;
    if (bimbingan) {
        selectedBimbinganId.value = bimbingan.id;
        formJadwal.hari = bimbingan.hari || 'Senin';
        formJadwal.jam_mulai = bimbingan.jam_mulai ? bimbingan.jam_mulai.substring(0, 5) : '08:00';
        formJadwal.jam_selesai = bimbingan.jam_selesai ? bimbingan.jam_selesai.substring(0, 5) : '09:30';
    } else {
        selectedBimbinganId.value = null;
        formJadwal.hari = 'Senin';
        formJadwal.jam_mulai = '08:00';
        formJadwal.jam_selesai = '09:30';
    }
    formJadwal.clearErrors();
    showingJadwalModal.value = true;
};

const closeJadwalModal = () => {
    showingJadwalModal.value = false;
    formJadwal.reset();
    formJadwal.clearErrors();
};

const submitJadwal = () => {
    const targetId = selectedBimbinganId.value || 0;
    formJadwal.put(route('guru.bimbingan.jadwal.update', targetId), {
        preserveScroll: true,
        onSuccess: () => {
            closeJadwalModal();
        },
    });
};
</script>

<template>
    <Head :title="'Siswa Bimbingan - ' + ($page.props.pengaturan?.nama_les || 'Les Ceria')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">
                        Siswa Bimbingan
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">
                        Daftar seluruh siswa bimbingan yang dialokasikan kepada Anda
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6 pb-12 max-w-7xl mx-auto">
            
            <!-- MAIN TABLE CARD -->
            <div class="bg-white rounded-3xl border border-indigo-100 shadow-xl shadow-indigo-500/5 overflow-hidden">
                
                <!-- Controls & Search -->
                <div class="p-6 border-b border-indigo-50 bg-gradient-to-r from-indigo-50/30 via-purple-50/20 to-white">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="relative w-full sm:w-80">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari nama, no. siswa, atau wali..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-indigo-100 text-xs font-semibold focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none bg-white"
                            />
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-400">Total:</span>
                            <span class="px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 font-black text-xs">
                                {{ daftar_siswa?.total || 0 }} Siswa Bimbingan
                            </span>
                        </div>
                    </div>
                </div>

                <!-- TABLE SISWA BIMBINGAN -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-indigo-50 bg-slate-50/50 text-[11px] font-black uppercase tracking-wider text-slate-400">
                                <th class="py-4 px-6">Siswa</th>
                                <th class="py-4 px-6">No. Siswa / NIS</th>
                                <th class="py-4 px-6">Jadwal</th>
                                <th class="py-4 px-6">Wali & Kontak</th>
                                <th class="py-4 px-6">Status Akun</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-50/60 font-medium text-slate-600">
                            <tr 
                                v-for="siswa in daftar_siswa?.data" 
                                :key="siswa.id"
                                class="hover:bg-indigo-50/30 transition-colors"
                            >
                                <!-- Siswa & Foto -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-white border border-indigo-100 p-0.5 shadow-sm overflow-hidden flex items-center justify-center shrink-0">
                                            <img 
                                                v-if="siswa.foto_url" 
                                                :src="siswa.foto_url" 
                                                :alt="siswa.nama"
                                                class="w-full h-full object-cover rounded-xl"
                                            />
                                            <div 
                                                v-else 
                                                class="w-full h-full bg-gradient-to-tr from-pink-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-sm"
                                            >
                                                {{ (siswa.nama || 'S').charAt(0) }}
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-xs">{{ siswa.nama }}</p>
                                            <p class="text-[11px] text-slate-400 font-medium truncate max-w-xs">{{ siswa.alamat || 'Alamat belum diatur' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- No. Siswa & NIS (Digabung) -->
                                <td class="py-4 px-6">
                                    <div class="space-y-1">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-pink-50 text-pink-700 border border-pink-100 font-mono text-xs font-bold">
                                            <svg class="w-3.5 h-3.5 text-pink-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                            <span>{{ siswa.nomor_siswa }}</span>
                                        </div>
                                        <div v-if="siswa.nis" class="text-[11px] text-slate-500 font-mono flex items-center gap-1">
                                            <span class="text-slate-400 font-semibold">NIS:</span>
                                            <span class="font-bold text-slate-700">{{ siswa.nis }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Jadwal Bimbingan (Hari di atas, Jam horizontal menarik di bawah) -->
                                <td class="py-4 px-6">
                                    <div v-if="siswa.bimbingan && siswa.bimbingan.length > 0 && siswa.bimbingan[0].hari" class="inline-flex flex-col gap-1.5">
                                        <!-- Hari -->
                                        <div class="inline-flex items-center gap-1.5 text-slate-800">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 ring-4 ring-amber-100"></span>
                                            <span class="font-extrabold text-xs tracking-tight text-slate-900">{{ siswa.bimbingan[0].hari }}</span>
                                        </div>
                                        <!-- Rentang Waktu Horizontal Modern -->
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50/90 border border-amber-200/70 text-amber-900 shadow-sm">
                                            <span class="font-mono text-xs font-bold text-amber-950 bg-white/90 px-1.5 py-0.5 rounded-md shadow-xs border border-amber-200/50">
                                                {{ siswa.bimbingan[0].jam_mulai ? siswa.bimbingan[0].jam_mulai.substring(0, 5) : '00:00' }}
                                            </span>
                                            <span class="text-amber-500 font-bold text-[11px]">s/d</span>
                                            <span class="font-mono text-xs font-bold text-amber-950 bg-white/90 px-1.5 py-0.5 rounded-md shadow-xs border border-amber-200/50">
                                                {{ siswa.bimbingan[0].jam_selesai ? siswa.bimbingan[0].jam_selesai.substring(0, 5) : '00:00' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] text-slate-400 font-semibold bg-slate-50 border border-slate-200/60">
                                        Belum diatur
                                    </span>
                                </td>

                                <!-- Wali & Kontak -->
                                <td class="py-4 px-6">
                                    <div class="space-y-0.5">
                                        <p class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>{{ siswa.nama_wali || 'Wali Belum Diatur' }}</span>
                                        </p>
                                        <p class="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span>{{ siswa.no_hp || '-' }}</span>
                                        </p>
                                    </div>
                                </td>

                                <!-- Status Akun -->
                                <td class="py-4 px-6">
                                    <span 
                                        :class="siswa.aktif ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border"
                                    >
                                        <span :class="siswa.aktif ? 'bg-emerald-500' : 'bg-rose-500'" class="w-1.5 h-1.5 rounded-full"></span>
                                        {{ siswa.aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Tombol Jadwal -->
                                        <button 
                                            type="button"
                                            @click="openJadwalModal(siswa)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-500 hover:text-white border border-amber-200 text-amber-700 font-bold text-xs transition-all shadow-sm"
                                            title="Atur Jadwal Bimbingan Siswa"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>Jadwal</span>
                                        </button>

                                        <!-- Tombol Materi -->
                                        <Link 
                                            :href="route('guru.materi.index', { siswa_id: siswa.encrypted_id || siswa.id })"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-600 hover:text-white border border-purple-200 text-purple-700 font-bold text-xs transition-all shadow-sm"
                                            title="Kelola Target Materi Siswa"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            <span>Materi</span>
                                        </Link>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!daftar_siswa?.data || daftar_siswa.data.length === 0">
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-indigo-50 text-indigo-400 flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-sm text-slate-600">Belum ada siswa bimbingan</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Siswa yang dibimbing belum dialokasikan oleh admin atau tidak sesuai pencarian.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="daftar_siswa?.links && daftar_siswa.links.length > 3" class="p-4 border-t border-indigo-50 flex items-center justify-between">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan {{ daftar_siswa.from || 0 }} sampai {{ daftar_siswa.to || 0 }} dari total {{ daftar_siswa.total || 0 }} data
                    </div>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, i) in daftar_siswa.links"
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

        <!-- MODAL ATUR JADWAL BIMBINGAN -->
        <Modal :show="showingJadwalModal" @close="closeJadwalModal" maxWidth="md">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-indigo-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900">
                                Atur Jadwal Bimbingan
                            </h3>
                            <p class="text-xs text-slate-500 font-medium">
                                Siswa: <span class="font-bold text-indigo-600">{{ selectedSiswa?.nama || '-' }}</span>
                            </p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="closeJadwalModal" 
                        class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Form -->
                <form @submit.prevent="submitJadwal" class="space-y-4">
                    <!-- Pilihan Hari -->
                    <div>
                        <InputLabel for="hari" value="Pilih Hari Bimbingan *" class="text-xs font-bold text-slate-700" />
                        <select
                            id="hari"
                            v-model="formJadwal.hari"
                            required
                            class="mt-1.5 block w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 p-2.5"
                        >
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                        <InputError class="mt-1" :message="formJadwal.errors.hari" />
                    </div>

                    <!-- Jam Mulai & Jam Selesai -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <InputLabel for="jam_mulai" value="Jam Mulai *" class="text-xs font-bold text-slate-700" />
                            <input
                                id="jam_mulai"
                                type="time"
                                v-model="formJadwal.jam_mulai"
                                required
                                class="mt-1.5 block w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 p-2.5"
                            />
                            <InputError class="mt-1" :message="formJadwal.errors.jam_mulai" />
                        </div>

                        <div>
                            <InputLabel for="jam_selesai" value="Jam Selesai *" class="text-xs font-bold text-slate-700" />
                            <input
                                id="jam_selesai"
                                type="time"
                                v-model="formJadwal.jam_selesai"
                                required
                                class="mt-1.5 block w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 p-2.5"
                            />
                            <InputError class="mt-1" :message="formJadwal.errors.jam_selesai" />
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                        <button
                            type="button"
                            @click="closeJadwalModal"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="formJadwal.processing"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-black text-xs shadow-md shadow-amber-500/20 transition-all disabled:opacity-50 flex items-center gap-1.5"
                        >
                            <svg v-if="formJadwal.processing" class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ formJadwal.processing ? 'Menyimpan...' : 'Simpan Jadwal' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>