<template>
    <div class="space-y-4">
        <h1 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
            ⚔️ Thông số nhân vật
        </h1>

        <LoadingSpinner v-if="loading" />
        <div v-else-if="user" class="space-y-3">
            <!-- Thông tin cơ bản -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">Thông tin cơ bản</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">👤</span>
                        <div>
                            <p class="text-xs text-gray-500">Tên</p>
                            <p class="text-sm font-semibold text-gray-800">{{ user.name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📧</span>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ user.email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">⭐</span>
                        <div>
                            <p class="text-xs text-gray-500">Cấp độ</p>
                            <p class="text-sm font-semibold text-gray-800">Cấp {{ user.level || 1 }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">💎</span>
                        <div>
                            <p class="text-xs text-gray-500">Linh thạch</p>
                            <p class="text-sm font-semibold text-gray-800">{{ formatCurrency(user.currency || 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông số chiến đấu -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">Thông số chiến đấu</h2>
                <div class="space-y-3">
                    <!-- HP -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">❤️</span>
                                <span class="text-sm font-semibold text-gray-700">Máu (HP)</span>
                            </div>
                            <span class="text-sm font-bold text-red-600">
                                {{ user.hp || 100 }} / {{ user.max_hp || 100 }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div 
                                class="h-full bg-gradient-to-r from-red-500 to-pink-500 rounded-full transition-all duration-500"
                                :style="{ width: `${hpPercentage}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- Attack -->
                    <div class="flex items-center justify-between p-2.5 bg-gradient-to-r from-orange-50 to-red-50 rounded-lg border border-orange-200">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">⚔️</span>
                            <span class="text-sm font-semibold text-gray-700">Tấn công</span>
                        </div>
                        <span class="text-base font-bold text-orange-600">{{ user.attack || 10 }}</span>
                    </div>

                    <!-- Defense -->
                    <div class="flex items-center justify-between p-2.5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🛡️</span>
                            <span class="text-sm font-semibold text-gray-700">Phòng thủ</span>
                        </div>
                        <span class="text-base font-bold text-blue-600">{{ user.defense || 5 }}</span>
                    </div>
                </div>
            </div>

            <!-- Thống kê -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">Thống kê</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-2.5 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Kinh nghiệm</p>
                        <p class="text-sm font-bold text-gray-800">{{ formatCurrency(user.exp || 0) }} XP</p>
                    </div>
                    <div class="text-center p-2.5 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Tên cấp</p>
                        <p class="text-sm font-bold text-purple-600">{{ user.level_name || 'Chưa có' }}</p>
                    </div>
                </div>
                <!-- Hấp thụ linh lực -->
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center gap-3 p-2.5 bg-purple-50 rounded-lg border border-purple-200">
                        <span class="text-2xl">✨</span>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 mb-0.5">Hấp thụ linh lực</p>
                            <p class="text-sm font-bold text-purple-600">{{ user.online_exp_per_5s || 1 }} linh lực/chu thiên</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../api'
import LoadingSpinner from '../common/LoadingSpinner.vue'

const user = ref(null)
const loading = ref(true)

const hpPercentage = computed(() => {
    if (!user.value) return 0
    const hp = user.value.hp || 100
    const maxHp = user.value.max_hp || 100
    return maxHp > 0 ? Math.min(100, (hp / maxHp) * 100) : 0
})

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('vi-VN').format(amount)
}

const fetchUser = async () => {
    loading.value = true
    try {
        const res = await api.get('/me')
        user.value = res.data
    } catch (error) {
        console.error('Error fetching user:', error)
    } finally {
        loading.value = false
    }
}

onMounted(fetchUser)
</script>

