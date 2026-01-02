<template>
    <div class="space-y-4">
        <h1 class="text-lg font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
            📊 Thống kê
        </h1>

        <LoadingSpinner v-if="loading" />
        <div v-else-if="stats" class="space-y-4">
            <!-- User Overview -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">Tổng quan</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="text-center p-2.5 bg-blue-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Cấp độ</p>
                        <p class="text-lg font-bold text-blue-600">{{ stats.user.level }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-purple-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Kinh nghiệm</p>
                        <p class="text-lg font-bold text-purple-600">{{ formatCurrency(stats.user.exp) }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-yellow-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Linh thạch</p>
                        <p class="text-lg font-bold text-yellow-600">{{ formatCurrency(stats.user.currency) }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-orange-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Chuỗi ngày</p>
                        <p class="text-lg font-bold text-orange-600">{{ stats.user.current_streak }}</p>
                    </div>
                </div>
            </div>

            <!-- Task Statistics -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">📋 Công việc</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="text-center p-2.5 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Tổng số</p>
                        <p class="text-lg font-bold text-gray-800">{{ stats.tasks.total }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-green-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Hoàn thành</p>
                        <p class="text-lg font-bold text-green-600">{{ stats.tasks.completed }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-red-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Thất bại</p>
                        <p class="text-lg font-bold text-red-600">{{ stats.tasks.failed }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-yellow-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Đang làm</p>
                        <p class="text-lg font-bold text-yellow-600">{{ stats.tasks.pending }}</p>
                    </div>
                </div>
            </div>

            <!-- Battle Statistics -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">⚔️ Chiến đấu</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="text-center p-2.5 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Tổng trận</p>
                        <p class="text-lg font-bold text-gray-800">{{ stats.battles.total }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-green-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Thắng</p>
                        <p class="text-lg font-bold text-green-600">{{ stats.battles.won }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-red-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Thua</p>
                        <p class="text-lg font-bold text-red-600">{{ stats.battles.lost }}</p>
                    </div>
                    <div class="text-center p-2.5 bg-purple-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">XP kiếm được</p>
                        <p class="text-lg font-bold text-purple-600">{{ formatCurrency(stats.battles.total_xp_gained) }}</p>
                    </div>
                </div>
            </div>

            <!-- Daily Activity -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">📅 Hoạt động 7 ngày qua</h2>
                <div class="space-y-2">
                    <div 
                        v-for="day in stats.daily_activity" 
                        :key="day.date"
                        class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"
                    >
                        <span class="text-xs text-gray-600">{{ formatDate(day.date) }}</span>
                        <div class="flex gap-4 text-xs">
                            <span>📋 {{ day.tasks_completed }} tasks</span>
                            <span>📝 {{ day.notes_created }} notes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useFormat } from '../../composables'
import api from '../../api'
import LoadingSpinner from '../common/LoadingSpinner.vue'

const { formatDate } = useFormat()
const stats = ref(null)
const loading = ref(true)

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const loadStatistics = async () => {
    loading.value = true
    try {
        const res = await api.get('/statistics/index')
        stats.value = res.data?.data || null
    } catch (error) {
        console.error('Lỗi tải thống kê:', error)
    } finally {
        loading.value = false
    }
}

onMounted(loadStatistics)
</script>

