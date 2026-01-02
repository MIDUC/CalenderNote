<template>
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-gray-200">
        <div class="text-sm sm:text-base font-semibold text-gray-700">
            <span class="hidden sm:inline">Trang </span><span class="text-blue-600">{{ currentPage }}</span> / 
            <span class="text-blue-600">{{ totalPages }}</span> 
            <span class="hidden sm:inline"> — Tổng: </span>
            <span class="text-indigo-600 font-bold">{{ totalItems }}</span> 
            <span class="hidden sm:inline"> mục</span>
        </div>

        <div class="flex items-center gap-2">
            <button 
                :disabled="currentPage <= 1" 
                @click="$emit('page-changed', currentPage - 1)"
                class="px-4 py-2.5 rounded-xl border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed bg-white hover:bg-gray-50 hover:border-blue-500 hover:text-blue-600 transition-all duration-200 disabled:hover:bg-white disabled:hover:border-gray-300 disabled:hover:text-gray-400 shadow-sm hover:shadow-md">
                <ChevronLeftIcon class="w-5 h-5" />
            </button>

            <div class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold shadow-lg">
                {{ currentPage }}
            </div>

            <button 
                :disabled="currentPage >= totalPages" 
                @click="$emit('page-changed', currentPage + 1)"
                class="px-4 py-2.5 rounded-xl border border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed bg-white hover:bg-gray-50 hover:border-blue-500 hover:text-blue-600 transition-all duration-200 disabled:hover:bg-white disabled:hover:border-gray-300 disabled:hover:text-gray-400 shadow-sm hover:shadow-md">
                <ChevronRightIcon class="w-5 h-5" />
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
