<template>
    <div class="space-y-6">
        <div class="flex justify-end items-center mb-4">
            <div class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-100 to-pink-100 rounded-xl">
                <span class="text-2xl">💎</span>
                <span class="font-bold text-purple-600">{{ formatCurrency(userCurrency) }}</span>
            </div>
        </div>

        <LoadingSpinner v-if="loading" />
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
                v-for="item in items" 
                :key="item.id"
                class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-lg border border-gray-200/50 p-6 hover:shadow-xl transition-all duration-300"
            >
                <div class="text-center mb-4">
                    <div class="text-6xl mb-2">{{ item.icon || '📦' }}</div>
                    <h3 class="font-bold text-lg text-gray-800">{{ item.name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ item.description }}</p>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Giá mua:</span>
                        <span class="font-semibold text-purple-600">💎 {{ formatCurrency(item.price) }}</span>
                    </div>
                    <div v-if="item.sell_price > 0" class="flex justify-between text-sm">
                        <span class="text-gray-600">Giá bán:</span>
                        <span class="font-semibold text-green-600">💎 {{ formatCurrency(item.sell_price) }}</span>
                    </div>
                </div>

                <button 
                    @click="buyItem(item)"
                    :disabled="userCurrency < item.price"
                    class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-4 py-2 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Mua ngay
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

const toast = useToast()
const items = ref([])
const loading = ref(true)
const userCurrency = ref(0)

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const loadItems = async () => {
    loading.value = true
    try {
        const res = await api.get('/shop/items')
        items.value = res.data?.data || res.data || []
    } catch (error) {
        console.error('Lỗi tải cửa hàng:', error)
        toast.error('Không thể tải danh sách vật phẩm')
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

const buyItem = async (item) => {
    if (userCurrency.value < item.price) {
        toast.error('Không đủ linh thạch!')
        return
    }

    try {
        const res = await api.post(`/shop/buy/${item.id}`, { quantity: 1 })
        toast.success(`Đã mua ${item.name} thành công!`)
        await loadUserCurrency()
    } catch (error) {
        console.error('Lỗi mua vật phẩm:', error)
        toast.error(error.response?.data?.message || 'Mua vật phẩm thất bại')
    }
}

onMounted(() => {
    loadItems()
    loadUserCurrency()
})
</script>

