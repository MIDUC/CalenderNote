<template>
    <MainLayout>
        <h1 class="text-xl font-bold mb-3">Ghi chú cá nhân</h1>

        <div class="space-y-3">
            <div v-for="note in notes" :key="note.id" class="bg-white p-3 rounded shadow">
                <p class="font-semibold">{{ note.title }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ note.content }}</p>
            </div>
        </div>

        <button class="mt-4 w-full bg-blue-500 text-white py-2 rounded shadow">+ Thêm ghi chú</button>
    </MainLayout>
</template>

<script setup>
import MainLayout from '../layouts/MainLayout.vue'

const notes = [
    { id: 1, title: 'Ngày 2/11', content: 'Tập tốt, nên duy trì chạy sáng.' },
    { id: 2, title: 'Ngày 3/11', content: 'Hôm nay hơi mệt, giảm khối lượng tập.' },
]
</script>
