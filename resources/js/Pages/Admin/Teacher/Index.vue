<script setup>
import GuruIndex from '@/Pages/Admin/Guru/Index.vue';

defineProps({
    daftar_guru: Object,
    teachers: Object,
    filters: Object,
});
</script>

<template>
    <GuruIndex :daftar_guru="daftar_guru || teachers" :teachers="teachers || daftar_guru" :filters="filters" />
</template>
