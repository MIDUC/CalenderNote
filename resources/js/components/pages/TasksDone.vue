<template>
    <MainLayout>
        <h1 class="text-xl font-bold mb-3">Task hoàn thành</h1>

        <ul class="space-y-2">
            <li v-for="task in tasks" :key="task.id" class="bg-green-100 p-3 rounded shadow">
                <p class="font-medium text-green-700">{{ task.title }}</p>
                <p class="text-sm text-gray-500">Hoàn thành lúc {{ task.time }}</p>
            </li>
        </ul>
    </MainLayout>
</template>

<script setup>
import MainLayout from '../layouts/MainLayout.vue'

const tasks = [
    { id: 1, title: 'Thiền 10 phút', time: '06:30' },
    { id: 2, title: 'Đọc sách 20 trang', time: '09:45' },
]
</script>
