<template>
    <div class="w-full">
        <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold text-gray-700">Cấp {{ level }}</span>
                <span v-if="levelName" class="text-xs px-1.5 py-0.5 rounded-full bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 font-medium">
                    {{ levelName }}
                </span>
            </div>
            <div class="text-xs text-gray-500">
                {{ currentExp || exp }} / {{ nextLevelExp }} XP
            </div>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden shadow-inner">
            <div 
                class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full transition-all duration-500 ease-out relative overflow-hidden"
                :style="{ width: `${progress}%` }"
            >
                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
            </div>
        </div>
        <div v-if="expToNext" class="text-xs text-gray-400 mt-0.5 text-right">
            Còn {{ expToNext }} XP để lên cấp {{ level + 1 }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    level: {
        type: Number,
        default: 1,
    },
    exp: {
        type: Number,
        default: 0,
    },
    levelName: {
        type: String,
        default: null,
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

const progress = computed(() => {
    const exp = props.currentExp || props.exp || 0
    if (props.nextLevelExp <= 0) return 100
    return Math.min(100, (exp / props.nextLevelExp) * 100)
})

const expToNext = computed(() => {
    const exp = props.currentExp || props.exp || 0
    const remaining = props.nextLevelExp - exp
    return remaining > 0 ? remaining : 0
})
</script>

<style scoped>
@keyframes pulse {
    0%, 100% {
        opacity: 0.2;
    }
    50% {
        opacity: 0.4;
    }
}
</style>

