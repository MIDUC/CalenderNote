<template>
    <div class="space-y-4">
        <h1 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
            👤 Thông tin cá nhân
        </h1>

        <LoadingSpinner v-if="loading" />
        <div v-else class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
            <form @submit.prevent="updateInfo" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tuổi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tuổi</label>
                        <input
                            v-model.number="formData.age"
                            type="number"
                            min="1"
                            max="150"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Nhập tuổi"
                        />
                    </div>

                    <!-- Giới tính -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Giới tính</label>
                        <select
                            v-model="formData.gender"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        >
                            <option value="">Chọn giới tính</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>

                    <!-- Cân nặng -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cân nặng (kg)</label>
                        <input
                            v-model.number="formData.weight"
                            type="number"
                            step="0.1"
                            min="1"
                            max="500"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Nhập cân nặng"
                        />
                    </div>

                    <!-- Chiều cao -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Chiều cao (cm)</label>
                        <input
                            v-model.number="formData.height"
                            type="number"
                            step="0.1"
                            min="50"
                            max="300"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Nhập chiều cao"
                        />
                    </div>
                </div>

                <!-- Goals -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mục tiêu (tùy chọn)</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="formData.goals.lose_weight" class="rounded" />
                            <span class="text-sm text-gray-700">Giảm cân</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="formData.goals.gain_muscle" class="rounded" />
                            <span class="text-sm text-gray-700">Tăng cơ</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="formData.goals.improve_cardio" class="rounded" />
                            <span class="text-sm text-gray-700">Cải thiện tim mạch</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="formData.goals.improve_strength" class="rounded" />
                            <span class="text-sm text-gray-700">Tăng sức mạnh</span>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ghi chú (tùy chọn)</label>
                    <textarea
                        v-model="formData.notes"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Ghi chú về mục tiêu hoặc tiến độ..."
                    ></textarea>
                </div>

                <!-- Current Month Info -->
                <div v-if="currentMonthRecord" class="p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                    <p class="text-xs font-semibold text-gray-600 mb-1">📅 Bản ghi tháng hiện tại</p>
                    <div class="text-xs text-gray-700 space-y-1">
                        <p>Cân nặng: <span class="font-semibold">{{ currentMonthRecord.weight }} kg</span></p>
                        <p>BMI: <span class="font-semibold">{{ currentMonthRecord.bmi }}</span></p>
                        <p v-if="currentMonthRecord.notes" class="text-gray-600 italic">{{ currentMonthRecord.notes }}</p>
                    </div>
                </div>

                <!-- BMI Display -->
                <div v-if="bmi" class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">Chỉ số BMI:</span>
                        <span class="text-lg font-bold"
                            :class="{
                                'text-green-600': bmi >= 18.5 && bmi < 25,
                                'text-yellow-600': (bmi >= 25 && bmi < 30) || (bmi < 18.5),
                                'text-red-600': bmi >= 30 || bmi < 16
                            }"
                        >
                            {{ bmi }}
                        </span>
                        <span class="text-xs text-gray-600">
                            {{ getBMICategory(bmi) }}
                        </span>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200"
                >
                    Cập nhật thông tin
                </button>
            </form>
        </div>

        <!-- History Section -->
        <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
            <h2 class="text-base font-bold text-gray-800 mb-3">📊 Lịch sử cập nhật</h2>
            <LoadingSpinner v-if="loadingHistory" />
            <EmptyState 
                v-else-if="history.length === 0"
                icon="📝"
                title="Chưa có lịch sử"
                message="Hãy cập nhật thông tin để bắt đầu!"
            />
            <div v-else class="space-y-2">
                <div 
                    v-for="record in history" 
                    :key="record.id"
                    class="p-3 bg-gray-50 rounded-lg border border-gray-200"
                >
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-800">{{ formatMonthYear(record.month_year) }}</span>
                        <span v-if="record.bmi" class="text-xs px-2 py-1 rounded-full"
                            :class="{
                                'bg-green-100 text-green-700': record.bmi >= 18.5 && record.bmi < 25,
                                'bg-yellow-100 text-yellow-700': (record.bmi >= 25 && record.bmi < 30) || (record.bmi < 18.5),
                                'bg-red-100 text-red-700': record.bmi >= 30 || record.bmi < 16
                            }"
                        >
                            BMI: {{ record.bmi }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                        <p v-if="record.weight">Cân nặng: <span class="font-semibold">{{ record.weight }} kg</span></p>
                        <p v-if="record.height">Chiều cao: <span class="font-semibold">{{ record.height }} cm</span></p>
                        <p v-if="record.age">Tuổi: <span class="font-semibold">{{ record.age }}</span></p>
                        <p v-if="record.gender">Giới tính: <span class="font-semibold">{{ getGenderLabel(record.gender) }}</span></p>
                    </div>
                    <div v-if="record.goals && Object.keys(record.goals).length > 0" class="mt-2 pt-2 border-t border-gray-200">
                        <p class="text-xs text-gray-600 mb-1">Mục tiêu:</p>
                        <div class="flex flex-wrap gap-1">
                            <span v-for="(value, key) in record.goals" :key="key" v-if="value"
                                class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full"
                            >
                                {{ getGoalLabel(key) }}
                            </span>
                        </div>
                    </div>
                    <p v-if="record.notes" class="text-xs text-gray-500 italic mt-2">{{ record.notes }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../../api'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import EmptyState from '../common/EmptyState.vue'

const toast = useToast()
const loading = ref(true)
const loadingHistory = ref(false)
const personalInfo = ref(null)
const history = ref([])
const formData = ref({
    age: null,
    weight: null,
    height: null,
    gender: '',
    goals: {
        lose_weight: false,
        gain_muscle: false,
        improve_cardio: false,
        improve_strength: false,
    },
    notes: '',
})
const currentMonthRecord = ref(null)

const bmi = computed(() => {
    if (!formData.value.weight || !formData.value.height || formData.value.height <= 0) {
        return null
    }
    const heightInMeters = formData.value.height / 100
    return (formData.value.weight / (heightInMeters * heightInMeters)).toFixed(1)
})

const getBMICategory = (bmiValue) => {
    if (bmiValue < 16) return 'Gầy độ III'
    if (bmiValue < 17) return 'Gầy độ II'
    if (bmiValue < 18.5) return 'Gầy độ I'
    if (bmiValue < 25) return 'Bình thường'
    if (bmiValue < 30) return 'Thừa cân'
    if (bmiValue < 35) return 'Béo phì độ I'
    if (bmiValue < 40) return 'Béo phì độ II'
    return 'Béo phì độ III'
}

const loadPersonalInfo = async () => {
    loading.value = true
    try {
        const res = await api.get('/personal-info/show')
        personalInfo.value = res.data?.data || {}
        currentMonthRecord.value = personalInfo.value.current_month_record
        formData.value = {
            age: personalInfo.value.age,
            weight: personalInfo.value.weight,
            height: personalInfo.value.height,
            gender: personalInfo.value.gender || '',
            goals: currentMonthRecord.value?.goals || {
                lose_weight: false,
                gain_muscle: false,
                improve_cardio: false,
                improve_strength: false,
            },
            notes: currentMonthRecord.value?.notes || '',
        }
    } catch (error) {
        console.error('Lỗi tải thông tin:', error)
    } finally {
        loading.value = false
    }
}

const updateInfo = async () => {
    try {
        // Filter goals to only include true values
        const goalsToSend = Object.keys(formData.value.goals)
            .filter(key => formData.value.goals[key])
            .reduce((obj, key) => {
                obj[key] = true
                return obj
            }, {})
        
        const payload = {
            ...formData.value,
            goals: Object.keys(goalsToSend).length > 0 ? goalsToSend : null,
        }
        
        const res = await api.put('/personal-info/update', payload)
        const responseData = res.data?.data || {}
        
        toast.success(responseData.message || 'Cập nhật thông tin thành công!')
        
        // Show notification if schedules were created
        if (responseData.schedules_created > 0) {
            toast.info(`Đã tự động tạo ${responseData.schedules_created} nhiệm vụ luyện tập mới!`)
        }
        
        await loadPersonalInfo()
    } catch (error) {
        console.error('Lỗi cập nhật:', error)
        toast.error(error.response?.data?.message || 'Cập nhật thất bại')
    }
}

const loadHistory = async () => {
    loadingHistory.value = true
    try {
        const res = await api.get('/personal-info/history')
        history.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải lịch sử:', error)
    } finally {
        loadingHistory.value = false
    }
}

const formatMonthYear = (monthYear) => {
    if (!monthYear) return ''
    const [year, month] = monthYear.split('-')
    const monthNames = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 
                        'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12']
    return `${monthNames[parseInt(month) - 1]} ${year}`
}

const getGenderLabel = (gender) => {
    const labels = {
        'male': 'Nam',
        'female': 'Nữ',
        'other': 'Khác',
    }
    return labels[gender] || gender
}

const getGoalLabel = (goalKey) => {
    const labels = {
        'lose_weight': 'Giảm cân',
        'gain_muscle': 'Tăng cơ',
        'improve_cardio': 'Cải thiện tim mạch',
        'improve_strength': 'Tăng sức mạnh',
        'maintain': 'Duy trì',
        'improve_fitness': 'Cải thiện thể lực',
    }
    return labels[goalKey] || goalKey
}

onMounted(() => {
    loadPersonalInfo()
    loadHistory()
})
</script>

