<template>
    <div class="space-y-6">
        <LoadingSpinner v-if="loading" />
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
                v-for="npc in npcs" 
                :key="npc.id"
                class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg border border-gray-200/50 p-6 hover:shadow-xl transition-all duration-300 cursor-pointer"
                @click="interactWithNPC(npc)"
            >
                <div class="text-center mb-4">
                    <div class="text-6xl mb-2">{{ npc.icon || '👤' }}</div>
                    <h3 class="font-bold text-lg text-gray-800">{{ npc.name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ npc.description }}</p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold"
                        :class="{
                            'bg-blue-100 text-blue-700': npc.type === 'quest_giver',
                            'bg-green-100 text-green-700': npc.type === 'merchant',
                            'bg-purple-100 text-purple-700': npc.type === 'trainer',
                            'bg-gray-100 text-gray-700': npc.type === 'guard'
                        }"
                    >
                        {{ getNPCTypeLabel(npc.type) }}
                    </span>
                </div>
                
                <div v-if="npc.quests && npc.quests.length > 0" class="text-sm text-gray-600">
                    <span class="font-semibold">{{ npc.quests.length }}</span> nhiệm vụ có sẵn
                </div>
                <div v-else class="text-sm text-gray-400">
                    Không có nhiệm vụ
                </div>
            </div>
        </div>

        <!-- NPC Interaction Modal -->
        <transition name="modal">
            <div v-if="showNPCModal && activeNPC" 
                @click.self="closeNPCModal"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-3xl p-6 lg:p-8 w-full max-w-2xl shadow-2xl relative overflow-y-auto max-h-[90vh]">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-4">
                            <div class="text-6xl">{{ activeNPC.icon || '👤' }}</div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">{{ activeNPC.name }}</h2>
                                <p class="text-sm text-gray-500">{{ activeNPC.description }}</p>
                            </div>
                        </div>
                        <button @click="closeNPCModal" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Dialogue -->
                    <div v-if="activeNPC.dialogue" class="mb-6 p-4 bg-blue-50 rounded-xl">
                        <p class="text-gray-700 italic">"{{ activeNPC.dialogue.greeting || 'Xin chào!' }}"</p>
                    </div>

                    <!-- Available Quests -->
                    <div v-if="availableQuests.length > 0">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Nhiệm vụ có sẵn:</h3>
                        <div class="space-y-3">
                            <div 
                                v-for="quest in availableQuests" 
                                :key="quest.id"
                                class="bg-gray-50 rounded-xl p-4 border border-gray-200"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-800">{{ quest.title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ quest.description }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center mt-3">
                                    <div class="text-sm text-gray-600">
                                        <span>Yêu cầu: {{ quest.target_count }} {{ getQuestTypeLabel(quest.type) }}</span>
                                    </div>
                                    <div class="flex gap-2 text-sm">
                                        <span class="text-yellow-600">🎁 {{ quest.xp_reward }} XP</span>
                                        <span class="text-purple-600">💎 {{ formatCurrency(quest.currency_reward) }}</span>
                                    </div>
                                </div>
                                
                                <button 
                                    @click="acceptQuest(quest)"
                                    class="w-full mt-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white px-4 py-2 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200"
                                >
                                    Nhận nhiệm vụ
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <p>Không có nhiệm vụ nào khả dụng tại thời điểm này.</p>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../../api'
import LoadingSpinner from '../common/LoadingSpinner.vue'

const toast = useToast()
const npcs = ref([])
const loading = ref(true)
const showNPCModal = ref(false)
const activeNPC = ref(null)
const availableQuests = ref([])

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const getNPCTypeLabel = (type) => {
    const labels = {
        'quest_giver': 'Người giao nhiệm vụ',
        'merchant': 'Thương nhân',
        'trainer': 'Huấn luyện viên',
        'guard': 'Vệ binh'
    }
    return labels[type] || type
}

const getQuestTypeLabel = (type) => {
    const labels = {
        'task': 'task',
        'kill': 'quái vật',
        'collect': 'vật phẩm',
        'explore': 'địa điểm',
        'daily': 'nhiệm vụ hàng ngày'
    }
    return labels[type] || type
}

const loadNPCs = async () => {
    loading.value = true
    try {
        const res = await api.get('/npc/list')
        npcs.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải NPCs:', error)
        toast.error('Không thể tải danh sách NPC')
    } finally {
        loading.value = false
    }
}

const interactWithNPC = async (npc) => {
    try {
        const res = await api.post(`/npc/interact/${npc.id}`)
        activeNPC.value = res.data?.data?.npc || npc
        availableQuests.value = res.data?.data?.available_quests || []
        showNPCModal.value = true
    } catch (error) {
        console.error('Lỗi tương tác với NPC:', error)
        toast.error(error.response?.data?.message || 'Không thể tương tác với NPC')
    }
}

const acceptQuest = async (quest) => {
    try {
        const res = await api.post(`/quest/accept/${quest.id}`)
        toast.success(`Đã nhận nhiệm vụ: ${quest.title}!`)
        closeNPCModal()
        // Reload NPCs to update quest count
        await loadNPCs()
    } catch (error) {
        console.error('Lỗi nhận nhiệm vụ:', error)
        toast.error(error.response?.data?.message || 'Không thể nhận nhiệm vụ')
    }
}

const closeNPCModal = () => {
    showNPCModal.value = false
    activeNPC.value = null
    availableQuests.value = []
}

onMounted(loadNPCs)
</script>

