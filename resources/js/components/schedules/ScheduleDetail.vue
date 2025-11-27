<template>
  <div class="flex flex-col h-full max-h-[80vh]">
    <div class="flex justify-between items-start mb-4 border-b pb-2">
      <div>
        <h2 class="text-xl font-bold text-gray-800">{{ schedule.title }}</h2>
        <span class="text-xs text-gray-400">ID: {{ schedule.id }}</span>
      </div>
      <span :class="schedule.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
            class="px-3 py-1 rounded-full text-xs font-bold uppercase">
          {{ schedule.is_active ? 'Đang chạy' : 'Đã dừng' }}
      </span>
    </div>

    <div class="flex border-b mb-4">
        <button 
            @click="activeTab = 'info'"
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
            :class="activeTab === 'info' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
        >
            ℹ️ Thông tin
        </button>
        <button 
            @click="activeTab = 'tasks'"
            class="px-4 py-2 text-sm font-medium transition-colors border-b-2"
            :class="activeTab === 'tasks' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
        >
            📋 Danh sách công việc
        </button>
    </div>

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
        
        <div v-if="activeTab === 'info'" class="space-y-4 text-sm text-gray-700">
            <div class="bg-gray-50 p-4 rounded-lg border">
                <p class="mb-2"><b>Mô tả:</b> <span class="text-gray-600">{{ schedule.description || 'Không có mô tả' }}</span></p>
                <div class="grid grid-cols-2 gap-4">
                    <p><b>Bắt đầu:</b> {{ formatDate(schedule.start_date) }}</p>
                    <p><b>Kết thúc:</b> {{ schedule.end_date ? formatDate(schedule.end_date) : 'Vô thời hạn' }}</p>
                    <p><b>Loại lịch:</b> {{ formatType(schedule.type) }}</p>
                    <p><b>Kiểu lặp:</b> {{ formatRepeat(schedule.repeat_type) }}</p>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <h3 class="font-bold text-blue-800 mb-2">Cấu hình thời gian</h3>
                <div class="grid grid-cols-2 gap-2">
                    <p><b>Giờ cố định:</b> {{ schedule.has_fixed_time ? schedule.fixed_time : 'Không' }}</p>
                    <p v-if="schedule.has_fixed_time"><b>Báo trước:</b> {{ schedule.notify_before_minutes }} phút</p>
                    <p><b>Chia sẻ:</b> {{ schedule.shareable ? 'Có' : 'Không' }}</p>
                    <p><b>Yêu cầu Check-in:</b> {{ schedule.require_checkin ? 'Có' : 'Không' }}</p>
                </div>
            </div>

            <div v-if="schedule.repeat_type === 'weekly'" class="bg-yellow-50 p-3 rounded border border-yellow-100">
                <b>Lặp lại các thứ:</b> {{ parseArray(schedule.days_of_week).join(', ') }}
            </div>
            <div v-else-if="schedule.repeat_type === 'monthly'" class="bg-yellow-50 p-3 rounded border border-yellow-100">
                <b>Lặp lại các ngày:</b> {{ parseArray(schedule.days_of_month).join(', ') }}
            </div>

            <div class="text-xs text-gray-400 border-t pt-2 mt-4">
                <p>Tạo: {{ formatDate(schedule.created_at, true) }}</p>
                <p>Cập nhật: {{ formatDate(schedule.updated_at, true) }}</p>
            </div>
        </div>

        <div v-if="activeTab === 'tasks'" class="space-y-3">
            <div v-if="loadingTasks" class="text-center py-6 text-gray-500">
                <div class="animate-spin h-6 w-6 border-2 border-blue-500 rounded-full border-t-transparent mx-auto mb-2"></div>
                Đang tải công việc...
            </div>

            <div v-else-if="tasks.length === 0" class="text-center py-10 bg-gray-50 rounded border border-dashed">
                <p class="text-gray-500">Chưa có công việc nào được tạo từ lịch này.</p>
            </div>

            <div v-else class="space-y-2">
                <div v-for="task in tasks" :key="task.id" 
                     class="flex justify-between items-center p-3 bg-white border rounded shadow-sm hover:bg-gray-50">
                    <div>
                        <p class="font-medium text-gray-800">{{ task.title }}</p>
                        <p class="text-xs text-gray-500">
                            📅 {{ formatDate(task.task_date) }} 
                            <span v-if="task.fixed_time">• ⏰ {{ task.fixed_time }}</span>
                        </p>
                    </div>
                    <div>
                        <span :class="getStatusBadge(task.status)" class="px-2 py-1 rounded text-xs font-bold uppercase">
                            {{ formatStatus(task.status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-4 pt-4 border-t flex justify-end">
        <button @click="$emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg transition">
            Đóng
        </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    schedule: { type: Object, required: true }
});

const emit = defineEmits(['close']);

const activeTab = ref('info');
const tasks = ref([]);
const loadingTasks = ref(false);

// --- API FETCH TASKS ---
const fetchTasks = async () => {
    if (!props.schedule?.id) return;
    
    loadingTasks.value = true;
    const token = localStorage.getItem('token');
    
    try {
        // Giả sử API listing task có hỗ trợ filter theo schedule_id
        const res = await axios.post(
            `${import.meta.env.VITE_API_BASE_URL}/api/task/listing`,
            {
                filters: { schedule_id: props.schedule.id }, // Lọc theo ID lịch trình
                sort_by: ['task_date'],
                sort_direction: ['desc'],
                page: 1,
                item_per_page: 50 // Lấy 50 task gần nhất
            },
            { headers: { Authorization: `Bearer ${token}` } }
        );
        tasks.value = res.data?.data?.data || [];
    } catch (e) {
        console.error("Lỗi tải tasks:", e);
    } finally {
        loadingTasks.value = false;
    }
};

// Gọi fetch khi chuyển sang tab tasks
watch(activeTab, (newTab) => {
    if (newTab === 'tasks' && tasks.value.length === 0) {
        fetchTasks();
    }
});

// --- HELPERS ---
const parseArray = (val) => {
    if (Array.isArray(val)) return val;
    try { return JSON.parse(val || '[]'); } catch { return []; }
};

const formatDate = (d, full = false) => {
    if (!d) return '';
    const options = full 
        ? { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }
        : { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(d).toLocaleDateString('vi-VN', options);
};

const formatType = (t) => t === 'fixed' ? 'Cố định' : 'Linh hoạt';
const formatRepeat = (t) => ({'daily':'Hàng ngày','weekly':'Hàng tuần','monthly':'Hàng tháng','none':'Một lần'}[t]);
const formatStatus = (s) => ({'pending':'Chưa làm','done':'Hoàn thành','failed':'Thất bại'}[s] || s);

const getStatusBadge = (s) => {
    const map = {
        done: 'bg-green-100 text-green-700',
        failed: 'bg-red-100 text-red-700',
        pending: 'bg-gray-100 text-gray-600'
    };
    return map[s] || 'bg-gray-100';
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
</style>