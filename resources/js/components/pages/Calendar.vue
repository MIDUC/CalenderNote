<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">📅 Lịch luyện tập</h1>

        <!-- 🔍 Thanh tìm kiếm & sắp xếp -->
        <FilterBar :filters="filters" :sortBy="sortBy" :sortDirection="sortDirection" :sortFields="sortFields"
            @updateFilters="handleFilterUpdate" />

        <!-- loading -->
        <div v-if="loading" class="text-gray-500">Đang tải dữ liệu...</div>

        <!-- danh sách lịch -->
        <div v-else class="bg-white rounded-xl shadow-md divide-y">
            <div v-for="schedule in schedules" :key="schedule.id" class="p-4 hover:bg-gray-50 transition">
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-lg text-indigo-700">{{ schedule.title }}</h2>
                    <span class="text-sm text-gray-500">
                        {{ formatDate(schedule.start_date) }} - {{ formatDate(schedule.end_date) }}
                    </span>
                </div>

                <p class="text-gray-600 mt-1">{{ schedule.description }}</p>

                <div class="flex items-center mt-2 text-sm text-gray-500 space-x-2">
                    <span>🕒 {{ schedule.fixed_time }}</span>
                    <span v-if="schedule.repeat_type !== 'none'">
                        🔁 Lặp lại: {{ repeatLabel(schedule.repeat_type, schedule.days_of_week) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 📄 Phân trang -->
        <Pagination v-if="!loading && total > 0" :currentPage="page" :totalPages="lastPage" :totalItems="total"
            :itemPerPage="itemPerPage" @page-changed="loadSchedules" @item-per-page-changed="handleItemPerPageChange" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Pagination from '../layouts/Pagination.vue'
import FilterBar from '../layouts/FilterBar.vue'

const schedules = ref([])
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

const loadSchedules = async (newPage = 1) => {
    page.value = newPage
    loading.value = true
    try {
        const res = await axios.post(
            `${import.meta.env.VITE_API_BASE_URL}/api/schedule/listing`,
            {
                filters: filters.value,
                sort_by: [sortBy.value],
                sort_direction: [sortDirection.value],
                page: page.value,
                item_per_page: itemPerPage.value,
            },
            {
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
            }
        )

        const data = res.data?.data
        schedules.value = data?.data || []
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
    loadSchedules(1)
}

// 🔧 Nhận thay đổi số lượng item / trang
const handleItemPerPageChange = (newSize) => {
    itemPerPage.value = newSize
    loadSchedules(1)
}

onMounted(loadSchedules)
</script>
