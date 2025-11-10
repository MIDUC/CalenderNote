<template>
    <div class="flex justify-between items-center mt-4 text-sm text-gray-700">
        <p>
            Trang {{ currentPage }} / {{ totalPages }} — Tổng: {{ totalItems }} mục
        </p>

        <div class="flex space-x-2">
            <button :disabled="currentPage <= 1" @click="$emit('page-changed', currentPage - 1)"
                class="px-3 py-1 border rounded disabled:opacity-50 hover:bg-gray-100">
                ← Trước
            </button>

            <button :disabled="currentPage >= totalPages" @click="$emit('page-changed', currentPage + 1)"
                class="px-3 py-1 border rounded disabled:opacity-50 hover:bg-gray-100">
                Sau →
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    currentPage: Number,
    totalPages: Number,
    totalItems: Number,
})
defineEmits(['page-changed'])
</script>
