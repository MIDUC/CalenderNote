<template>
    <div class="p-4 max-w-3xl mx-auto pb-24 safe-area-pb">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Xin chào! 👋</h1>
                <p class="text-gray-500 text-sm">{{ getCurrentDateFormatted() }}</p>
            </div>
        </div>

        <div class="bg-gray-100 p-1.5 rounded-xl flex items-center gap-1 mb-6">
            <button @click="activeTab = 'tasks'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition-all duration-200"
                :class="activeTab === 'tasks' ? 'bg-white text-cyan-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                📋 Công việc
            </button>
            <button @click="activeTab = 'notes'"
                class="flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition-all duration-200"
                :class="activeTab === 'notes' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                📝 Ghi chú
            </button>
        </div>

        <div class="transition-opacity duration-300">

            <div v-if="activeTab === 'tasks'" class="space-y-4">
                <button @click="openModal('add_task')"
                    class="w-full bg-cyan-600 active:bg-cyan-700 text-white py-3 rounded-xl shadow-lg shadow-cyan-500/30 font-semibold flex items-center justify-center gap-2 mb-4">
                    <span>+ Thêm việc mới</span>
                </button>

                <div v-if="loading" class="text-center py-8 text-gray-400">Đang tải công việc...</div>
                <div v-else-if="tasks.length === 0"
                    class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-gray-500">Hôm nay rảnh rỗi quá nhỉ! 😎</p>
                </div>

                <ul v-else class="space-y-3">
                    <li v-for="task in tasks" :key="task.id"
                        class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 relative group">
                        <div class="flex justify-between items-start">
                            <div class="pr-10">
                                <h3 class="font-semibold text-gray-800">{{ task.title }}</h3>
                                <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    ⏰ {{ task.time || 'Cả ngày' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="handleConfirmComplete(task.id)"
                                    class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center border border-green-100"
                                    title="Hoàn thành">
                                    ✓
                                </button>
                                <button
                                    class="w-8 h-8 rounded-full bg-gray-50 text-gray-600 flex items-center justify-center border border-gray-200">
                                    ⋯
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div v-if="activeTab === 'notes'" class="space-y-4">
                <button
                    class="w-full bg-indigo-500 active:bg-indigo-600 text-white py-3 rounded-xl shadow-lg shadow-indigo-500/30 font-semibold flex items-center justify-center gap-2 mb-4">
                    <span>+ Viết ghi chú</span>
                </button>
                <div v-if="notes.length === 0" class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-gray-500">Chưa có ghi chú nào.</p>
                </div>
                <div v-else class="grid grid-cols-2 gap-3">
                    <div v-for="note in notes" :key="note.id" 
                        class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 shadow-sm flex flex-col justify-between h-32 relative overflow-hidden"> <!-- 1. Thêm relative overflow-hidden -->
                        
                        <!-- 2. TRẠNG THÁI (RIBBON GÓC) -->
                        <div v-if="note.status" class="absolute top-0 right-0 pointer-events-none">
                            <!-- 
                                - rotate-45: Xoay chéo 45 độ
                                - translate-x/y: Căn chỉnh vị trí để nó nằm đúng góc
                                - w-24: Đặt chiều rộng cố định để ruy băng đủ dài
                            -->
                            <div class="bg-green-500 text-white text-[9px] font-bold py-1 px-8 text-center transform translate-x-8 translate-y-3 rotate-45 shadow-sm uppercase tracking-wide w-32">
                                {{ note.status }}
                            </div>
                        </div>

                        <!-- Nội dung -->
                        <div>
                            <!-- 3. Thêm pr-8 để tiêu đề không bị ruy băng che -->
                            <p class="text-gray-800 font-bold line-clamp-2 text-sm pr-8 mb-1 leading-tight">
                                {{ note.title }}
                            </p>
                            <p class="text-gray-600 text-xs line-clamp-3 leading-relaxed">
                                {{ note.content }}
                            </p>
                        </div>

                        <p class="text-[10px] text-gray-400 font-medium pt-2 mt-auto">
                            {{ formatDate(note.created_at) }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <Teleport to="body">
            <Transition name="modal-scale">
                <div v-if="showAddModal" 
                    class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                    
                    <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" 
                        @click="showAddModal = false">
                    </div>

                    <div class="bg-white w-full md:w-[500px] md:max-w-lg 
                                rounded-2xl shadow-2xl ring-1 ring-white/20
                                z-10 
                                
                                /* 🔥 BẮT BUỘC: Thiết lập chiều cao giới hạn và Flex dọc */
                                flex flex-col 
                                max-h-[85vh] h-[85vh] /* Set cứng chiều cao luôn để ép browser tính toán */
                                
                                overflow-hidden transform transition-all duration-300">
                        
                        <div class="p-5 pb-0 shrink-0">
                            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-4"></div>
                            <h2 class="text-2xl font-black text-gray-800 mb-2 flex items-center gap-2 tracking-tight">
                                <span class="text-3xl">✨</span> Thêm công việc
                            </h2>
                        </div>

                        <div class="flex-1 min-h-0 w-full relative">
                            <TaskForm 
                                @submit="handleCreateTask" 
                                @cancel="showAddModal = false"
                                :loading="isSubmitting" 
                            />
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
import { useToast } from "vue-toastification";
import "vue-toastification/dist/index.css";
// 👇 IMPORT QUAN TRỌNG
import TaskForm from '../tasks/TaskForm.vue';

const toast = useToast();

// --- STATE ---
const activeTab = ref('tasks')
const tasks = ref([])
const notes = ref([])
const loading = ref(false)
const showAddModal = ref(false); // Trạng thái mở modal
const selectedTask = ref(null);
const isSubmitting = ref(false); // Trạng thái loading khi lưu

// Config
const token = localStorage.getItem('token')
const authHeader = { headers: { Accept: 'application/json', Authorization: `Bearer ${token}` } }

// Helpers
const getTodayDate = () => new Date().toISOString().split('T')[0];
const getCurrentDateFormatted = () => new Date().toLocaleDateString('vi-VN', { weekday: 'long', day: '2-digit', month: '2-digit' });
const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });

const filters = ref({ status: 'pending', task_date: getTodayDate() })

// --- API ACTIONS ---
const loadData = async () => {
    loading.value = true
    try {
        const [resTask, resNote] = await Promise.all([
            axios.post(`/api/task/listing`, {
                filters: filters.value,
                page: 1, item_per_page: 20,
                sort_by: ['created_at'], sort_direction: ['desc']
            }, authHeader),
            axios.post(`/api/note/listing`, {
                page: 1, item_per_page: 20,
                sort_by: ['created_at'], sort_direction: ['desc']
            }, authHeader)
        ])
        tasks.value = resTask.data?.data?.data || []
        notes.value = resNote.data?.data?.data || []
    } catch (error) {
        console.error('Lỗi tải dữ liệu:', error)
    } finally {
        loading.value = false
    }
}

// 🔥 HÀM TẠO TASK MỚI 🔥
const handleCreateTask = async (formData) => {
    isSubmitting.value = true;
    try {
        // Gọi API tạo task
        await axios.post(`/api/task/store`, formData, authHeader);
        
        toast.success("Thêm thành công! 🎉");
        showAddModal.value = false; // Đóng modal
        
        // Load lại danh sách Task (chỉ cần load task, ko cần load note)
        const res = await axios.post(`/api/task/listing`, {
                filters: filters.value,
                page: 1, item_per_page: 20,
                sort_by: ['created_at'], sort_direction: ['desc']
            }, authHeader);
        tasks.value = res.data?.data?.data || [];

    } catch (error) {
        console.error(error);
        toast.error(error.response?.data?.message || "Lỗi khi thêm mới");
    } finally {
        isSubmitting.value = false;
    }
};

const handleConfirmComplete = async (taskId) => {
    if (!confirm("Xác nhận hoàn thành?")) return;
    try {
        await axios.post(`/api/task/complete/${taskId}`, {}, authHeader)
        toast.success('Đã xong! 👍')
        loadData(); // Reload lại
    } catch (e) {
        toast.error('Lỗi cập nhật')
    }
}

const openModal = (type, data = null) => {
    if (type === 'add_task') {
        selectedTask.value = null;
        showAddModal.value = true; // Bật modal
    }
}

onMounted(() => {
    loadData()
})
</script>

<style scoped>
.safe-area-pb {
    padding-bottom: env(safe-area-inset-bottom, 20px);
}
button:active {
    transform: scale(0.98);
}

/* 🔥 CSS ANIMATION CHO MODAL 🔥 */
.modal-scale-enter-active,
.modal-scale-leave-active {
  transition: all 0.2s ease-out;
}

.modal-scale-enter-from,
.modal-scale-leave-to {
  opacity: 0;
  transform: scale(0.95); /* Thu nhỏ nhẹ khi ẩn */
}

.modal-scale-enter-to,
.modal-scale-leave-from {
  opacity: 1;
  transform: scale(1);
}
/* Mobile: Trượt từ dưới lên */
@media (max-width: 768px) {
    .modal-slide-enter-from .bg-white, 
    .modal-slide-leave-to .bg-white {
        transform: translateY(100%);
    }
}
/* Desktop: Fade in nhẹ */
@media (min-width: 769px) {
    .modal-slide-enter-from .bg-white, 
    .modal-slide-leave-to .bg-white {
        opacity: 0;
        transform: scale(0.95);
    }
}
</style>