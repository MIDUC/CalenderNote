<template>

    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ...">
            <div class="mb-4">
                <button class="bg-cyan-500 shadow-lg shadow-cyan-500/50 rounded-full">
                    <div class="p-2 text-white font-medium">
                        Thêm việc cần làm
                    </div>
                </button>
            </div>
            <h1 class=" text-xl font-bold mb-3">Công việc hôm nay</h1>
            <ul class="space-y-2">
                <li v-for="task in tasks" :key="task.id" class="bg-white p-3 rounded shadow">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2 ...">
                            <p class="font-medium">{{ task.title }}</p>
                            <p class="text-sm text-gray-500">{{ task.time }}</p>
                        </div>
                        <div class="...">
                            <div class="grid grid-cols-3 gap-1">
                                <button @click="openModal('view')"
                                    class="bg-cyan-500 shadow-lg shadow-cyan-500/50 rounded-full">
                                    <div class="p-2 text-white font-medium">
                                        Chi tiết
                                    </div>
                                </button>
                                <button @click="openModal('done', task.id)"
                                    class="bg-green-500 shadow-lg shadow-cyan-500/50 rounded-full">
                                    <div class="p-2 text-white font-medium">
                                        Hoàn thành
                                    </div>
                                </button>
                                <button @click="openModal('failed', task.id)"
                                    class="bg-red-500 shadow-lg shadow-cyan-500/50 rounded-full">
                                    <div class="p-2 text-white font-medium">
                                        Thất bại
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="...">
            <div>
                <button class="bg-indigo-500 shadow-lg shadow-cyan-500/50 rounded-full">
                    <div class="p-2 text-white font-medium">
                        Thêm ghi chú
                    </div>
                </button>
            </div>
        </div>
    </div>



</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Pagination from '../layouts/Pagination.vue'
import FilterBar from '../layouts/FilterBar.vue'

const tasks = ref([])
const notes = ref([])
const loading = ref(true)
const getTodayDate = () => {
    const today = new Date();
    // Lấy năm, tháng và ngày
    const yyyy = today.getFullYear();
    // Tháng bắt đầu từ 0 (Tháng 1 = 0), nên cần cộng 1. Dùng padStart(2, '0') để đảm bảo có 2 chữ số (ví dụ: 05)
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');

    return `${yyyy}-${mm}-${dd}`; // Trả về định dạng YYYY-MM-DD
};
// 🔹 Bộ lọc & sắp xếp
const filters = ref({ status: 'pending', task_date: getTodayDate() })
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
        const res = await axios.post(
            `${import.meta.env.VITE_API_BASE_URL}/api/task/listing`,
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
        tasks.value = data?.data || []
        total.value = data?.total || 0
        lastPage.value = data?.last_page || 1
    } catch (error) {
        console.error('Lỗi tải lịch:', error)
    } finally {
        loading.value = false
    }
}

const loadNotes = async (newPage = 1) => {
    page.value = newPage
    loading.value = true
    try {
        const res = await axios.post(
            `${import.meta.env.VITE_API_BASE_URL}/api/note/listing`,
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

onMounted(loadTasks)
</script>