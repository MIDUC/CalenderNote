<template>
  <div class="space-y-4">
    <!-- Thanh filter & sort -->
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
      <div class="flex-1 w-full lg:w-auto">
        <FilterBar :filters="filters" :sortBy="sortBy" :sortDirection="sortDirection" :sortFields="sortFields"
          @updateFilters="handleFilterUpdate" />
      </div>
      <button @click="openModal('add')"
        class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 transition-all duration-200 hover:scale-105 flex items-center gap-2 w-full lg:w-auto justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Thêm mới</span>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <div class="flex flex-col items-center gap-3">
        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-gray-500 font-medium">Đang tải dữ liệu...</p>
      </div>
    </div>

    <!-- Danh sách lịch -->
    <div v-else class="grid grid-cols-1 gap-4">
      <div v-for="schedule in schedules" :key="schedule.id" 
        class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100/50 overflow-hidden group hover:scale-[1.02]">
        <div class="p-5 lg:p-6">
          <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            <!-- Main Content -->
            <div class="flex-1 space-y-3">
              <div class="flex items-start gap-3 flex-wrap">
                <h2 class="font-bold text-lg lg:text-xl text-gray-800 group-hover:text-blue-600 transition-colors">
                  {{ schedule.title }}
                </h2>
                <span v-if="schedule.is_active"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-700 border border-emerald-200">
                  <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                  Đang chạy
                </span>
                <span v-else
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                  <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                  Đã dừng
                </span>
              </div>

              <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <div v-if="schedule.fixed_time" class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="font-medium">{{ schedule.fixed_time }}</span>
                </div>
                <div v-if="schedule.repeat_type !== 'none'" class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  <span class="font-medium">{{ repeatLabel(schedule.repeat_type, schedule.repeat_type) }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span class="font-medium">
                    {{ formatDate(schedule.start_date) }}
                    <span v-if="schedule.end_date"> - {{ formatDate(schedule.end_date) }}</span>
                    <span v-else class="text-gray-400"> - Không giới hạn</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 lg:flex-nowrap">
              <button @click="openModal('view', schedule)"
                class="p-3 rounded-xl bg-gradient-to-br from-cyan-400 to-cyan-500 text-white shadow-md hover:shadow-lg hover:scale-110 transition-all duration-200 flex items-center justify-center">
                <EyeIcon class="w-5 h-5" />
              </button>

              <button @click="openModal('edit', schedule)"
                class="p-3 rounded-xl bg-gradient-to-br from-blue-400 to-blue-500 text-white shadow-md hover:shadow-lg hover:scale-110 transition-all duration-200 flex items-center justify-center">
                <PencilIcon class="w-5 h-5" />
              </button>

              <button v-if="schedule.is_active" @click="openModal('pause', schedule)"
                class="p-3 rounded-xl bg-gradient-to-br from-amber-400 to-amber-500 text-white shadow-md hover:shadow-lg hover:scale-110 transition-all duration-200 flex items-center justify-center">
                <PauseIcon class="w-5 h-5" />
              </button>

              <button v-if="!schedule.is_active" @click="openModal('play', schedule)"
                class="p-3 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-500 text-white shadow-md hover:shadow-lg hover:scale-110 transition-all duration-200 flex items-center justify-center">
                <PlayIcon class="w-5 h-5" />
              </button>

              <button @click="openModal('delete', schedule)"
                class="p-3 rounded-xl bg-gradient-to-br from-red-400 to-red-500 text-white shadow-md hover:shadow-lg hover:scale-110 transition-all duration-200 flex items-center justify-center">
                <TrashIcon class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Phân trang -->
    <Pagination v-if="!loading && total > 0" :currentPage="page" :totalPages="lastPage" :totalItems="total"
      :itemPerPage="itemPerPage" @page-changed="loadSchedules" @item-per-page-changed="handleItemPerPageChange" />

    <!-- Modal chung -->
    <transition name="modal">
      <div v-if="showModal" 
        @click.self="closeModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl p-6 lg:p-8 w-full max-w-4xl shadow-2xl relative overflow-y-auto max-h-[90vh] animate-modal-in">
          <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              {{ modalTitle }}
            </h2>
            <button @click="closeModal" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
              <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="text-gray-700">
            <template v-if="modalType === 'view'">
              <div class="space-y-3 text-sm text-gray-700">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-2 mb-3">
                  Chi tiết Lịch trình
                </h3>

                <p><b>Tiêu đề:</b> {{ activeSchedule?.title }}</p>
                <p><b>Mô tả:</b> {{ activeSchedule?.description }}</p>

                <p><b>Người tạo (ID):</b> {{ activeSchedule?.user_id }}</p>

                <p>
                  <b>Thời gian:</b> {{ formatDate(activeSchedule?.start_date) }} -
                  {{ formatDate(activeSchedule?.end_date) }}
                </p>

                <p><b>Loại lịch:</b> {{ type(activeSchedule?.type) }}</p>
                <p><b>Lặp lại:</b> {{ repeatLabel(activeSchedule?.repeat_type) }}</p>

                <div v-if="activeSchedule?.repeat_type === 'weekly'">
                  <p><b>Lặp lại hàng tuần: </b> {{ activeSchedule?.days_of_week }}</p>
                </div>
                <div v-else-if="activeSchedule?.repeat_type === 'monthly'">
                  <p>
                    <b>Lặp lại hàng tháng (các ngày): </b>
                    {{ activeSchedule?.days_of_month }}
                  </p>
                </div>

                <div class="border-t pt-2 mt-3">
                  <p>
                    <b>Có giờ cố định: </b>
                    <span :class="
                        activeSchedule?.has_fixed_time
                          ? 'text-green-600'
                          : 'text-gray-500'
                      ">
                      {{ activeSchedule?.has_fixed_time ? "Có" : "Không" }}
                    </span>
                  </p>

                  <div v-if="activeSchedule?.has_fixed_time">
                    <p><b>Giờ cố định: </b> {{ activeSchedule?.fixed_time }}</p>
                    <p>
                      <b>Thông báo trước: </b>
                      {{ activeSchedule?.notify_before_minutes }} phút
                    </p>

                    <p>
                      <b>Số lần nhắc: </b>
                      {{ activeSchedule?.notify_times ?? "Không giới hạn" }}
                    </p>
                  </div>
                </div>

                <div class="border-t pt-2 mt-3 space-y-1">
                  <p>
                    <b>Trạng thái: </b>
                    <span :class="
                        activeSchedule?.is_active ? 'text-green-600' : 'text-red-600'
                      ">
                      {{ activeSchedule?.is_active ? "Đang kích hoạt" : "Đã dừng" }}
                    </span>
                  </p>
                  <p>
                    <b>Chia sẻ: </b>
                    <span :class="
                        activeSchedule?.shareable ? 'text-green-600' : 'text-gray-500'
                      ">
                      {{ activeSchedule?.shareable ? "Có" : "Không" }}
                    </span>
                  </p>
                  <p>
                    <b>Yêu cầu Check-in: </b>
                    <span :class="
                        activeSchedule?.require_checkin ? 'text-red-600' : 'text-gray-500'
                      ">
                      {{ activeSchedule?.require_checkin ? "Có" : "Không" }}
                    </span>
                  </p>
                </div>

                <div class="text-xs text-gray-400 pt-2 border-t mt-3">
                  <p>Tạo lúc: {{ formatDate(activeSchedule?.created_at, true) }}</p>
                  <p>Cập nhật lúc: {{ formatDate(activeSchedule?.updated_at, true) }}</p>
                </div>
              </div>
            </template>

            <template v-else-if="modalType === 'pause'">
              <p>
                ⏸️ Bạn có chắc muốn dừng lịch "<b>{{ activeSchedule?.title }}</b>"?
              </p>
            </template>

            <template v-else-if="modalType === 'play'">
              <p>
                ▶️ Chạy lịch "<b>{{ activeSchedule?.title }}</b> và tạo các task liên quan"?
              </p>
            </template>

            <template v-else-if="modalType === 'delete'">
              <p>
                ⚠️ Bạn có chắc muốn xóa lịch "<b>{{ activeSchedule?.title }}</b>"?
              </p>
            </template>

            <template v-else-if="modalType === 'add' || modalType === 'edit'">
              <form class="space-y-6" @submit.prevent="modalType === 'add' ? handleAdd() : handleEdit()">
                <div class="grid grid-cols-2 gap-3 text-sm">
                  <input v-model="form.title" type="text" placeholder="Tiêu đề" class="border rounded p-3 w-full"
                    required />

                  <select v-model="form.type" class="border rounded p-3 w-full">
                    <option value="" disabled>-- Chọn loại lịch --</option>
                    <option value="fixed">Cố định (Fixed)</option>
                    <option value="flexible">Linh hoạt (Flexible)</option>
                  </select>

                  <textarea v-model="form.description" placeholder="Mô tả chi tiết"
                    class="border rounded p-3 col-span-2" rows="3"></textarea>

                  <select v-model="form.repeat_type" class="border rounded p-3 w-full">
                    <option value="none">Không lặp</option>
                    <option value="daily">Hàng ngày</option>
                    <option value="weekly">Hàng tuần</option>
                    <option value="monthly">Hàng tháng</option>
                  </select>

                  <div class="col-span-2">
                    <div v-if="form.repeat_type === 'weekly'" class="col-span-2">
                      <label class="block mb-2 font-semibold">Chọn các ngày trong tuần:</label>
                      <div class="flex flex-wrap gap-4">
                        <label v-for="day in daysOfWeek" :key="day.value" class="flex items-center gap-2">
                          <input type="checkbox" :value="day.value" v-model="form.days_of_week" />
                          {{ day.label }}
                        </label>
                      </div>
                    </div>

                    <div v-if="form.repeat_type === 'monthly'" class="mt-2">
                      <p class="font-medium mb-2 text-gray-700">
                        Chọn các ngày trong tháng:
                      </p>
                      <div class="grid grid-cols-7 gap-2 text-center">
                        <label v-for="d in 31" :key="d" class="border rounded py-1 cursor-pointer hover:bg-blue-50">
                          <input type="checkbox" :value="d" v-model="form.days_of_month" class="mr-1" />
                          {{ d }}
                        </label>
                      </div>
                    </div>
                  </div>

                  <input v-model="form.start_date" type="date" class="border rounded p-3" placeholder="Ngày bắt đầu"
                    required />
                  <input v-model="form.end_date" type="date" class="border rounded p-3" placeholder="Ngày kết thúc" />

                  <div class="col-span-2 flex flex-wrap gap-3 mt-2 items-center">
                    <label class="flex items-center gap-2">
                      <input type="checkbox" v-model="form.has_fixed_time" />
                      Có giờ cố định
                    </label>

                    <input v-if="form.has_fixed_time" v-model="form.fixed_time" type="time"
                      class="border rounded p-2" />

                    <input v-if="form.has_fixed_time" v-model.number="form.notify_before_minutes" type="number"
                      placeholder="Thông báo trước (phút)" class="border rounded p-2 w-48" min="0" />

                    <input v-model="form.notify_times" type="number" placeholder="Số lần nhắc (để trống = null)"
                      class="border rounded p-2 w-48" min="0" />

                    <!-- <label class="flex items-center gap-2">
                      <input type="checkbox" v-model="form.is_active" /> Kích hoạt
                    </label> -->
                    <label class="flex items-center gap-2">
                      <input type="checkbox" v-model="form.shareable" /> Chia sẻ
                    </label>
                    <label class="flex items-center gap-2">
                      <input type="checkbox" v-model="form.require_checkin" /> Yêu cầu
                      check-in
                    </label>
                  </div>
                </div>
              </form>
            </template>
          </div>

          <!-- Nút -->
          <div class="mt-6 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
            <button @click="closeModal" 
              class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all duration-200 hover:scale-105">
              Hủy
            </button>

            <button v-if="modalType === 'add' || modalType === 'edit'"
              @click="modalType === 'add' ? handleAdd() : handleEdit()"
              class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 transition-all duration-200 hover:scale-105">
              {{ modalType === "add" ? "Thêm mới" : "Cập nhật" }}
            </button>

            <button v-else-if="modalType === 'delete' || modalType === 'pause' || modalType === 'play'" 
              @click="handleConfirm"
              class="px-6 py-3 rounded-xl bg-gradient-to-r from-red-500 to-rose-500 text-white font-semibold shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40 transition-all duration-200 hover:scale-105">
              Xác nhận
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { useToast } from "vue-toastification";
import "vue-toastification/dist/index.css";
import {
  EyeIcon,
  PencilIcon,
  PauseIcon,
  TrashIcon,
  PlayIcon,
  PlusIcon,
} from "@heroicons/vue/solid";
import { ref, onMounted } from "vue";
import api from "../../api";
import Pagination from "../layouts/Pagination.vue";
import FilterBar from "../layouts/FilterBar.vue";
const schedules = ref([]);
const loading = ref(true);
const daysOfWeek = [
  { label: "Thứ 2", value: 1 },
  { label: "Thứ 3", value: 2 },
  { label: "Thứ 4", value: 3 },
  { label: "Thứ 5", value: 4 },
  { label: "Thứ 6", value: 5 },
  { label: "Thứ 7", value: 6 },
  { label: "Chủ nhật", value: 7 },
];
const filters = ref({ title: "" });
const sortBy = ref("created_at");
const sortDirection = ref("desc");
const sortFields = [
  { value: "created_at", label: "Ngày tạo" },
  { value: "title", label: "Tiêu đề" },
  { value: "start_date", label: "Ngày bắt đầu" },
];
const page = ref(1);
const itemPerPage = ref(10);
const total = ref(0);
const lastPage = ref(1);
const token = localStorage.getItem("token");
const toast = useToast();
// Modal chung
const showModal = ref(false);
const modalType = ref("");
const modalTitle = ref("");
const activeSchedule = ref(null);

const openModal = (type, schedule = null) => {
  modalType.value = type;
  activeSchedule.value = schedule;
  switch (type) {
    case "view":
      modalTitle.value = "📄 Xem chi tiết lịch";
      break;
    case "edit":
      modalTitle.value = "✏️ Chỉnh sửa lịch";
      copyScheduleToForm(schedule);
      break;
    case "pause":
      modalTitle.value = "⏸️ Dừng lịch luyện tập";
      break;
    case "play":
      modalTitle.value = "▶️ Chạy lịch luyện tập";
      break;
    case "delete":
      modalTitle.value = "🗑️ Xóa lịch luyện tập";
      break;
    case "add":
      modalTitle.value = "➕ Thêm lịch mới";
      break;
  }
  showModal.value = true;
};
const resetForm = () => {
  form.value = JSON.parse(JSON.stringify(initialFormState));
};
const closeModal = () => {
  showModal.value = false;
  resetForm();
};
const form = ref({
  title: "",
  description: "",
  type: "",
  repeat_type: "none",
  days_of_week: [],
  days_of_month: [],
  start_date: "",
  end_date: "",
  has_fixed_time: false,
  fixed_time: "",
  notify_before_minutes: 0,
  notify_times: null,
  is_active: false,
  shareable: false,
  require_checkin: false,
});

const initialFormState = {
  title: "",
  description: "",
  type: "",
  repeat_type: "none",
  days_of_week: [],
  days_of_month: [],
  start_date: "",
  end_date: "",
  has_fixed_time: false,
  fixed_time: "",
  notify_before_minutes: 0,
  notify_times: null,
  is_active: false,
  shareable: false,
  require_checkin: false,
};
const handleConfirm = () => {
  switch (modalType.value) {
    case "add":
      handleAdd(); // gọi API thêm
      break;
    case "edit":
      handleEdit(activeSchedule.value); // gọi API sửa
      break;
    case "pause":
      handlePause(activeSchedule.value); // gọi API dừng
      break;
    case "play":
      handlePlay(activeSchedule.value); // gọi API chạy
      break;
    case "delete":
      handleDelete(activeSchedule.value); // gọi API xóa
      break;
    default:
      console.warn("Không có hành động xác nhận cho loại modal này");
  }
};

// 🔹 Load danh sách giữ nguyên
const loadSchedules = async (newPage = 1) => {
  page.value = newPage;
  loading.value = true;
  try {
    const res = await api.post(
      `/schedule/listing`,
      {
        filters: filters.value,
        sort_by: [sortBy.value],
        sort_direction: [sortDirection.value],
        page: page.value,
        item_per_page: itemPerPage.value,
      },
      { headers: { Accept: "application/json", Authorization: `Bearer ${token}` } }
    );
    const data = res.data?.data;
    schedules.value = data?.data || [];
    total.value = data?.total || 0;
    lastPage.value = data?.last_page || 1;
  } catch (err) {
    console.error("Lỗi tải lịch:", err);
  } finally {
    loading.value = false;
  }
};

// 🔹 Filter/Sort
const handleFilterUpdate = (payload) => {
  filters.value = payload.filters;
  sortBy.value = payload.sortBy;
  sortDirection.value = payload.sortDirection;
  loadSchedules(1);
};

const handleItemPerPageChange = (newSize) => {
  itemPerPage.value = newSize;
  loadSchedules(1);
};

onMounted(loadSchedules);

const formatDate = (dateStr) => {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  return date.toLocaleDateString("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
};

const repeatLabel = (type, days) => {
  if (type === "weekly" && days) return "Hàng tuần";
  if (type === "monthly") return "Hàng tháng";
  if (type === "daily") return "Hàng ngày";
  return "Một lần";
};

const type = (type) => {
  if (type === "fixed") return "Cố định";
  if (type === "flexible") return "Linh hoạt";
  return "Không xác định";
};
const handleAdd = async () => {
  try {
    // --- Chuẩn bị Payload (Giữ nguyên logic chuyển đổi) ---
    const payload = { ...form.value };
    payload.days_of_week = JSON.stringify(payload.days_of_week);
    payload.days_of_month = JSON.stringify(payload.days_of_month);

    if (payload.notify_times === "" || payload.notify_times === null) {
      payload.notify_times = null;
    } else {
      payload.notify_times = parseInt(payload.notify_times, 10);
    }

    if (payload.end_date === "") payload.end_date = null;
    if (!payload.has_fixed_time || payload.fixed_time === "") {
      payload.fixed_time = null;
    }
    // --------------------------------------------------

    const url = `/schedule/store`;

    await api.post(url, payload);

    // ⭐ TRƯỜNG HỢP THÀNH CÔNG: Đóng modal + Thông báo

    toast.success("Đã thêm lịch trình thành công!");
    closeModal();
    console.log("-> Đang gọi tải lại danh sách...");
    await loadSchedules();
    console.log("-> Tải lại hoàn tất.");

    // (Tải lại danh sách lịch trình nếu cần)
    // fetchSchedules();
  } catch (error) {
    console.error("Lỗi khi thêm lịch trình:", error);

    // ⭐ TRƯỜNG HỢP THẤT BẠI: Thông báo lỗi + Giữ nguyên modal

    // let errorMessage = "Thêm lịch trình thất bại. Vui lòng thử lại.";

    if (error.response) {
      if (error.response.status === 422) {
        // Lỗi validation (thường là lỗi nhập liệu)
        errorMessage = error.response.data.message || "Dữ liệu nhập vào không hợp lệ.";
      } else if (error.response.data && error.response.data.message) {
        // Lỗi server có tin nhắn cụ thể
        errorMessage = error.response.data.message;
      }
      // Bạn có thể log chi tiết lỗi validation ở đây:
      // console.error('Chi tiết lỗi validation:', error.response.data.errors);
    }

    toast.error(errorMessage);
    // Không gọi closeModal() nên modal vẫn được giữ nguyên.
  }
};

const copyScheduleToForm = (schedule) => {
  if (!schedule) return;

  // Chỉ gán các thuộc tính cần chỉnh sửa
  form.value.title = schedule.title;
  form.value.description = schedule.description;
  form.value.type = schedule.type;
  form.value.repeat_type = schedule.repeat_type;

  // ⭐ Xử lý mảng (cần deep copy nếu chúng là JSON string trong schedule)
  try {
    form.value.days_of_week = Array.isArray(schedule.days_of_week)
      ? schedule.days_of_week
      : JSON.stringify(schedule.days_of_week);
    form.value.days_of_month = Array.isArray(schedule.days_of_month)
      ? schedule.days_of_month
      : JSON.stringify(schedule.days_of_month);
  } catch (e) {
    // Fallback nếu parsing thất bại
    form.value.days_of_week = [];
    form.value.days_of_month = [];
  }

  form.value.start_date = schedule.start_date;
  form.value.end_date = schedule.end_date;
  form.value.has_fixed_time = schedule.has_fixed_time === 1; // tinyint to boolean
  form.value.fixed_time = schedule.fixed_time;
  form.value.notify_before_minutes = schedule.notify_before_minutes;
  form.value.notify_times = schedule.notify_times;
  form.value.is_active = schedule.is_active === 1; // tinyint to boolean
  form.value.shareable = schedule.shareable === 1; // tinyint to boolean
  form.value.require_checkin = schedule.require_checkin === 1; // tinyint to boolean
};

const handleEdit = async () => {
  if (!activeSchedule.value || !activeSchedule.value.id) {
    // Kiểm tra an toàn
    toast.error("Không tìm thấy lịch trình để cập nhật.");
    return;
  }

  // ⭐ Dữ liệu cần gửi đi (bao gồm ID của lịch trình)
  const scheduleId = activeSchedule.value.id;
  const dataToSend = {
    ...form.value,
    // Đảm bảo các giá trị tinyint được gửi dưới dạng 1/0
    has_fixed_time: form.value.has_fixed_time ? 1 : 0,
    is_active: form.value.is_active ? 1 : 0,
    shareable: form.value.shareable ? 1 : 0,
    require_checkin: form.value.require_checkin ? 1 : 0,
    // Bạn có thể cần xử lý days_of_week/month thành string/JSON trước khi gửi
  };

  try {
    // ⭐ GỌI API CẬP NHẬT (Sử dụng scheduleId để xác định lịch trình)
    const response = await api.put(
      `/schedule/update/${scheduleId}`,
      dataToSend
    );

    // Cập nhật thành công
    toast.success("Lịch trình đã được cập nhật thành công!");
    closeModal();
    console.log("-> Đang gọi tải lại danh sách...");
    await loadSchedules();
    console.log("-> Tải lại hoàn tất.");
  } catch (error) {
    console.error("Lỗi khi cập nhật lịch trình:", error);
    let errorMessage = "Cập nhật lịch trình thất bại. Vui lòng thử lại.";
    // ... (Logic xử lý errorMessage tương tự handleAdd)
    toast.error(errorMessage);
  }
};

const handlePlay = async () => {
  if (!activeSchedule.value || !activeSchedule.value.id) {
    toast.error("Không tìm thấy lịch trình để cập nhật.");
    return;
  }

  const scheduleId = activeSchedule.value.id;

  try {
    const response = await api.post(`/schedule/play/${scheduleId}`);
    toast.success("Lịch trình đã được chạy thành công!");
    closeModal();
    await loadSchedules();
  } catch (error) {
    console.error("Lỗi khi chạy lịch trình:", error);
    toast.error("Chạy lịch trình thất bại. Vui lòng thử lại.");
  }
};

const handlePause = async () => {
  if (!activeSchedule.value || !activeSchedule.value.id) {
    toast.error("Không tìm thấy lịch trình để cập nhật.");
    return;
  }

  const scheduleId = activeSchedule.value.id;

  try {
    const response = await api.put(`/schedule/update/${scheduleId}`, {
      is_active: 0,
    });
    toast.success("Lịch trình đã được dừng thành công!");
    closeModal();
    await loadSchedules();
  } catch (error) {
    console.error("Lỗi khi dừng lịch trình:", error);
    toast.error("Dừng lịch trình thất bại. Vui lòng thử lại.");
  }
};

const handleDelete = async () => {
  if (!activeSchedule.value || !activeSchedule.value.id) {
    toast.error("Không tìm thấy lịch trình để xóa.");
    return;
  }

  const scheduleId = activeSchedule.value.id;

  try {
    const response = await api.delete(`/schedule/delete/${scheduleId}`);
    toast.success("Lịch trình đã được xóa thành công!");
    closeModal();
    await loadSchedules();
  } catch (error) {
    console.error("Lỗi khi xóa lịch trình:", error);
    toast.error("Xóa lịch trình thất bại. Vui lòng thử lại.");
  }
};

</script>

<style scoped>
/* Modal Animation */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.95) translateY(-10px);
}

@keyframes modal-in {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-modal-in {
  animation: modal-in 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Form inputs */
input[type="text"],
input[type="date"],
input[type="time"],
input[type="number"],
select,
textarea {
  @apply border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200;
}

input[type="checkbox"] {
  @apply w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500;
}
</style>
