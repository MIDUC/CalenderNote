<template>
    <div class="p-2 pb-24 safe-area-pb"> 
        <div class="flex overflow-x-auto gap-2 mb-4 no-scrollbar pb-1">
            <button v-for="tab in statusTabs" :key="tab.value"
                @click="changeStatusTab(tab.value)"
                class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-semibold transition-all border"
                :class="filters.status === tab.value 
                    ? 'bg-blue-600 text-white border-blue-600 shadow-md' 
                    : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'"
            >
                {{ tab.label }}
            </button>
        </div>

        <div class="mb-4">
            <FilterBar :filters="filters" :sortBy="sortBy" :sortDirection="sortDirection" :sortFields="sortFields"
            @updateFilters="handleFilterUpdate" />
        </div>

        <div v-if="loading" class="flex flex-col items-center py-10 text-gray-400">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-2"></div>
            <span>Đang tải dữ liệu...</span>
        </div>

        <div v-else class="space-y-3">
             <div v-if="tasks.length === 0" class="text-center py-10 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                <p class="text-gray-500">Không tìm thấy công việc nào.</p>
            </div>

            <div v-for="task in tasks" :key="task.id" 
                @click="openTaskDetail(task)"
                class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col gap-3 relative overflow-hidden cursor-pointer active:scale-[0.98] transition-transform">
                
                <div class="absolute left-0 top-0 bottom-0 w-1.5" :class="getStatusColor(task.status)"></div>

                <div class="flex justify-between items-start pl-2">
                    <h3 class="font-semibold text-gray-800 text-lg leading-tight line-clamp-2">
                        {{ task.title }}
                    </h3>
                    <span class="text-xs font-bold px-2 py-1 rounded-md uppercase tracking-wide shrink-0 ml-2"
                        :class="getStatusBadgeClass(task.status)">
                        {{ getStatusLabel(task.status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-y-2 text-sm text-gray-500 pl-2">
                    <div class="flex items-center gap-1">
                        <span>📅</span>
                        <span>{{ formatDate(task.task_date) }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span>⏰</span>
                        <span>{{ task.fixed_time || 'Cả ngày' }}</span>
                    </div>
                    <div v-if="task.completed_at" class="col-span-2 text-green-600 flex items-center gap-1">
                        <span>✅</span>
                        <span>Xong: {{ formatDate(task.completed_at) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 mb-10">
             <Pagination v-if="!loading && total > 0" :currentPage="page" :totalPages="lastPage" :totalItems="total"
            :itemPerPage="itemPerPage" @page-changed="loadTasks" @item-per-page-changed="handleItemPerPageChange" />
        </div>
        
        <Teleport to="body">
            <Transition name="modal-fade">
                <div v-if="selectedTask" 
                     class="fixed inset-0 z-[100] flex items-end md:items-center justify-center sm:p-4"
                >
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
                         @click="closeTaskDetail">
                    </div>

                    <div class="bg-white w-full md:w-[500px] md:max-w-lg 
                                rounded-t-3xl md:rounded-2xl 
                                shadow-2xl z-10 overflow-hidden flex flex-col 
                                max-h-[90vh] md:max-h-[85vh] 
                                transform transition-all duration-300"
                    >
                        <div class="md:hidden w-full flex justify-center pt-3 pb-1 cursor-pointer" @click="closeTaskDetail">
                            <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
                        </div>

                        <div class="px-5 py-4 border-b flex justify-between items-start bg-gray-50/50">
                            <h2 class="text-lg md:text-xl font-bold text-gray-800 leading-snug pr-4 line-clamp-2">
                                {{ selectedTask.title }}
                            </h2>
                            <button @click="closeTaskDetail" class="text-gray-400 hover:text-gray-600 bg-gray-100 p-1.5 rounded-full">✕</button>
                        </div>

                        <div class="p-5 overflow-y-auto custom-scrollbar space-y-5">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Trạng thái</span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold border uppercase" 
                                    :class="getStatusBadgeClass(selectedTask.status)">
                                    {{ getStatusLabel(selectedTask.status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                                    <p class="text-[10px] text-blue-400 uppercase font-bold mb-1">Ngày thực hiện</p>
                                    <p class="font-semibold text-blue-900 text-sm">{{ formatDate(selectedTask.task_date) }}</p>
                                </div>
                                <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                                    <p class="text-[10px] text-indigo-400 uppercase font-bold mb-1">Giờ cụ thể</p>
                                    <p class="font-semibold text-indigo-900 text-sm">{{ selectedTask.fixed_time || 'Tự do' }}</p>
                                </div>
                            </div>

                            <div v-if="selectedTask.description">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Mô tả:</p>
                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 text-sm text-gray-600 leading-relaxed">
                                    {{ selectedTask.description }}
                                </div>
                            </div>
                        </div>

                        <div class="p-4 border-t bg-white flex gap-3 safe-area-pb">
                            <template v-if="selectedTask.status === 'pending'">
                                <button @click="updateTaskStatus(selectedTask.id, 'done')" 
                                    class="flex-1 bg-green-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-green-200 active:scale-95 transition-all">
                                    Hoàn thành
                                </button>
                                <button @click="updateTaskStatus(selectedTask.id, 'failed')" 
                                    class="flex-1 bg-red-50 text-red-600 py-3 rounded-xl font-bold border border-red-100 active:scale-95 transition-all">
                                    Thất bại
                                </button>
                            </template>
                            <button v-else @click="closeTaskDetail" 
                                class="w-full bg-gray-100 text-gray-700 py-3 rounded-xl font-bold active:scale-95 transition-all">
                                Đóng lại
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Pagination from '../layouts/Pagination.vue'
import FilterBar from '../layouts/FilterBar.vue'
import { useToast } from "vue-toastification"; // Import Toast

// --- DATA ---
const tasks = ref([])
const loading = ref(true)
const page = ref(1)
const itemPerPage = ref(10)
const total = ref(0)
const lastPage = ref(1)
const selectedTask = ref(null) // Thêm biến lưu task đang xem
const toast = useToast() // Init Toast

// --- CONFIG ---
const token = localStorage.getItem('token')
const authHeader = { headers: { Accept: 'application/json', Authorization: `Bearer ${token}` } }

// --- FILTER & SORT ---
const filters = ref({ title: '', status: '' }) 
const sortBy = ref('created_at')
const sortDirection = ref('desc')

const sortFields = [
    { value: 'created_at', label: 'Mới nhất' },
    { value: 'task_date', label: 'Ngày thực hiện' },
    { value: 'title', label: 'Tên A-Z' },
]

const statusTabs = [
    { label: 'Tất cả', value: '' },
    { label: 'Chưa làm', value: 'pending' },
    { label: 'Đã xong', value: 'done' },
    { label: 'Thất bại', value: 'failed' },
]

// --- METHODS ---
const loadTasks = async (newPage = 1) => {
    page.value = newPage
    loading.value = true
    try {
        const res = await axios.post(
            `/api/task/listing`,
            {
                filters: filters.value,
                sort_by: [sortBy.value],
                sort_direction: [sortDirection.value],
                page: page.value,
                item_per_page: itemPerPage.value,
            },
            authHeader
        )
        const data = res.data?.data
        tasks.value = data?.data || []
        total.value = data?.total || 0
        lastPage.value = data?.last_page || 1
    } catch (error) {
        console.error('Lỗi tải task:', error)
    } finally {
        loading.value = false
    }
}

// --- MODAL ACTIONS (MỚI) ---
const openTaskDetail = (task) => {
    selectedTask.value = task
}

const closeTaskDetail = () => {
    selectedTask.value = null
}

const updateTaskStatus = async (id, status) => {
    if(!confirm(`Xác nhận cập nhật trạng thái?`)) return;
    
    try {
        const endpoint = status === 'done' ? 'complete' : 'fail'
        await axios.post(`/api/task/${endpoint}/${id}`, {}, authHeader)
        
        toast.success("Cập nhật thành công!")
        closeTaskDetail()
        loadTasks(page.value) // Reload list
    } catch (e) {
        toast.error("Lỗi cập nhật")
    }
}

// --- HELPERS ---
const changeStatusTab = (statusValue) => {
    filters.value.status = statusValue
    loadTasks(1)
}

const handleFilterUpdate = (payload) => {
    filters.value = { ...filters.value, ...payload.filters } 
    sortBy.value = payload.sortBy
    sortDirection.value = payload.sortDirection
    loadTasks(1)
}

const handleItemPerPageChange = (newSize) => {
    itemPerPage.value = newSize
    loadTasks(1)
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit' })
}

const getStatusLabel = (status) => {
    const map = { done: 'Hoàn thành', failed: 'Thất bại', pending: 'Chờ xử lý', blocked: 'Tạm hoãn' }
    return map[status] || status
}

const getStatusColor = (status) => {
    const map = { done: 'bg-green-500', failed: 'bg-red-500', pending: 'bg-gray-400', blocked: 'bg-yellow-500' }
    return map[status] || 'bg-gray-200'
}

const getStatusBadgeClass = (status) => {
    const map = {
        done: 'bg-green-100 text-green-700',
        failed: 'bg-red-100 text-red-700',
        pending: 'bg-gray-100 text-gray-600',
        blocked: 'bg-yellow-100 text-yellow-700'
    }
    return map[status] || 'bg-gray-100'
}

onMounted(() => loadTasks(1))
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.safe-area-pb { padding-bottom: env(safe-area-inset-bottom, 20px); }

/* Custom Scrollbar cho modal content */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }

/* Animation Modal Responsive */
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }

@media (max-width: 768px) {
    .modal-fade-enter-active .bg-white, .modal-fade-leave-active .bg-white { transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    .modal-fade-enter-from .bg-white, .modal-fade-leave-to .bg-white { transform: translateY(100%); }
}

@media (min-width: 769px) {
    .modal-fade-enter-active .bg-white, .modal-fade-leave-active .bg-white { transition: transform 0.2s ease-out, opacity 0.2s ease; }
    .modal-fade-enter-from .bg-white, .modal-fade-leave-to .bg-white { transform: scale(0.95); opacity: 0; }
}
</style>