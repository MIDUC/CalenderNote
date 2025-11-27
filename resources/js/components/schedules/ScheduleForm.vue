<template>
  <form class="space-y-6" @submit.prevent="submitForm">
    <div class="grid grid-cols-2 gap-3 text-sm">
      <input v-model="form.title" type="text" placeholder="Tiêu đề" class="border rounded p-3 w-full" required />
      
      <select v-model="form.type" class="border rounded p-3 w-full">
        <option value="" disabled>-- Chọn loại lịch --</option>
        <option value="fixed">Cố định (Fixed)</option>
        <option value="flexible">Linh hoạt (Flexible)</option>
      </select>

      <textarea v-model="form.description" placeholder="Mô tả chi tiết" class="border rounded p-3 col-span-2" rows="3"></textarea>

      <div class="col-span-2 bg-gray-50 p-3 rounded border">
          <label class="font-bold mb-2 block">Cấu hình lặp lại</label>
          <select v-model="form.repeat_type" class="border rounded p-3 w-full mb-3">
            <option value="none">Không lặp</option>
            <option value="daily">Hàng ngày</option>
            <option value="weekly">Hàng tuần</option>
            <option value="monthly">Hàng tháng</option>
          </select>

          <div v-if="form.repeat_type === 'weekly'" class="flex flex-wrap gap-4">
            <label v-for="day in daysOfWeek" :key="day.value" class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" :value="day.value" v-model="form.days_of_week" /> {{ day.label }}
            </label>
          </div>

          <div v-if="form.repeat_type === 'monthly'" class="grid grid-cols-7 gap-2 mt-2">
            <label v-for="d in 31" :key="d" class="border rounded py-1 text-center hover:bg-blue-50 cursor-pointer">
              <input type="checkbox" :value="d" v-model="form.days_of_month" class="hidden peer" />
              <span class="peer-checked:font-bold peer-checked:text-blue-600">{{ d }}</span>
            </label>
          </div>
      </div>

      <input v-model="form.start_date" type="date" class="border rounded p-3" required />
      <input v-model="form.end_date" type="date" class="border rounded p-3" placeholder="Ngày kết thúc" />

      <div class="col-span-2 flex flex-wrap gap-3 mt-2 items-center bg-gray-50 p-3 rounded border">
        <label class="flex items-center gap-2 font-medium">
          <input type="checkbox" v-model="form.has_fixed_time" /> Có giờ cố định
        </label>
        
        <template v-if="form.has_fixed_time">
            <input v-model="form.fixed_time" type="time" class="border rounded p-2" />
            <input v-model.number="form.notify_before_minutes" type="number" placeholder="Báo trước (phút)" class="border rounded p-2 w-32" />
            <input v-model="form.notify_times" type="number" placeholder="Số lần nhắc" class="border rounded p-2 w-32" />
        </template>
        
        <div class="w-full h-px bg-gray-200 my-1"></div>
        
        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.shareable" /> Chia sẻ</label>
        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.require_checkin" /> Yêu cầu check-in</label>
      </div>
    </div>

    <div class="mt-6 text-right flex justify-end gap-2">
      <button type="button" @click="$emit('cancel')" class="bg-gray-500 text-white px-4 py-2 rounded">Hủy</button>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
          {{ isEdit ? 'Cập nhật' : 'Thêm mới' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
    initialData: { type: Object, default: null } // Nếu có data truyền vào thì là Edit
});
const emit = defineEmits(['submit', 'cancel']);

const daysOfWeek = [
  { label: "Thứ 2", value: 1 }, { label: "Thứ 3", value: 2 }, { label: "Thứ 4", value: 3 },
  { label: "Thứ 5", value: 4 }, { label: "Thứ 6", value: 5 }, { label: "Thứ 7", value: 6 }, { label: "CN", value: 7 },
];

const isEdit = computed(() => !!props.initialData);

// Khởi tạo form
const form = ref({
  title: "", description: "", type: "", repeat_type: "none",
  days_of_week: [], days_of_month: [],
  start_date: "", end_date: "",
  has_fixed_time: false, fixed_time: "",
  notify_before_minutes: 0, notify_times: null,
  shareable: false, require_checkin: false,
  is_active: false,
});

onMounted(() => {
    if (props.initialData) {
        fillData(props.initialData);
    }
});

// Hàm fill data từ props vào form (Logic cũ của bạn)
const fillData = (schedule) => {
    form.value = { ...schedule };
    // Xử lý Array/JSON
    try {
        form.value.days_of_week = Array.isArray(schedule.days_of_week) ? schedule.days_of_week : JSON.parse(schedule.days_of_week || '[]');
        form.value.days_of_month = Array.isArray(schedule.days_of_month) ? schedule.days_of_month : JSON.parse(schedule.days_of_month || '[]');
    } catch(e) { /* fallback */ }
    
    // Xử lý Boolean (Tinyint)
    form.value.has_fixed_time = schedule.has_fixed_time === 1;
    form.value.shareable = schedule.shareable === 1;
    form.value.require_checkin = schedule.require_checkin === 1;
};

const submitForm = () => {
    // Chuẩn bị payload để gửi ra ngoài
    const payload = { ...form.value };
    
    // Convert array -> JSON string
    payload.days_of_week = JSON.stringify(payload.days_of_week);
    payload.days_of_month = JSON.stringify(payload.days_of_month);
    
    // Convert boolean -> 1/0
    payload.has_fixed_time = payload.has_fixed_time ? 1 : 0;
    payload.shareable = payload.shareable ? 1 : 0;
    payload.require_checkin = payload.require_checkin ? 1 : 0;

    // Emit lên cha xử lý API
    emit('submit', payload);
};
</script>