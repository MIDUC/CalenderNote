<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="text-6xl mb-4">{{ icon }}</div>
    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ title }}</h3>
    <p class="text-gray-500 mb-6 max-w-md">{{ message }}</p>
    <slot name="action">
      <button 
        v-if="showAction"
        @click="$emit('action')"
        class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105">
        {{ actionLabel }}
      </button>
    </slot>
  </div>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    default: '📭',
  },
  title: {
    type: String,
    default: 'Không có dữ liệu',
  },
  message: {
    type: String,
    default: 'Hiện tại chưa có dữ liệu để hiển thị.',
  },
  showAction: {
    type: Boolean,
    default: false,
  },
  actionLabel: {
    type: String,
    default: 'Thêm mới',
  },
})

defineEmits(['action'])
</script>

