<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                🎒 Túi đồ
            </h1>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg">
                <span class="text-xl">💎</span>
                <span class="font-bold text-purple-600 text-sm">{{ formatCurrency(userCurrency) }}</span>
            </div>
        </div>

        <LoadingSpinner v-if="loading" />
        <EmptyState 
            v-else-if="inventory.length === 0"
            icon="🎒"
            title="Túi đồ trống"
            message="Hãy mua vật phẩm từ cửa hàng để bắt đầu!"
        />
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            <div 
                v-for="item in inventory" 
                :key="item.id"
                class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-3.5 hover:shadow-lg transition-all duration-300"
            >
                <div class="flex items-start gap-3">
                    <div class="text-4xl flex-shrink-0">{{ item.icon || '📦' }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-semibold text-sm text-gray-800 truncate">{{ item.name }}</h3>
                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">
                                x{{ item.pivot.quantity }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-2 line-clamp-2">{{ item.description }}</p>
                        <div class="flex items-center gap-2 text-xs text-gray-600 mb-2">
                            <span v-if="item.type === 'consumable'" class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded">Tiêu hao</span>
                            <span v-if="item.type === 'equipment'" class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded">Trang bị</span>
                            <span v-if="item.type === 'material'" class="px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded">Nguyên liệu</span>
                            <span v-if="item.type === 'special'" class="px-1.5 py-0.5 bg-pink-100 text-pink-700 rounded">Đặc biệt</span>
                        </div>
                        <div v-if="item.effects" class="text-xs text-gray-500 mb-2">
                            <span v-if="item.effects.hp_restore">+{{ item.effects.hp_restore }} HP</span>
                            <span v-if="item.effects.exp_bonus"> +{{ item.effects.exp_bonus }} XP</span>
                            <span v-if="item.effects.currency_bonus"> +{{ item.effects.currency_bonus }} 💎</span>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                v-if="item.sell_price > 0"
                                @click="sellItem(item)"
                                class="flex-1 px-2.5 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg font-medium transition-colors"
                            >
                                Bán ({{ formatCurrency(item.sell_price) }})
                            </button>
                            <button 
                                v-if="item.type === 'consumable' && item.effects?.hp_restore"
                                @click="useItem(item)"
                                class="flex-1 px-2.5 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs rounded-lg font-medium transition-colors"
                            >
                                Sử dụng
                            </button>
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
import EmptyState from '../common/EmptyState.vue'

const toast = useToast()
const inventory = ref([])
const loading = ref(true)
const userCurrency = ref(0)

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const loadInventory = async () => {
    loading.value = true
    try {
        const res = await api.get('/shop/inventory')
        inventory.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải túi đồ:', error)
        toast.error('Không thể tải túi đồ')
    } finally {
        loading.value = false
    }
}

const loadUserCurrency = async () => {
    try {
        const res = await api.get('/me')
        userCurrency.value = res.data?.currency || 0
    } catch (error) {
        console.error('Lỗi tải thông tin user:', error)
    }
}

const sellItem = async (item) => {
    try {
        await api.post(`/shop/sell/${item.id}`, { quantity: 1 })
        toast.success(`Đã bán ${item.name} thành công!`)
        await loadInventory()
        await loadUserCurrency()
    } catch (error) {
        console.error('Lỗi bán vật phẩm:', error)
        toast.error(error.response?.data?.message || 'Bán vật phẩm thất bại')
    }
}

const useItem = async (item) => {
    // TODO: Implement item usage logic (e.g., restore HP)
    toast.info('Tính năng sử dụng vật phẩm đang được phát triển!')
}

onMounted(() => {
    loadInventory()
    loadUserCurrency()
})
</script>

