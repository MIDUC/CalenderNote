<template>
  <form @submit.prevent="handleSubmit" class="flex flex-col h-full bg-white">
    
    <div class="flex-1 overflow-y-auto p-5 custom-scrollbar">
      
      <div class="space-y-5">
          
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">
              Tên công việc <span class="text-red-500">*</span>
            </label>
            <input v-model="form.title" ref="titleInput" type="text" placeholder="Ví dụ: Mua sữa..." 
                   class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required />
          </div>

          <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-4">
             <div>
                <label class="block text-xs font-bold text-blue-800 mb-2 uppercase">📅 Ngày thực hiện</label>
                <input v-model="form.task_date" type="date" class="w-full p-3 border border-blue-200 rounded-xl bg-white font-medium text-gray-700" required />
                <div class="flex gap-2 mt-3">
                    <button type="button" @click="setDate('today')" class="px-4 py-1.5 rounded-lg text-sm border bg-white hover:bg-blue-50 shadow-sm">Hôm nay</button>
                    <button type="button" @click="setDate('tomorrow')" class="px-4 py-1.5 rounded-lg text-sm border bg-white hover:bg-blue-50 shadow-sm">Ngày mai</button>
                </div>
             </div>
             <div class="border-t border-blue-200 pt-4">
                 <div class="flex justify-between items-center mb-2">
                    <label class="block text-xs font-bold text-blue-800 uppercase">⏰ Giờ cụ thể</label>
                    <button v-if="form.fixed_time" @click="form.fixed_time = ''" type="button" class="text-xs text-red-500 font-medium bg-red-50 px-2 py-1 rounded">✕ Xóa</button>
                 </div>
                 <input v-model="form.fixed_time" type="time" class="w-full p-3 border border-blue-200 rounded-xl bg-white font-medium" />
             </div>
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Ghi chú thêm</label>
            <textarea v-model="form.description" rows="3" placeholder="..." class="w-full p-3 border border-gray-300 rounded-xl resize-none"></textarea>
          </div>

      </div> <div class="mt-8 pt-4 border-t border-gray-100 flex gap-3 pb-10">
          <button type="button" @click="$emit('cancel')" 
            class="px-5 py-3 rounded-xl bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition active:scale-95">
              Hủy
          </button>
          <button type="submit" 
            class="flex-1 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/30 active:scale-95 transition flex justify-center items-center gap-2"
            :disabled="loading">
              <span v-if="loading" class="animate-spin h-5 w-5 border-2 border-white rounded-full border-t-transparent"></span>
              <span v-else>{{ isEdit ? 'Cập nhật' : 'Thêm công việc' }}</span>
          </button>
      </div>

    </div>
  </form>
</template>

<script setup>
// ... (Phần Script giữ nguyên như cũ, không cần sửa gì)
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
  initialData: { type: Object, default: null },
  loading: { type: Boolean, default: false }
});

const emit = defineEmits(['submit', 'cancel']);
const titleInput = ref(null);
const isEdit = computed(() => !!props.initialData);

const defaultForm = {
  title: '',
  description: '',
  task_date: new Date().toISOString().split('T')[0],
  fixed_time: '', 
  schedule_id: null,
  status: 'pending'
};

const form = ref({ ...defaultForm });

onMounted(() => {
    if (props.initialData) {
        form.value = { ...props.initialData };
        if (form.value.task_date) {
            form.value.task_date = form.value.task_date.split('T')[0];
        }
    }
    setTimeout(() => titleInput.value?.focus(), 100);
});

const handleSubmit = () => {
    const payload = { ...form.value };
    if (!payload.fixed_time) payload.fixed_time = null;
    if (!isEdit.value) payload.schedule_id = null;
    emit('submit', payload);
};

const setDate = (type) => {
    const d = new Date();
    if (type === 'tomorrow') d.setDate(d.getDate() + 1);
    form.value.task_date = d.toISOString().split('T')[0];
}
</script>

<style scoped>
/* Vẫn giữ scrollbar đẹp */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
</style>