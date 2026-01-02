<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">
                🎁 Phần thưởng đăng nhập
            </h1>
            <div v-if="rewardStatus" class="flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-orange-100 to-yellow-100 rounded-lg">
                <span class="text-xl">🔥</span>
                <span class="font-bold text-orange-600 text-sm">{{ rewardStatus.login_streak }} ngày</span>
            </div>
        </div>

        <LoadingSpinner v-if="loading" />
        <div v-else class="space-y-4">
            <!-- Current reward status -->
            <div v-if="rewardStatus" class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <div class="text-center mb-3">
                    <p class="text-sm text-gray-600 mb-2">Ngày {{ rewardStatus.current_day }}/7</p>
                    <div v-if="rewardStatus.reward" class="space-y-2">
                        <div class="text-4xl mb-2">🎁</div>
                        <h3 class="font-bold text-base text-gray-800">{{ rewardStatus.reward.description }}</h3>
                        <div class="flex justify-center gap-4 text-sm">
                            <span v-if="rewardStatus.reward.xp_reward > 0">🎁 +{{ rewardStatus.reward.xp_reward }} XP</span>
                            <span v-if="rewardStatus.reward.currency_reward > 0">💎 +{{ formatCurrency(rewardStatus.reward.currency_reward) }}</span>
                            <span v-if="rewardStatus.reward.item_reward">📦 {{ rewardStatus.reward.item_reward.name }} x{{ rewardStatus.reward.item_reward_quantity }}</span>
                        </div>
                    </div>
                </div>
                <button 
                    v-if="rewardStatus.can_claim"
                    @click="claimReward"
                    class="w-full px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200"
                >
                    Nhận phần thưởng
                </button>
                <div v-else class="text-center text-sm text-gray-500 py-2">
                    Đã nhận phần thưởng hôm nay
                </div>
            </div>

            <!-- Weekly calendar -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">Lịch phần thưởng tuần</h2>
                <div class="grid grid-cols-7 gap-2">
                    <div 
                        v-for="(reward, index) in rewards" 
                        :key="reward.id"
                        class="text-center p-2 rounded-lg border-2 transition-all"
                        :class="{
                            'bg-gradient-to-br from-yellow-100 to-orange-100 border-yellow-400': rewardStatus && reward.day === rewardStatus.current_day && rewardStatus.can_claim,
                            'bg-gray-50 border-gray-300': rewardStatus && reward.day < rewardStatus.current_day,
                            'bg-gray-100 border-gray-200 opacity-50': rewardStatus && reward.day > rewardStatus.current_day,
                            'bg-white border-gray-200': !rewardStatus
                        }"
                    >
                        <div class="text-xs font-semibold text-gray-600 mb-1">Ngày {{ reward.day }}</div>
                        <div class="text-2xl mb-1">🎁</div>
                        <div class="text-xs text-gray-600 space-y-0.5">
                            <div v-if="reward.xp_reward > 0">+{{ reward.xp_reward }} XP</div>
                            <div v-if="reward.currency_reward > 0">+{{ reward.currency_reward }} 💎</div>
                        </div>
                        <div v-if="rewardStatus && reward.day === rewardStatus.current_day" class="mt-1">
                            <span class="text-xs px-1.5 py-0.5 bg-yellow-400 text-yellow-900 rounded font-semibold">Hôm nay</span>
                        </div>
                    </div>
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

const toast = useToast()
const rewards = ref([])
const rewardStatus = ref(null)
const loading = ref(true)

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const loadRewards = async () => {
    loading.value = true
    try {
        const [rewardsRes, statusRes] = await Promise.all([
            api.get('/daily-reward/list'),
            api.get('/daily-reward/check')
        ])
        rewards.value = rewardsRes.data?.data || []
        rewardStatus.value = statusRes.data?.data || null
    } catch (error) {
        console.error('Lỗi tải phần thưởng:', error)
        toast.error('Không thể tải phần thưởng đăng nhập')
    } finally {
        loading.value = false
    }
}

const claimReward = async () => {
    try {
        const res = await api.post('/daily-reward/claim')
        toast.success('Đã nhận phần thưởng thành công!')
        
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
            if (rewards.item) {
                toast.info(`📦 Nhận ${rewards.item.name} x${rewards.item_quantity}`)
            }
        }
        
        await loadRewards()
    } catch (error) {
        console.error('Lỗi nhận phần thưởng:', error)
        toast.error(error.response?.data?.message || 'Nhận phần thưởng thất bại')
    }
}

onMounted(loadRewards)
</script>

