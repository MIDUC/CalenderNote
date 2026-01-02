<template>
    <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold shadow-md">
                {{ userInitials }}
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-bold text-gray-800 truncate">{{ user.name }}</h3>
                <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
            </div>
        </div>

        <!-- XP Bar -->
        <XPBar 
            :level="user.level || 1"
            :exp="user.exp || 0"
            :level-name="user.level_name"
            :next-level-exp="nextLevelExp"
            :current-exp="currentExp"
        />

        <!-- Currency & Streak -->
        <div class="mt-3 pt-3 border-t border-gray-200 space-y-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="text-xl">💎</span>
                    <span class="text-xs font-medium text-gray-700">Linh thạch</span>
                </div>
                <span class="text-base font-bold text-purple-600">{{ formatCurrency(user.currency || 0) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="text-xl">🔥</span>
                    <span class="text-xs font-medium text-gray-700">Chuỗi ngày</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-orange-600">{{ user.current_streak || 0 }} ngày</span>
                    <span v-if="user.longest_streak > 0" class="text-xs text-gray-500">
                        (Kỷ lục: {{ user.longest_streak }})
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import XPBar from './XPBar.vue'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    nextLevelExp: {
        type: Number,
        default: 100,
    },
    currentExp: {
        type: Number,
        default: 0,
    },
})

const userInitials = computed(() => {
    if (!props.user?.name) return 'U'
    const names = props.user.name.split(' ')
    if (names.length >= 2) {
        return (names[0][0] + names[names.length - 1][0]).toUpperCase()
    }
    return props.user.name.substring(0, 2).toUpperCase()
})

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}
</script>

