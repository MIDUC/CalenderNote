<template>
    <div class="p-2 space-y-3">

        <!-- 🔍 Thanh tìm kiếm & sắp xếp -->
        <FilterBar :filters="filters" :sortBy="sortBy" :sortDirection="sortDirection" :sortFields="sortFields"
            @updateFilters="handleFilterUpdate" />

        <!-- loading -->
        <div v-if="loading" class="text-gray-500 text-sm">Đang tải dữ liệu...</div>

        <!-- danh sách task -->
        <div v-else class="bg-white rounded-lg shadow-sm divide-y">
            <div v-for="task in tasks" :key="task.id" class="p-2.5 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-indigo-700 font-medium text-sm truncate">{{ task.title }}</p>
                        </div>
                        <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500">
                            <span v-if="task.task_date">Ngày: {{ formatDate(task.task_date) }}</span>
                            <span v-if="task.fixed_time">Giờ: {{ task.fixed_time }}</span>
                            <span v-if="task.completed_at">Hoàn thành: {{ formatDate(task.completed_at) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <StatusBadge :status="task.status" type="task" />
                        <!-- Nút hoàn thành/thất bại cho task pending -->
                        <div v-if="task.status === 'pending'" class="flex gap-1.5">
                            <button
                                @click="updateTaskStatus(task.id, 'done')"
                                class="px-2.5 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded-lg font-medium transition-colors"
                                title="Hoàn thành"
                            >
                                ✓
                            </button>
                            <button
                                @click="updateTaskStatus(task.id, 'failed')"
                                class="px-2.5 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg font-medium transition-colors"
                                title="Thất bại"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 📄 Phân trang -->
        <Pagination v-if="!loading && total > 0" :currentPage="page" :totalPages="lastPage" :totalItems="total"
            :itemPerPage="itemPerPage" @page-changed="loadTasks" @item-per-page-changed="handleItemPerPageChange" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import api from '../../api'
import { taskService } from '../../services'
import Pagination from '../layouts/Pagination.vue'
import FilterBar from '../layouts/FilterBar.vue'
import StatusBadge from '../common/StatusBadge.vue'

const toast = useToast()
const tasks = ref([])
const loading = ref(true)

// 🔹 Bộ lọc & sắp xếp
const filters = ref({ title: '' })
const sortBy = ref('created_at')
const sortDirection = ref('desc')

// 🔹 Các trường được phép sort (truyền xuống FilterBar)
const sortFields = [
    { value: 'created_at', label: 'Ngày tạo' },
    { value: 'title', label: 'Tiêu đề' },
    { value: 'start_date', label: 'Ngày bắt đầu' },
]

// 🔹 Phân trang
const page = ref(1)
const itemPerPage = ref(10)
const total = ref(0)
const lastPage = ref(1)

// 🔑 Token
const token = localStorage.getItem('token')

const loadTasks = async (newPage = 1) => {
    page.value = newPage
    loading.value = true
    try {
        const res = await api.post(
            `/task/listing`,
            {
                filters: filters.value,
                sort_by: [sortBy.value],
                sort_direction: [sortDirection.value],
                page: page.value,
                item_per_page: itemPerPage.value,
            }
        )

        const data = res.data?.data
        tasks.value = data?.data || []
        total.value = data?.total || 0
        lastPage.value = data?.last_page || 1
    } catch (error) {
        console.error('Lỗi tải lịch:', error)
    } finally {
        loading.value = false
    }
}

// 🗓️ Format ngày
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    })
}

// 🔁 Nhãn lặp lại
const repeatLabel = (type, days) => {
    if (type === 'weekly' && days) return 'Thứ ' + days.split(',').join(', ')
    if (type === 'monthly') return 'Hàng tháng'
    if (type === 'daily') return 'Hàng ngày'
    return 'Một lần'
}

// 🧩 Nhận sự kiện thay đổi filter/sort
const handleFilterUpdate = (payload) => {
    filters.value = payload.filters
    sortBy.value = payload.sortBy
    sortDirection.value = payload.sortDirection
    loadTasks(1)
}

// 🔧 Nhận thay đổi số lượng item / trang
const handleItemPerPageChange = (newSize) => {
    itemPerPage.value = newSize
    loadTasks(1)
}

// ✅ Cập nhật trạng thái task
const updateTaskStatus = async (taskId, status) => {
    try {
        const res = await taskService.update(taskId, { status })
        if (status === 'done') {
            toast.success('Đã hoàn thành task!')
            
            // Show streak info if available
            if (res?.data?.streak) {
                const streak = res.data.streak
                if (streak.current_streak > 1) {
                    toast.info(`🔥 Chuỗi ngày: ${streak.current_streak} ngày!`)
                }
                if (streak.milestone_reward) {
                    toast.success(`🎉 Đạt milestone ${streak.current_streak} ngày! +${streak.milestone_reward.xp} XP, +${streak.milestone_reward.currency} 💎`)
                }
                if (streak.streak_bonus_xp > 0 || streak.streak_bonus_currency > 0) {
                    toast.info(`✨ Bonus chuỗi: +${streak.streak_bonus_xp} XP, +${streak.streak_bonus_currency} 💎`)
                }
            }
            
            // Show reward info
            if (res?.data?.reward) {
                const reward = res.data.reward
                if (reward.level_result?.leveled_up) {
                    toast.success(`🎉 Lên cấp ${reward.level_result.new_level}!`)
                }
            }
        } else {
            toast.success('Đã đánh dấu task thất bại!')
        }
        await loadTasks(page.value) // Reload current page
    } catch (error) {
        console.error('Lỗi cập nhật task:', error)
        toast.error('Không thể cập nhật task. Vui lòng thử lại!')
    }
}

onMounted(loadTasks)
</script>
