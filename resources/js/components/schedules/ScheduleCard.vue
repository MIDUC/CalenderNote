<template>
  <div class="p-4 hover:bg-gray-50 transition border-b last:border-0 relative group">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
      
      <div class="col-span-2 pr-10 md:pr-0">
         <div class="flex items-center gap-3">
            <h2 class="font-semibold text-lg text-indigo-700">{{ schedule.title }}</h2>
            <span v-if="schedule.is_active" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold whitespace-nowrap">🟢 Đang chạy</span>
            <span v-else class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-bold whitespace-nowrap">⚪ Đã dừng</span>
         </div>
         <div class="flex items-center mt-2 text-sm text-gray-500 space-x-2">
            <span>🕒 {{ schedule.fixed_time || 'Tự do' }}</span>
            <span v-if="schedule.repeat_type !== 'none'">🔁 {{ repeatLabel(schedule.repeat_type) }}</span>
         </div>
         <div class="text-xs text-gray-400 mt-1">
             {{ formatDate(schedule.start_date) }} - {{ formatDate(schedule.end_date) }}
         </div>
      </div>

      <div class="hidden md:flex justify-end items-start mt-1 gap-2">
             <button @click="$emit('view')" class="bg-cyan-500 text-white p-2 rounded-full shadow hover:opacity-90" title="Xem">
                <EyeIcon class="w-4 h-4" />
             </button>
             <button @click="$emit('edit')" class="bg-blue-500 text-white p-2 rounded-full shadow hover:opacity-90" title="Sửa">
                <PencilIcon class="w-4 h-4" />
             </button>
             
             <button v-if="schedule.is_active" @click="$emit('pause')" class="bg-yellow-500 text-white p-2 rounded-full shadow hover:opacity-90" title="Dừng">
                <PauseIcon class="w-4 h-4" />
             </button>
             <button v-else @click="$emit('play')" class="bg-green-500 text-white p-2 rounded-full shadow hover:opacity-90" title="Chạy">
                <PlayIcon class="w-4 h-4" />
             </button>
             
             <button @click="$emit('delete')" class="bg-red-500 text-white p-2 rounded-full shadow hover:opacity-90" title="Xóa">
                <TrashIcon class="w-4 h-4" />
             </button>
      </div>

      <div class="md:hidden absolute top-3 right-2">
            <button @click.stop="toggleMenu" class="p-2 text-gray-400 hover:text-gray-600 rounded-full transition-colors active:bg-gray-100">
                <DotsVerticalIcon class="w-5 h-5" />
            </button>

            <transition name="scale">
                <div v-if="showMenu" class="absolute right-0 top-8 w-48 bg-white rounded-xl shadow-xl border border-gray-100 z-20 overflow-hidden origin-top-right">
                    
                    <div class="fixed inset-0 z-[-1]" @click="showMenu = false"></div>

                    <div class="flex flex-col text-sm font-medium">
                        <button @click="handleAction('view')" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 text-gray-700 text-left border-b border-gray-50 transition-colors">
                            <EyeIcon class="w-4 h-4 text-cyan-500" /> Xem chi tiết
                        </button>
                        
                        <button @click="handleAction('edit')" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 text-gray-700 text-left border-b border-gray-50 transition-colors">
                            <PencilIcon class="w-4 h-4 text-blue-500" /> Chỉnh sửa
                        </button>

                        <button v-if="schedule.is_active" @click="handleAction('pause')" class="flex items-center gap-3 px-4 py-3 hover:bg-yellow-50 text-gray-700 text-left border-b border-gray-50 transition-colors">
                            <PauseIcon class="w-4 h-4 text-yellow-500" /> Tạm dừng
                        </button>
                        <button v-else @click="handleAction('play')" class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 text-gray-700 text-left border-b border-gray-50 transition-colors">
                            <PlayIcon class="w-4 h-4 text-green-500" /> Kích hoạt
                        </button>

                        <button @click="handleAction('delete')" class="flex items-center gap-3 px-4 py-3 hover:bg-red-50 text-red-600 text-left transition-colors">
                            <TrashIcon class="w-4 h-4" /> Xóa lịch
                        </button>
                    </div>
                </div>
            </transition>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { 
    EyeIcon, PencilIcon, PauseIcon, PlayIcon, TrashIcon, DotsVerticalIcon 
} from "@heroicons/vue/solid";

const props = defineProps(['schedule']);
const emit = defineEmits(['view', 'edit', 'pause', 'play', 'delete']);

const showMenu = ref(false);

const toggleMenu = () => {
    showMenu.value = !showMenu.value;
};

const handleAction = (action) => {
    emit(action, props.schedule);
    showMenu.value = false;
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('vi-VN', {day:'2-digit', month:'2-digit'}) : '';
const repeatLabel = (t) => ({'daily':'Hàng ngày','weekly':'Hàng tuần','monthly':'Hàng tháng'}[t] || 'Một lần');
</script>

<style scoped>
/* Hiệu ứng Scale cho menu */
.scale-enter-active, .scale-leave-active {
  transition: transform 0.1s ease, opacity 0.1s ease;
}
.scale-enter-from, .scale-leave-to {
  transform: scale(0.9);
  opacity: 0;
}
</style>