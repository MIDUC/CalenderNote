<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 mb-4">
    
    <div class="flex justify-between items-center md:hidden">
      <span class="text-sm font-bold text-gray-700">🔍Tìm kiếm</span>
      <button 
        @click="isOpen = !isOpen"
        class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg text-sm font-medium text-gray-700 active:bg-gray-200 transition"
      >
        <span v-if="isOpen">▲ Thu gọn</span>
        <span v-else>▼ Mở bộ lọc</span>
      </button>
    </div>

    <div :class="isOpen ? 'block' : 'hidden'" class="mt-3 md:mt-0 md:block transition-all duration-300">
      <div class="flex flex-col md:flex-row gap-3">
        
        <div class="flex-1 relative">
           <input 
              :value="filters.title"
              @input="updateFilter('title', $event.target.value)"
              type="text" 
              placeholder="Tìm kiếm theo tiêu đề..." 
              class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
           />
           <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
        </div>

        <div class="grid grid-cols-2 md:flex gap-3">
            <select 
              :value="sortBy"
              @change="$emit('updateFilters', { filters, sortBy: $event.target.value, sortDirection })"
              class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer"
            >
              <option v-for="field in sortFields" :key="field.value" :value="field.value">
                {{ field.label }}
              </option>
            </select>

            <select 
              :value="sortDirection"
              @change="$emit('updateFilters', { filters, sortBy, sortDirection: $event.target.value })"
              class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer"
            >
              <option value="desc">Giảm dần</option>
              <option value="asc">Tăng dần</option>
            </select>
            
            <button 
                class="col-span-2 md:col-auto bg-blue-600 hover:bg-blue-700 text-white p-2.5 rounded-lg shadow-md active:scale-95 transition flex justify-center items-center"
                title="Tìm kiếm"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';

// Nhận props từ cha
const props = defineProps({
  filters: Object,
  sortBy: String,
  sortDirection: String,
  sortFields: {
    type: Array,
    default: () => [
        { value: 'created_at', label: 'Mới nhất' },
        { value: 'title', label: 'Tiêu đề' }
    ]
  }
});

const emit = defineEmits(['updateFilters']);

// State quản lý việc đóng mở trên mobile
const isOpen = ref(false);

// Hàm helper để update filter title (có thể thêm debounce nếu muốn)
const updateFilter = (key, value) => {
    emit('updateFilters', {
        filters: { ...props.filters, [key]: value },
        sortBy: props.sortBy,
        sortDirection: props.sortDirection
    });
};
</script>