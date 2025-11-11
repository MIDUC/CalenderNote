<template>
    <div :class="notificationClasses" role="alert">

        <div class="flex-shrink-0 mr-3">
            <svg v-if="type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <svg v-else-if="type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path
                    d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 13a1 1 0 112 0v2a1 1 0 11-2 0v-2zm1-8a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" />
            </svg>
        </div>

        <p class="text-sm font-medium">
            {{ message }}
        </p>

        <div v-if="closable" class="ml-auto pl-3">
            <button @click="$emit('close')" type="button" class="p-1 rounded-md transition duration-150 ease-in-out"
                :class="closeButtonClasses">
                <span class="sr-only">Đóng</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    /** 'success', 'error', 'info' */
    type: {
        type: String,
        default: 'info',
    },
    message: {
        type: String,
        required: true,
    },
    closable: {
        type: Boolean,
        default: true,
    }
});

const emit = defineEmits(['close']);

/** Tính toán các lớp CSS nền động dựa trên loại thông báo */
const notificationClasses = computed(() => {
    // Lớp cơ bản (Base classes)
    let base = 'max-w-md w-full p-4 rounded-lg shadow-lg flex items-start text-white';

    switch (props.type) {
        case 'success':
            return `${base} bg-green-500`;
        case 'error':
            return `${base} bg-red-500`;
        default:
            return `${base} bg-blue-500`; // info (mặc định)
    }
});

/** Tính toán các lớp CSS động cho nút đóng */
const closeButtonClasses = computed(() => {
    let base = 'opacity-80 hover:opacity-100';
    switch (props.type) {
        case 'success':
            return `${base} hover:bg-green-600`;
        case 'error':
            return `${base} hover:bg-red-600`;
        default:
            return `${base} hover:bg-blue-600`;
    }
});
</script>