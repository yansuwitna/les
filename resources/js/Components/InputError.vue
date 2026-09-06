<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message" class="flex items-start gap-1.5 mt-1.5 text-xs font-semibold text-rose-600 animate-fadeIn">
        <svg class="w-3.5 h-3.5 shrink-0 text-rose-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span class="leading-tight">{{ message }}</span>
    </div>
</template>
