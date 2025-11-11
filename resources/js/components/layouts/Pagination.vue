<template>
    <div class="flex justify-between items-center mt-2 text-md text-gray-700">
        <div class="font-bold">
            Trang {{ currentPage }} / {{ totalPages }} — Tổng: {{ totalItems }} mục
        </div>

        <div class="flex space-x-2">
            <button :disabled="currentPage <= 1" @click="$emit('page-changed', currentPage - 1)"
                class="px-3 py-3 border rounded disabled:opacity-50 hover:bg-gray-100">
                <div class="font-bold text-xl">
                    <ChevronLeftIcon class="w-4 h-4 text-gray-600" />
                </div>

            </button>

            <button :disabled="currentPage >= totalPages" @click="$emit('page-changed', currentPage + 1)"
                class="px-3 py-1 border rounded disabled:opacity-50 hover:bg-gray-100">
                <div class="font-bold text-xl">
                    <ChevronRightIcon class="w-4 h-4 text-gray-600" />
                </div>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/solid'
defineProps({
    currentPage: Number,
    totalPages: Number,
    totalItems: Number,
})
defineEmits(['page-changed'])
</script>
