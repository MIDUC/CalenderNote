<template>
  <span :class="badgeClasses" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold">
    <span v-if="showDot" :class="dotClasses" class="w-2 h-2 rounded-full"></span>
    <span v-if="showDot && animate" class="animate-pulse"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { TASK_STATUS_LABELS, TASK_STATUS_COLORS } from '../../utils/constants'

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'task', // 'task' or 'schedule'
  },
  showDot: {
    type: Boolean,
    default: true,
  },
  animate: {
    type: Boolean,
    default: false,
  },
})

const label = computed(() => {
  if (props.type === 'task') {
    return TASK_STATUS_LABELS[props.status] || props.status
  }
  // Schedule status
  if (props.status === 'active' || props.status === true || props.status === 1) {
    return 'Đang chạy'
  }
  return 'Đã dừng'
})

const badgeClasses = computed(() => {
  if (props.type === 'task') {
    const color = TASK_STATUS_COLORS[props.status] || 'bg-gray-500'
    return `${color} text-white`
  }
  // Schedule status
  if (props.status === 'active' || props.status === true || props.status === 1) {
    return 'bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-700 border border-emerald-200'
  }
  return 'bg-gray-100 text-gray-600 border border-gray-200'
})

const dotClasses = computed(() => {
  if (props.type === 'task') {
    return 'bg-white'
  }
  if (props.status === 'active' || props.status === true || props.status === 1) {
    return 'bg-emerald-500'
  }
  return 'bg-gray-400'
})
</script>

