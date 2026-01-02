<template>
    <div class="space-y-4">
        <h1 class="text-lg font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">
            🏆 Bảng xếp hạng
        </h1>

        <!-- Tabs -->
        <div class="flex gap-2 border-b border-gray-200">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                class="px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === tab.key 
                    ? 'text-blue-600 border-b-2 border-blue-600' 
                    : 'text-gray-500 hover:text-gray-700'"
            >
                {{ tab.label }}
            </button>
        </div>

        <LoadingSpinner v-if="loading" />
        <div v-else class="space-y-2">
            <div 
                v-for="(user, index) in leaderboard" 
                :key="user.id"
                class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-3 flex items-center gap-3"
                :class="{
                    'ring-2 ring-yellow-400': index === 0,
                    'ring-2 ring-gray-300': index === 1,
                    'ring-2 ring-orange-300': index === 2
                }"
            >
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm"
                    :class="{
                        'bg-yellow-400 text-yellow-900': index === 0,
                        'bg-gray-300 text-gray-700': index === 1,
                        'bg-orange-300 text-orange-900': index === 2,
                        'bg-gray-200 text-gray-600': index > 2
                    }"
                >
                    {{ index + 1 }}
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">{{ user.name }}</p>
                    <p class="text-xs text-gray-500">Cấp {{ user.level }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-base"
                        :class="{
                            'text-yellow-600': index === 0,
                            'text-gray-600': index === 1,
                            'text-orange-600': index === 2,
                            'text-gray-500': index > 2
                        }"
                    >
                        {{ getValue(user) }}
                    </p>
                    <p class="text-xs text-gray-500">{{ getLabel() }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../../api'
import LoadingSpinner from '../common/LoadingSpinner.vue'

const activeTab = ref('level')
const leaderboard = ref([])
const loading = ref(true)

const tabs = [
    { key: 'level', label: 'Cấp độ' },
    { key: 'tasks', label: 'Công việc' },
    { key: 'currency', label: 'Linh thạch' },
    { key: 'streak', label: 'Chuỗi ngày' },
]

const getValue = (user) => {
    switch (activeTab.value) {
        case 'level':
            return `Cấp ${user.level}`
        case 'tasks':
            return `${user.tasks_count || 0} tasks`
        case 'currency':
            return formatCurrency(user.currency || 0)
        case 'streak':
            return `${user.current_streak || 0} ngày`
        default:
            return ''
    }
}

const getLabel = () => {
    switch (activeTab.value) {
        case 'level':
            return 'Cấp độ'
        case 'tasks':
            return 'Hoàn thành'
        case 'currency':
            return 'Linh thạch'
        case 'streak':
            return 'Chuỗi ngày'
        default:
            return ''
    }
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const loadLeaderboard = async () => {
    loading.value = true
    try {
        const res = await api.get(`/leaderboard/${activeTab.value}`)
        leaderboard.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải bảng xếp hạng:', error)
    } finally {
        loading.value = false
    }
}

watch(activeTab, () => {
    loadLeaderboard()
})

onMounted(loadLeaderboard)
</script>

