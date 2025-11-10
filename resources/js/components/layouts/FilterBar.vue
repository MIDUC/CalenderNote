<template>
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <input v-model="localFilters.keyword" @keyup.enter="applyFilters" type="text"
            placeholder="Tìm kiếm theo tiêu đề..." class="border rounded px-3 py-2 w-64" />

        <select v-model="localSortBy" class="border rounded px-3 py-2">
            <option v-for="field in sortFields" :key="field.value" :value="field.value">
                {{ field.label }}
            </option>
        </select>

        <select v-model="localSortDirection" class="border rounded px-3 py-2">
            <option value="desc">Giảm dần</option>
            <option value="asc">Tăng dần</option>
        </select>

        <button @click="applyFilters" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 transition">
            Lọc
        </button>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

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
