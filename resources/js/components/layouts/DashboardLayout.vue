<template>
    <div class="flex min-h-screen bg-gray-100">
        <main class="flex-1 p-4 overflow-y-auto">
            <RouterView />
        </main>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'

const sidebarOpen = ref(false)
const route = useRoute()

const pageTitle = ref('Trang chủ')

watch(
    () => route.name,
    () => {
        const map = {
            home: 'Trang chủ',
            calendar: 'Lịch',
            tasks: 'Task',
            'tasks-done': 'Task đã hoàn thành',
            'tasks-failed': 'Task thất bại',
            notes: 'Ghi chú cá nhân'
        }
        pageTitle.value = map[route.name] || 'Trang'
    },
    { immediate: true }
)
</script>

<style scoped>
.menu-item {
    display: block;
    padding: 10px 12px;
    border-radius: 8px;
    color: #444;
    font-weight: 500;
}

.menu-item.router-link-exact-active {
    background-color: #6366f1;
    color: white;
}
</style>