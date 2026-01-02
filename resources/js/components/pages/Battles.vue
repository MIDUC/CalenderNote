<template>
    <div class="space-y-6">

        <LoadingSpinner v-if="loading" />
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
                v-for="monster in monsters" 
                :key="monster.id"
                class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4 hover:shadow-lg transition-all duration-300"
            >
                <div class="text-center mb-3">
                    <div class="text-5xl mb-2">{{ monster.icon || '👹' }}</div>
                    <h3 class="font-bold text-base text-gray-800">{{ monster.name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Cấp {{ monster.level }}</p>
                </div>

                <div class="space-y-1.5 mb-3 text-xs">
                    <div class="flex justify-between">
                        <span>HP:</span>
                        <span class="font-semibold">{{ monster.hp }} / {{ monster.max_hp }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tấn công:</span>
                        <span class="font-semibold text-red-600">{{ monster.attack }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phòng thủ:</span>
                        <span class="font-semibold text-blue-600">{{ monster.defense }}</span>
                    </div>
                </div>

                <div class="mb-3 p-2 bg-gray-50 rounded-lg">
                    <div class="text-xs text-gray-600 mb-1">Phần thưởng:</div>
                    <div class="flex justify-between text-xs">
                        <span>🎁 XP: {{ monster.xp_reward }}</span>
                        <span>💎 {{ formatCurrency(monster.currency_reward) }}</span>
                    </div>
                </div>

                <button 
                    @click="startBattle(monster)"
                    class="w-full bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white px-3 py-2 rounded-lg font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200"
                >
                    Chiến đấu
                </button>
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
const monsters = ref([])
const loading = ref(true)

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const loadMonsters = async () => {
    loading.value = true
    try {
        const res = await api.get('/battle/monsters')
        monsters.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải quái:', error)
        toast.error('Không thể tải danh sách quái')
    } finally {
        loading.value = false
    }
}

const startBattle = async (monster) => {
    try {
        const res = await api.post(`/battle/start/${monster.id}`)
        toast.success('Bắt đầu trận đấu!')
        // TODO: Navigate to battle screen or show battle modal
        await attackMonster(res.data.data.id)
    } catch (error) {
        console.error('Lỗi bắt đầu trận đấu:', error)
        toast.error(error.response?.data?.message || 'Không thể bắt đầu trận đấu')
    }
}

const attackMonster = async (battleId) => {
    try {
        const res = await api.post(`/battle/attack/${battleId}`)
        const battle = res.data?.data?.battle
        const log = res.data?.data?.battle_log || []
        
        if (battle.status === 'won') {
            toast.success('Chiến thắng!')
            if (battle.xp_gained) toast.info(`+${battle.xp_gained} XP`)
            if (battle.currency_gained) toast.info(`+${formatCurrency(battle.currency_gained)} 💎`)
        } else if (battle.status === 'lost') {
            toast.error('Thất bại!')
        } else {
            // Continue battle
            setTimeout(() => attackMonster(battleId), 1000)
        }
    } catch (error) {
        console.error('Lỗi tấn công:', error)
        toast.error('Không thể tấn công')
    }
}

onMounted(loadMonsters)
</script>

