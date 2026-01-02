<template>
  <div class="flex items-center justify-center py-12" :class="containerClass">
    <div class="flex flex-col items-center gap-3">
      <div 
        :class="spinnerClass"
        class="border-4 border-blue-500 border-t-transparent rounded-full animate-spin">
      </div>
      <p v-if="message" class="text-gray-500 font-medium">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  message: {
    type: String,
    default: 'Đang tải dữ liệu...',
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md', 'lg'
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  containerClass: {
    type: String,
    default: '',
  },
})

const spinnerClass = computed(() => {
  const sizes = {
    sm: 'w-8 h-8',
    md: 'w-12 h-12',
    lg: 'w-16 h-16',
  }
  return sizes[props.size] || sizes.md
})
</script>

