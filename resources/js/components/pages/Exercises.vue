<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-lg font-bold bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text text-transparent">
                💪 Luyện tập
            </h1>
            <button
                @click="showScheduleModal = true"
                class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg font-medium transition-colors"
            >
                + Tạo lịch tập
            </button>
        </div>

        <!-- Today's Tasks -->
        <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
            <h2 class="text-base font-bold text-gray-800 mb-3">📅 Nhiệm vụ hôm nay</h2>
            <LoadingSpinner v-if="loadingTasks" />
            <EmptyState 
                v-else-if="todayTasks.length === 0"
                icon="💪"
                title="Chưa có nhiệm vụ tập luyện"
                message="Hãy tạo lịch tập để bắt đầu!"
            />
            <div v-else class="space-y-3">
                <div 
                    v-for="task in todayTasks" 
                    :key="task.schedule_id"
                    class="border-2 rounded-xl p-3"
                    :class="task.is_completed ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-50'"
                >
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ task.exercise_type.icon }}</span>
                            <div>
                                <h3 class="font-semibold text-sm text-gray-800">{{ task.exercise_type.name }}</h3>
                                <p class="text-xs text-gray-500">{{ task.exercise_type.description }}</p>
                            </div>
                        </div>
                        <span v-if="task.is_completed" class="px-2 py-1 bg-green-500 text-white text-xs rounded-full font-semibold">
                            ✓ Hoàn thành
                        </span>
                    </div>
                    <div class="mb-2">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Tiến độ: {{ task.completed_amount }} / {{ task.target_amount }} {{ task.exercise_type.unit }}</span>
                            <span>{{ Math.round(task.progress_percentage) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div 
                                class="h-2 rounded-full transition-all"
                                :class="task.is_completed ? 'bg-green-500' : 'bg-gradient-to-r from-blue-500 to-purple-500'"
                                :style="{ width: `${task.progress_percentage}%` }"
                            ></div>
                        </div>
                    </div>
                    <button
                        @click="logExercise(task.exercise_type.id, task.target_amount)"
                        class="w-full px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded-lg font-medium transition-colors"
                    >
                        Ghi nhận tập luyện
                    </button>
                </div>
            </div>
        </div>

        <!-- Exercise Schedules -->
        <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
            <h2 class="text-base font-bold text-gray-800 mb-3">📋 Lịch tập luyện</h2>
            <LoadingSpinner v-if="loadingSchedules" />
            <EmptyState 
                v-else-if="schedules.length === 0"
                icon="📅"
                title="Chưa có lịch tập"
                message="Hãy tạo lịch tập để bắt đầu!"
            />
            <div v-else class="space-y-3">
                <div 
                    v-for="schedule in schedules" 
                    :key="schedule.id"
                    class="border-2 rounded-xl p-3"
                    :class="schedule.is_accepted ? 'border-green-400 bg-green-50' : 'border-blue-300 bg-blue-50'"
                >
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ schedule.exercise_type.icon }}</span>
                            <div>
                                <h3 class="font-semibold text-sm text-gray-800">{{ schedule.exercise_type.name }}</h3>
                                <p class="text-xs text-gray-600">
                                    Mục tiêu: {{ schedule.target_amount }} {{ schedule.exercise_type.unit }} / 
                                    <span class="capitalize">{{ schedule.frequency === 'daily' ? 'ngày' : 'tuần' }}</span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Từ {{ formatDate(schedule.start_date) }} 
                                    <span v-if="schedule.end_date">đến {{ formatDate(schedule.end_date) }}</span>
                                    <span v-else>không giới hạn</span>
                                </p>
                            </div>
                        </div>
                        <span v-if="schedule.is_accepted" class="px-2 py-1 bg-green-500 text-white text-xs rounded-full font-semibold">
                            ✓ Đã chấp nhận
                        </span>
                    </div>
                    <div v-if="!schedule.is_accepted" class="mt-3">
                        <p class="text-xs text-yellow-600 mb-2">
                            ⚠️ Lưu ý: Nếu không hoàn thành task trong ngày, sẽ bị trừ 5 XP
                        </p>
                        <button
                            @click="acceptSchedule(schedule.id)"
                            :disabled="acceptingSchedule === schedule.id"
                            class="w-full px-3 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white text-xs rounded-lg font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="acceptingSchedule === schedule.id">Đang xử lý...</span>
                            <span v-else>✅ Chấp nhận và tạo task cho 1 tháng</span>
                        </button>
                    </div>
                    <div v-else class="mt-2 text-xs text-gray-600">
                        <p>✓ Đã tạo task tự động cho tháng này. Kiểm tra trong danh sách Task!</p>
                        <p class="text-yellow-600 mt-1">⚠️ Nhớ hoàn thành task mỗi ngày để tránh bị trừ XP!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exercise History -->
        <div class="bg-white/90 backdrop-blur-lg rounded-xl shadow-md border border-gray-200/50 p-4">
            <h2 class="text-base font-bold text-gray-800 mb-3">📊 Lịch sử tập luyện</h2>
            <LoadingSpinner v-if="loadingHistory" />
            <EmptyState 
                v-else-if="history.length === 0"
                icon="📝"
                title="Chưa có lịch sử"
                message="Hãy bắt đầu tập luyện!"
            />
            <div v-else class="space-y-2">
                <div 
                    v-for="exercise in history.slice(0, 10)" 
                    :key="exercise.id"
                    class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg"
                >
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ exercise.exercise_type.icon }}</span>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">{{ exercise.exercise_type.name }}</p>
                            <p class="text-xs text-gray-500">{{ formatDate(exercise.exercise_date) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-sm text-gray-800">{{ exercise.amount }} {{ exercise.exercise_type.unit }}</p>
                        <div class="flex gap-2 text-xs text-gray-600">
                            <span>+{{ exercise.xp_gained }} XP</span>
                            <span>+{{ exercise.currency_gained }} 💎</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Modal -->
        <transition name="modal">
            <div v-if="showScheduleModal" @click.self="showScheduleModal = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl p-5 w-full max-w-md shadow-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800">Tạo lịch tập luyện</h2>
                        <button @click="showScheduleModal = false" class="p-1 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form @submit.prevent="createSchedule" class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Loại bài tập</label>
                            <select v-model="scheduleForm.exercise_type_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Chọn bài tập</option>
                                <option v-for="type in exerciseTypes" :key="type.id" :value="type.id">
                                    {{ type.icon }} {{ type.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Mục tiêu</label>
                            <input v-model.number="scheduleForm.target_amount" type="number" step="0.1" min="0.1" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Nhập mục tiêu" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tần suất</label>
                            <select v-model="scheduleForm.frequency" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="daily">Hàng ngày</option>
                                <option value="weekly">Hàng tuần</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ngày bắt đầu</label>
                            <input v-model="scheduleForm.start_date" type="date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                        </div>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white rounded-lg font-semibold text-sm transition-all">
                            Tạo lịch
                        </button>
                    </form>
                </div>
            </div>
        </transition>

        <!-- Log Exercise Modal -->
        <transition name="modal">
            <div v-if="showLogModal" @click.self="showLogModal = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl p-5 w-full max-w-md shadow-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800">Ghi nhận tập luyện</h2>
                        <button @click="showLogModal = false" class="p-1 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitExercise" class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Loại bài tập</label>
                            <select v-model="logForm.exercise_type_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Chọn bài tập</option>
                                <option v-for="type in exerciseTypes" :key="type.id" :value="type.id">
                                    {{ type.icon }} {{ type.name }} ({{ type.unit }})
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Số lượng</label>
                            <input v-model.number="logForm.amount" type="number" step="0.1" min="0.1" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Nhập số lượng" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ngày tập (tùy chọn)</label>
                            <input v-model="logForm.exercise_date" type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ghi chú (tùy chọn)</label>
                            <textarea v-model="logForm.notes" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Ghi chú về buổi tập"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full px-4 py-2 bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600 text-white rounded-lg font-semibold text-sm transition-all">
                            Ghi nhận
                        </button>
                    </form>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { useFormat } from '../../composables'
import api from '../../api'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import EmptyState from '../common/EmptyState.vue'

const toast = useToast()
const { formatDate } = useFormat()
const todayTasks = ref([])
const history = ref([])
const schedules = ref([])
const exerciseTypes = ref([])
const loadingTasks = ref(true)
const loadingHistory = ref(true)
const loadingSchedules = ref(true)
const acceptingSchedule = ref(null)
const showScheduleModal = ref(false)
const showLogModal = ref(false)

const scheduleForm = ref({
    exercise_type_id: '',
    target_amount: 10,
    frequency: 'daily',
    start_date: new Date().toISOString().split('T')[0],
})

const logForm = ref({
    exercise_type_id: '',
    amount: 0,
    exercise_date: '',
    notes: '',
})

const loadTodayTasks = async () => {
    loadingTasks.value = true
    try {
        const res = await api.get('/exercise/today-tasks')
        todayTasks.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải nhiệm vụ:', error)
    } finally {
        loadingTasks.value = false
    }
}

const loadHistory = async () => {
    loadingHistory.value = true
    try {
        const res = await api.get('/exercise/history')
        history.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải lịch sử:', error)
    } finally {
        loadingHistory.value = false
    }
}

const loadExerciseTypes = async () => {
    try {
        const res = await api.get('/exercise/types')
        exerciseTypes.value = res.data?.data || []
    } catch (error) {
        console.error('Lỗi tải loại bài tập:', error)
    }
}

const loadSchedules = async () => {
    loadingSchedules.value = true
    try {
        const res = await api.get('/exercise/schedules')
        // Check if tasks exist for each schedule to determine if accepted
        const schedulesData = res.data?.data || []
        for (const schedule of schedulesData) {
            // Check if tasks exist for this schedule (we'll mark as accepted if tasks exist)
            // For now, we'll use a simple check - if schedule is active and has start_date in current month
            const today = new Date()
            const startDate = new Date(schedule.start_date)
            schedule.is_accepted = false // Will be updated when we check tasks
        }
        schedules.value = schedulesData
    } catch (error) {
        console.error('Lỗi tải lịch tập:', error)
    } finally {
        loadingSchedules.value = false
    }
}

const acceptSchedule = async (scheduleId) => {
    acceptingSchedule.value = scheduleId
    try {
        const res = await api.post(`/exercise/schedule/${scheduleId}/accept`)
        toast.success(res.data?.data?.message || 'Đã chấp nhận lịch tập và tạo task thành công!')
        if (res.data?.data?.tasks_created) {
            toast.info(`Đã tạo ${res.data.data.tasks_created} task cho tháng này!`)
        }
        // Update schedule status
        const schedule = schedules.value.find(s => s.id === scheduleId)
        if (schedule) {
            schedule.is_accepted = true
        }
        await loadTodayTasks() // Reload today's tasks
    } catch (error) {
        console.error('Lỗi chấp nhận lịch tập:', error)
        toast.error(error.response?.data?.message || 'Chấp nhận lịch tập thất bại')
    } finally {
        acceptingSchedule.value = null
    }
}

const createSchedule = async () => {
    try {
        await api.post('/exercise/schedule', scheduleForm.value)
        toast.success('Tạo lịch tập thành công!')
        showScheduleModal.value = false
        scheduleForm.value = {
            exercise_type_id: '',
            target_amount: 10,
            frequency: 'daily',
            start_date: new Date().toISOString().split('T')[0],
        }
        await loadSchedules() // Reload schedules
        await loadTodayTasks()
    } catch (error) {
        console.error('Lỗi tạo lịch:', error)
        toast.error(error.response?.data?.message || 'Tạo lịch thất bại')
    }
}

const logExercise = (exerciseTypeId, suggestedAmount) => {
    logForm.value = {
        exercise_type_id: exerciseTypeId,
        amount: suggestedAmount || 0,
        exercise_date: '',
        notes: '',
    }
    showLogModal.value = true
}

const submitExercise = async () => {
    try {
        const res = await api.post('/exercise/log', logForm.value)
        toast.success('Ghi nhận tập luyện thành công!')
        if (res.data?.data?.rewards) {
            toast.info(`+${res.data.data.rewards.xp} XP, +${res.data.data.rewards.currency} 💎`)
        }
        showLogModal.value = false
        logForm.value = {
            exercise_type_id: '',
            amount: 0,
            exercise_date: '',
            notes: '',
        }
        await loadTodayTasks()
        await loadHistory()
    } catch (error) {
        console.error('Lỗi ghi nhận:', error)
        toast.error(error.response?.data?.message || 'Ghi nhận thất bại')
    }
}

onMounted(() => {
    loadTodayTasks()
    loadHistory()
    loadSchedules()
    loadExerciseTypes()
})
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s;
}
.modal-enter-from, .modal-leave-to {
    opacity: 0;
}
</style>

