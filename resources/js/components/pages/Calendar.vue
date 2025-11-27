<template>
  <div class="p-3">
    <div class="flex gap-2 mb-4 items-stretch">
      
      <div class="flex-1 min-w-0">
        <FilterBar 
            :filters="filters" 
            :sortBy="sortBy" 
            :sortDirection="sortDirection" 
            :sortFields="sortFields"
            @updateFilters="handleFilterUpdate" 
            class="!mb-0 h-full" 
        /> 
      </div>

      <button 
        @click="openModal('add')"
        class="shrink-0 bg-green-500 hover:bg-green-600 text-white shadow-sm shadow-green-500/30 
               rounded-xl flex items-center justify-center transition-all active:scale-95
               w-12 md:w-auto md:px-5 gap-2 h-auto"
      >
        <PlusIcon class="w-6 h-6 text-white" />
        
        <span class="hidden md:inline font-bold text-sm">Thêm mới</span>
      </button>

    </div>

    <div v-if="loading" class="text-center py-4 text-gray-500">Đang tải dữ liệu...</div>
    <div v-else class="bg-white rounded-xl shadow-md divide-y">
        <div v-if="schedules.length === 0" class="p-4 text-center text-gray-400">Chưa có lịch nào</div>
        
        <ScheduleCard 
            v-for="item in schedules" :key="item.id" 
            :schedule="item"
            @view="openModal('view', item)"
            @edit="openModal('edit', item)"
            @pause="handleAction('pause', item)"
            @play="handleAction('play', item)"
            @delete="handleAction('delete', item)"
        />
    </div>

    <Pagination v-if="!loading && pagination.total > 0" 
        :currentPage="pagination.page" 
        :totalPages="pagination.lastPage" 
        :totalItems="pagination.total"
        :itemPerPage="pagination.itemPerPage" 
        @page-changed="handlePageChange"
        @item-per-page-changed="handleSizeChange" 
    />

    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-4xl shadow-xl relative max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">{{ modalTitle }}</h2>

            <ScheduleForm 
                v-if="modalType === 'add' || modalType === 'edit'"
                :initialData="modalType === 'edit' ? activeSchedule : null"
                @submit="handleFormSubmit"
                @cancel="closeModal"
            />
            
            <ScheduleDetail 
                v-if="modalType === 'view'" 
                :schedule="activeSchedule" 
                @close="closeModal" 
            />

            <div v-else class="text-center" v-if="modalType === 'delete' || modalType === 'pause' || modalType === 'play'">
                <p class="mb-4 text-lg">Bạn có chắc muốn thực hiện hành động này?</p>
                <div class="flex justify-center gap-3">
                    <button @click="closeModal" class="bg-gray-300 px-4 py-2 rounded">Hủy</button>
                    <button @click="confirmAction" class="bg-red-500 text-white px-4 py-2 rounded">Xác nhận</button>
                </div>
            </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Pagination from "../layouts/Pagination.vue";
import FilterBar from "../layouts/FilterBar.vue";
import ScheduleCard from "../schedules/ScheduleCard.vue"; // Import từ thư mục mới
import ScheduleForm from "../schedules/ScheduleForm.vue"; // Import từ thư mục mới
import { useSchedules } from "@/composables/useSchedules"; // Import logic API
import ScheduleDetail from "../schedules/ScheduleDetail.vue";
import { 
    PlusIcon
} from "@heroicons/vue/solid";
// Sử dụng Composable
const { schedules, loading, pagination, fetchSchedules, createSchedule, updateSchedule, changeStatus } = useSchedules();

// State cục bộ cho UI
const filters = ref({ title: "" });
const sortBy = ref("created_at");
const sortDirection = ref("desc");
const sortFields = [{ value: "created_at", label: "Ngày tạo" }, { value: "title", label: "Tiêu đề" }];

const showModal = ref(false);
const modalType = ref("");
const modalTitle = ref("");
const activeSchedule = ref(null);

// Init
onMounted(() => fetchSchedules(filters.value, { sortBy: sortBy.value, sortDirection: sortDirection.value }));

// Handlers UI
const handleFilterUpdate = (payload) => {
    filters.value = payload.filters;
    sortBy.value = payload.sortBy;
    sortDirection.value = payload.sortDirection;
    fetchSchedules(filters.value, payload);
};

const handlePageChange = (p) => {
    pagination.value.page = p;
    fetchSchedules(filters.value, { sortBy: sortBy.value, sortDirection: sortDirection.value });
};

const handleSizeChange = (s) => {
    pagination.value.itemPerPage = s;
    fetchSchedules(filters.value, { sortBy: sortBy.value, sortDirection: sortDirection.value });
};

// Modal Handlers
const openModal = (type, item = null) => {
    modalType.value = type;
    activeSchedule.value = item;
    
    const titles = { add: "➕ Thêm mới", edit: "✏️ Chỉnh sửa", view: "📄 Chi tiết", delete: "🗑️ Xóa", pause: "⏸️ Dừng", play: "▶️ Kích hoạt" };
    modalTitle.value = titles[type] || "Thông báo";
    showModal.value = true;
};

const closeModal = () => { showModal.value = false; activeSchedule.value = null; };

// Actions Logic
const handleFormSubmit = async (payload) => {
    let success = false;
    if (modalType.value === 'add') {
        success = await createSchedule(payload);
    } else {
        success = await updateSchedule(activeSchedule.value.id, payload);
    }
    
    if (success) {
        closeModal();
        fetchSchedules(filters.value); // Reload list
    }
};

const handleAction = (type, item) => openModal(type, item);

const confirmAction = async () => {
    // modalType lúc này là 'delete', 'pause', hoặc 'play'
    const success = await changeStatus(activeSchedule.value.id, modalType.value);
    if (success) {
        closeModal();
        fetchSchedules(filters.value);
    }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>