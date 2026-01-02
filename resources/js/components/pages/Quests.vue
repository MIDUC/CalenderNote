<template>
    <div class="space-y-6">

        <LoadingSpinner v-if="loading" />
        <EmptyState 
            v-else-if="quests.length === 0"
            icon="⚔️"
            title="Chưa có nhiệm vụ nào"
            message="Hãy tìm NPC để nhận nhiệm vụ!"
        />
        <div v-else class="space-y-4">
            <div 
                v-for="quest in quests" 
                :key="quest.id"
                class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg border border-gray-200/50 p-6"
            >
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-800">{{ quest.title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ quest.description }}</p>
                        <div v-if="quest.npc" class="mt-2 text-xs text-gray-500">
                            Từ: {{ quest.npc.name }}
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                        :class="{
                            'bg-green-100 text-green-700': quest.status === 'completed',
                            'bg-yellow-100 text-yellow-700': quest.status === 'claimed',
                            'bg-blue-100 text-blue-700': quest.status === 'in_progress',
                            'bg-gray-100 text-gray-700': quest.status === 'available'
                        }"
                    >
                        {{ getStatusLabel(quest.status) }}
                    </span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span>Tiến độ:</span>
                        <span class="font-semibold">{{ quest.progress || 0 }} / {{ quest.target_count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div 
                            class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all"
                            :style="{ width: `${Math.min(100, ((quest.progress || 0) / quest.target_count) * 100)}%` }"
                        ></div>
                    </div>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <div class="flex gap-4">
                        <span>🎁 XP: {{ quest.xp_reward }}</span>
                        <span>💎 {{ formatCurrency(quest.currency_reward) }}</span>
                        <span v-if="quest.item_reward">📦 {{ quest.item_reward.name }} x{{ quest.item_reward_quantity }}</span>
                    </div>
                    <button 
                        v-if="quest.status === 'completed'"
                        @click="claimReward(quest)"
                        class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl font-semibold hover:shadow-lg transition-all"
                    >
                        Nhận thưởng
                    </button>
                    <span v-else-if="quest.status === 'claimed'" class="px-4 py-2 bg-gray-200 text-gray-600 rounded-xl font-semibold">
                        Đã nhận
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../../api'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import EmptyState from '../common/EmptyState.vue'

const toast = useToast()
const quests = ref([])
const loading = ref(true)

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const getStatusLabel = (status) => {
    const labels = {
        'available': 'Có sẵn',
        'in_progress': 'Đang làm',
        'completed': 'Hoàn thành',
        'claimed': 'Đã nhận thưởng'
    }
    return labels[status] || status
}

const loadQuests = async () => {
    loading.value = true
    try {
        const res = await api.get('/quest/list')
        quests.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải nhiệm vụ:', error)
        toast.error('Không thể tải danh sách nhiệm vụ')
    } finally {
        loading.value = false
    }
}

const claimReward = async (quest) => {
    try {
        const res = await api.post(`/quest/claim/${quest.id}`)
        toast.success('Đã nhận thưởng thành công!')
        if (res.data?.data?.rewards) {
            const rewards = res.data.data.rewards
            if (rewards.xp) {
                toast.info(`+${rewards.xp} XP`)
            }
            if (rewards.currency) {
                toast.info(`+${formatCurrency(rewards.currency)} linh thạch`)
            }
            if (rewards.level_result?.leveled_up) {
                toast.success(`🎉 Lên cấp ${rewards.level_result.new_level}!`)
            }
        }
        await loadQuests()
    } catch (error) {
        console.error('Lỗi nhận thưởng:', error)
        toast.error(error.response?.data?.message || 'Nhận thưởng thất bại')
    }
}

onMounted(loadQuests)
</script>

