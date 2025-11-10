<template>
    <MainLayout>
        <h1 class="text-xl font-bold mb-3">Task thất bại</h1>

        <ul class="space-y-2">
            <li v-for="task in tasks" :key="task.id" class="bg-red-100 p-3 rounded shadow">
                <p class="font-medium text-red-700">{{ task.title }}</p>
                <p class="text-sm text-gray-500">Bỏ lỡ lúc {{ task.time }}</p>
            </li>
        </ul>
    </MainLayout>
</template>

<script setup>
import MainLayout from '../layouts/MainLayout.vue'

const tasks = [
    { id: 1, title: 'Tập core', time: '17:00' },
    { id: 2, title: 'Viết ghi chú buổi tối', time: '21:00' },
]
</script>
