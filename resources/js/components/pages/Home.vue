<template>

    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2 ...">
            <div class="mb-8">
                <button class="bg-cyan-500 shadow-lg shadow-cyan-500/50 rounded-full">
                    <div class="p-4 text-white font-medium">
                        Thêm việc cần làm
                    </div>
                </button>
            </div>
            <h1 class=" text-xl font-bold mb-3">Công việc hôm nay</h1>
            <ul class="space-y-2">
                <li v-for="task in tasks" :key="task.id" class="bg-white p-3 rounded shadow">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2 ...">
                            <p class="font-medium">{{ task.title }}</p>
                            <p class="text-sm text-gray-500">{{ task.time }}</p>
                        </div>
                        <div class="...">
                            <div class="grid grid-cols-3 gap-4">
                                <button class="bg-cyan-500 shadow-lg shadow-cyan-500/50 rounded-full">
                                    <div class="p-2 text-white font-medium">
                                        Chi tiết
                                    </div>
                                </button>
                                <button class="bg-green-500 shadow-lg shadow-cyan-500/50 rounded-full">
                                    <div class="p-2 text-white font-medium">
                                        Hoàn thành
                                    </div>
                                </button>
                                <button class="bg-red-500 shadow-lg shadow-cyan-500/50 rounded-full">
                                    <div class="p-2 text-white font-medium">
                                        Thất bại
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="...">
            <div>
                <button class="bg-indigo-500 shadow-lg shadow-cyan-500/50 rounded-full">
                    <div class="p-4 text-white font-medium">
                        Thêm ghi chú
                    </div>
                </button>
            </div>
        </div>
    </div>



</template>

<script setup>

const tasks = [
    { id: 1, title: 'Chạy bộ 5km', time: '06:00' },
    { id: 2, title: 'Đọc sách 30 phút', time: '08:00' },
    { id: 3, title: 'Tập core', time: '17:00' },
]
</script>