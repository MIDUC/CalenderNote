<template>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="relative flex-1">
            <input 
                v-model="searchValue" 
                @keyup.enter="applyFilters" 
                type="text"
                placeholder="Tìm kiếm theo tiêu đề..." 
                class="w-full border border-gray-300 rounded-xl px-4 py-3 pl-11 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white/80 backdrop-blur-sm" />
            <SearchIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        </div>

        <select v-model="localSortBy" 
            class="border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white/80 backdrop-blur-sm min-w-[140px]">
            <option v-for="field in sortFields" :key="field.value" :value="field.value">
                {{ field.label }}
            </option>
        </select>

        <select v-model="localSortDirection" 
            class="border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white/80 backdrop-blur-sm min-w-[120px]">
            <option value="desc">Giảm dần</option>
            <option value="asc">Tăng dần</option>
        </select>

        <button @click="applyFilters" 
            class="bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-200 hover:scale-105 flex items-center justify-center gap-2 min-w-[120px]">
            <SearchIcon class="w-5 h-5" />
            <span class="hidden sm:inline">Tìm kiếm</span>
        </button>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { SearchIcon} from '@heroicons/vue/solid'
const props = defineProps({
    filters: Object,
    sortBy: String,
    sortDirection: String,
    sortFields: {
        type: Array,
        default: () => [
            { value: 'created_at', label: 'Ngày tạo' }
        ],
    },
})

const emits = defineEmits(['updateFilters'])

const localFilters = ref({ ...props.filters })
const localSortBy = ref(props.sortBy)
const localSortDirection = ref(props.sortDirection)

// Computed property để xử lý cả keyword và title
const searchValue = computed({
    get: () => localFilters.value.keyword || localFilters.value.title || '',
    set: (value) => {
        if (localFilters.value.hasOwnProperty('keyword')) {
            localFilters.value.keyword = value
        } else if (localFilters.value.hasOwnProperty('title')) {
            localFilters.value.title = value
        } else {
            localFilters.value.title = value
        }
    }
})

watch(
    () => [props.filters, props.sortBy, props.sortDirection],
    () => {
        localFilters.value = { ...props.filters }
        localSortBy.value = props.sortBy
        localSortDirection.value = props.sortDirection
    }
)

const applyFilters = () => {
    emits('updateFilters', {
        filters: localFilters.value,
        sortBy: localSortBy.value,
        sortDirection: localSortDirection.value,
    })
}
</script>
