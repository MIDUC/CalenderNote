<template>
    <div class="space-y-4">
        <h1 class="text-lg font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">
            🏆 Thành tích
        </h1>

        <LoadingSpinner v-if="loading" />
        <EmptyState 
            v-else-if="achievements.length === 0"
            icon="🏆"
            title="Chưa có thành tích nào"
            message="Hãy hoàn thành các nhiệm vụ để mở khóa thành tích!"
        />
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            <div 
                v-for="achievement in achievements" 
                :key="achievement.id"
                class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4 hover:shadow-lg transition-all duration-300"
                :class="{
                    'opacity-50': !achievement.is_unlocked,
                    'ring-2 ring-yellow-400': achievement.is_unlocked
                }"
            >
                <div class="text-center mb-3">
                    <div class="text-5xl mb-2">{{ achievement.icon || '🏆' }}</div>
                    <h3 class="font-bold text-base text-gray-800">{{ achievement.name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ achievement.description }}</p>
                </div>

                <div v-if="!achievement.is_unlocked" class="space-y-2 mb-4">
                    <div class="text-xs text-gray-600 mb-1">Tiến độ:</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div 
                            class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all"
                            :style="{ width: `${Math.min(100, (achievement.progress / (achievement.requirements?.target || 1)) * 100)}%` }"
                        ></div>
                    </div>
                    <div class="text-xs text-center text-gray-500">
                        {{ achievement.progress || 0 }} / {{ achievement.requirements?.target || 1 }}
                    </div>
                </div>

                <div v-else class="mb-4 p-3 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl">
                    <div class="text-center text-sm font-semibold text-yellow-700">✅ Đã mở khóa</div>
                    <div v-if="achievement.unlocked_at" class="text-xs text-center text-gray-500 mt-1">
                        {{ formatDate(achievement.unlocked_at) }}
                    </div>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <div class="flex gap-4">
                        <span>🎁 XP: {{ achievement.xp_reward }}</span>
                        <span>💎 {{ formatCurrency(achievement.currency_reward) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { useFormat } from '../../composables'
import api from '../../api'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import EmptyState from '../common/EmptyState.vue'

const toast = useToast()
const { formatDate } = useFormat()
const achievements = ref([])
const loading = ref(true)

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const loadAchievements = async () => {
    loading.value = true
    try {
        const res = await api.get('/achievement/list')
        achievements.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải thành tích:', error)
        toast.error('Không thể tải danh sách thành tích')
    } finally {
        loading.value = false
    }
}

onMounted(loadAchievements)
</script>

