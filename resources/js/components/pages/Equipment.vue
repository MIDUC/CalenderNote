<template>
    <div class="space-y-4">
        <h1 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
            ⚔️ Trang bị
        </h1>

        <LoadingSpinner v-if="loading" />
        <div v-else class="space-y-4">
            <!-- Equipment Slots -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">Trang bị hiện tại</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Weapon Slot -->
                    <div class="border-2 rounded-xl p-3" :class="equipment.weapon ? 'border-blue-400 bg-blue-50' : 'border-gray-300 bg-gray-50'">
                        <div class="text-center">
                            <div class="text-3xl mb-2">⚔️</div>
                            <p class="text-xs font-semibold text-gray-600 mb-2">Vũ khí</p>
                            <div v-if="equipment.weapon" class="space-y-1">
                                <div class="text-2xl">{{ equipment.weapon.item.icon || '🗡️' }}</div>
                                <p class="text-sm font-bold text-gray-800">{{ equipment.weapon.item.name }}</p>
                                <div class="text-xs text-gray-600 space-y-0.5">
                                    <div v-if="equipment.weapon.item.effects?.attack">+{{ equipment.weapon.item.effects.attack }} Tấn công</div>
                                    <div v-if="equipment.weapon.item.effects?.hp">+{{ equipment.weapon.item.effects.hp }} HP</div>
                                </div>
                                <button 
                                    @click="unequipItem('weapon')"
                                    class="mt-2 px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg font-medium transition-colors"
                                >
                                    Gỡ
                                </button>
                            </div>
                            <div v-else class="text-xs text-gray-400 py-2">Trống</div>
                        </div>
                    </div>

                    <!-- Armor Slot -->
                    <div class="border-2 rounded-xl p-3" :class="equipment.armor ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-50'">
                        <div class="text-center">
                            <div class="text-3xl mb-2">🛡️</div>
                            <p class="text-xs font-semibold text-gray-600 mb-2">Giáp</p>
                            <div v-if="equipment.armor" class="space-y-1">
                                <div class="text-2xl">{{ equipment.armor.item.icon || '🛡️' }}</div>
                                <p class="text-sm font-bold text-gray-800">{{ equipment.armor.item.name }}</p>
                                <div class="text-xs text-gray-600 space-y-0.5">
                                    <div v-if="equipment.armor.item.effects?.defense">+{{ equipment.armor.item.effects.defense }} Phòng thủ</div>
                                    <div v-if="equipment.armor.item.effects?.max_hp">+{{ equipment.armor.item.effects.max_hp }} Max HP</div>
                                </div>
                                <button 
                                    @click="unequipItem('armor')"
                                    class="mt-2 px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg font-medium transition-colors"
                                >
                                    Gỡ
                                </button>
                            </div>
                            <div v-else class="text-xs text-gray-400 py-2">Trống</div>
                        </div>
                    </div>

                    <!-- Accessory Slot -->
                    <div class="border-2 rounded-xl p-3" :class="equipment.accessory ? 'border-purple-400 bg-purple-50' : 'border-gray-300 bg-gray-50'">
                        <div class="text-center">
                            <div class="text-3xl mb-2">💍</div>
                            <p class="text-xs font-semibold text-gray-600 mb-2">Phụ kiện</p>
                            <div v-if="equipment.accessory" class="space-y-1">
                                <div class="text-2xl">{{ equipment.accessory.item.icon || '💍' }}</div>
                                <p class="text-sm font-bold text-gray-800">{{ equipment.accessory.item.name }}</p>
                                <div class="text-xs text-gray-600 space-y-0.5">
                                    <div v-if="equipment.accessory.item.effects?.attack">+{{ equipment.accessory.item.effects.attack }} Tấn công</div>
                                    <div v-if="equipment.accessory.item.effects?.defense">+{{ equipment.accessory.item.effects.defense }} Phòng thủ</div>
                                    <div v-if="equipment.accessory.item.effects?.hp">+{{ equipment.accessory.item.effects.hp }} HP</div>
                                </div>
                                <button 
                                    @click="unequipItem('accessory')"
                                    class="mt-2 px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg font-medium transition-colors"
                                >
                                    Gỡ
                                </button>
                            </div>
                            <div v-else class="text-xs text-gray-400 py-2">Trống</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Equipment Items -->
            <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
                <h2 class="text-base font-bold text-gray-800 mb-3">Vật phẩm có thể trang bị</h2>
                <LoadingSpinner v-if="loadingInventory" />
                <EmptyState 
                    v-else-if="equipmentItems.length === 0"
                    icon="📦"
                    title="Không có vật phẩm trang bị"
                    message="Hãy mua vật phẩm từ cửa hàng!"
                />
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div 
                        v-for="item in equipmentItems" 
                        :key="item.id"
                        class="border-2 rounded-xl p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
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
                                <div class="text-xs text-gray-600 space-y-0.5 mb-2">
                                    <div v-if="item.effects?.attack">+{{ item.effects.attack }} Tấn công</div>
                                    <div v-if="item.effects?.defense">+{{ item.effects.defense }} Phòng thủ</div>
                                    <div v-if="item.effects?.hp">+{{ item.effects.hp }} HP</div>
                                    <div v-if="item.effects?.max_hp">+{{ item.effects.max_hp }} Max HP</div>
                                </div>
                                <button 
                                    @click="equipItem(item)"
                                    class="w-full px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg font-medium transition-colors"
                                >
                                    Trang bị
                                </button>
                            </div>
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
const equipment = ref({})
const equipmentItems = ref([])
const loading = ref(true)
const loadingInventory = ref(true)

const loadEquipment = async () => {
    loading.value = true
    try {
        const res = await api.get('/equipment/list')
        const equipmentList = res.data?.data || []
        // Convert array to object keyed by slot
        equipment.value = {}
        equipmentList.forEach(eq => {
            equipment.value[eq.slot] = eq
        })
    } catch (error) {
        console.error('Lỗi tải trang bị:', error)
        toast.error('Không thể tải trang bị')
    } finally {
        loading.value = false
    }
}

const loadInventory = async () => {
    loadingInventory.value = true
    try {
        const res = await api.get('/shop/inventory')
        const allItems = res.data?.data || []
        // Filter only equipment items
        equipmentItems.value = allItems.filter(item => item.type === 'equipment')
    } catch (error) {
        console.error('Lỗi tải túi đồ:', error)
    } finally {
        loadingInventory.value = false
    }
}

const equipItem = async (item) => {
    try {
        const res = await api.post(`/equipment/equip/${item.id}`)
        toast.success('Trang bị thành công!')
        if (res.data?.data?.stats) {
            toast.info(`Stats: HP ${res.data.data.stats.hp}/${res.data.data.stats.max_hp}, ATK ${res.data.data.stats.attack}, DEF ${res.data.data.stats.defense}`)
        }
        await loadEquipment()
        await loadInventory()
    } catch (error) {
        console.error('Lỗi trang bị:', error)
        toast.error(error.response?.data?.message || 'Trang bị thất bại')
    }
}

const unequipItem = async (slot) => {
    try {
        const res = await api.post(`/equipment/unequip/${slot}`)
        toast.success('Gỡ trang bị thành công!')
        if (res.data?.data?.stats) {
            toast.info(`Stats: HP ${res.data.data.stats.hp}/${res.data.data.stats.max_hp}, ATK ${res.data.data.stats.attack}, DEF ${res.data.data.stats.defense}`)
        }
        await loadEquipment()
        await loadInventory()
    } catch (error) {
        console.error('Lỗi gỡ trang bị:', error)
        toast.error(error.response?.data?.message || 'Gỡ trang bị thất bại')
    }
}

onMounted(() => {
    loadEquipment()
    loadInventory()
})
</script>

