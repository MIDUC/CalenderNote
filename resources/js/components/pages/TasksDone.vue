<template>
    <div class="space-y-4">
        <!-- 🔍 Thanh tìm kiếm & sắp xếp -->
        <FilterBar 
            :filters="filters" 
            :sortBy="sortBy" 
            :sortDirection="sortDirection" 
            :sortFields="sortFields"
            @updateFilters="handleFilterUpdate" 
        />

        <!-- Loading -->
        <LoadingSpinner v-if="loading" />

        <!-- Empty State -->
        <EmptyState 
            v-else-if="tasks.length === 0"
            icon="✅"
            title="Chưa có task hoàn thành"
            message="Các task đã hoàn thành sẽ hiển thị ở đây."
        />

        <!-- Danh sách tasks -->
        <div v-else class="space-y-3">
            <div 
                v-for="task in tasks" 
                :key="task.id" 
                class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100/50 p-5"
            >
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">{{ task.title }}</h3>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Ngày: {{ formatDate(task.task_date) }}</span>
                            </div>
                            <div v-if="task.fixed_time" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Giờ: {{ task.fixed_time }}</span>
                            </div>
                            <div v-if="task.completed_at" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Hoàn thành: {{ formatDateTime(task.completed_at) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <StatusBadge :status="task.status" type="task" />
                    </div>
                </div>
            </div>
        </div>

        <!-- 📄 Phân trang -->
        <Pagination 
            v-if="!loading && total > 0" 
            :currentPage="page" 
            :totalPages="lastPage" 
            :totalItems="total"
            :itemPerPage="itemPerPage" 
            @page-changed="loadTasks" 
            @item-per-page-changed="handleItemPerPageChange" 
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { useFormat } from '../../composables'
import { taskService } from '../../services'
import Pagination from '../layouts/Pagination.vue'
import FilterBar from '../layouts/FilterBar.vue'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import EmptyState from '../common/EmptyState.vue'
import StatusBadge from '../common/StatusBadge.vue'

const toast = useToast()
const { formatDate } = useFormat()

const tasks = ref([])
const loading = ref(true)

// 🔹 Bộ lọc & sắp xếp - Đảm bảo filter status: 'done' luôn được áp dụng
const filters = ref({ status: 'done' })
const sortBy = ref('completed_at')
const sortDirection = ref('desc')

// 🔹 Các trường được phép sort
const sortFields = [
    { value: 'completed_at', label: 'Ngày hoàn thành' },
    { value: 'created_at', label: 'Ngày tạo' },
    { value: 'title', label: 'Tiêu đề' },
    { value: 'task_date', label: 'Ngày thực hiện' },
]

// 🔹 Phân trang
const page = ref(1)
const itemPerPage = ref(10)
const total = ref(0)
const lastPage = ref(1)

const loadTasks = async (newPage = 1) => {
    page.value = newPage
    loading.value = true
    try {
        // Đảm bảo filter status: 'done' luôn được gửi
        const requestFilters = { ...filters.value, status: 'done' }
        
        console.log('Loading tasks with filters:', requestFilters)
        
        const res = await taskService.list({
            filters: requestFilters,
            sort_by: [sortBy.value],
            sort_direction: [sortDirection.value],
            page: page.value,
            item_per_page: itemPerPage.value,
        })

        console.log('Task list response:', res)
        
        const data = res?.data
        tasks.value = data?.data || []
        total.value = data?.total || 0
        lastPage.value = data?.last_page || 1
        
        console.log('Tasks loaded:', tasks.value.length, 'Total:', total.value)
    } catch (error) {
        console.error('Lỗi tải tasks:', error)
        console.error('Error response:', error.response?.data)
        toast.error('Không thể tải danh sách task hoàn thành')
    } finally {
        loading.value = false
    }
}

// Format ngày giờ
const formatDateTime = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

// 🧩 Nhận sự kiện thay đổi filter/sort
const handleFilterUpdate = (payload) => {
    // Đảm bảo status: 'done' luôn được giữ lại
    filters.value = { ...payload.filters, status: 'done' }
    sortBy.value = payload.sortBy
    sortDirection.value = payload.sortDirection
    loadTasks(1)
}

// 🔧 Nhận thay đổi số lượng item / trang
const handleItemPerPageChange = (newSize) => {
    itemPerPage.value = newSize
    loadTasks(1)
}

import { useRoute } from 'vue-router'

const route = useRoute()

onMounted(() => {
    loadTasks()
})

// Auto-refresh when navigating to this page
import { watch } from 'vue'
watch(() => route.path, (newPath) => {
    if (newPath === '/tasks/done') {
        loadTasks()
    }
})
</script>
