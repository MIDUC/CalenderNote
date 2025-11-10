<template>
    <MainLayout>
        <h1 class="text-xl font-bold mb-3">Công việc hôm nay</h1>

        <ul class="space-y-2">
            <li v-for="task in tasks" :key="task.id" class="bg-white p-3 rounded shadow">
                <p class="font-medium">{{ task.title }}</p>
                <p class="text-sm text-gray-500">{{ task.time }}</p>
            </li>
        </ul>
    </MainLayout>
</template>

<script setup>
import MainLayout from '../layouts/MainLayout.vue'

const tasks = [
    { id: 1, title: 'Chạy bộ 5km', time: '06:00' },
    { id: 2, title: 'Đọc sách 30 phút', time: '08:00' },
    { id: 3, title: 'Tập core', time: '17:00' },
]
</script>