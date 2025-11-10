<template>
    <nav class="bg-white border-t shadow-inner flex justify-around py-2">
        <router-link to="/" class="flex flex-col items-center text-sm" active-class="text-blue-500">
            🏠 <span>Trang chủ</span>
        </router-link>
        <router-link to="/calendar" class="flex flex-col items-center text-sm" active-class="text-blue-500">
            📅 <span>Lịch</span>
        </router-link>
        <router-link to="/task" class="flex flex-col items-center text-sm" active-class="text-blue-500">
            ✅ <span>Task</span>
        </router-link>
        <router-link to="/done" class="flex flex-col items-center text-sm" active-class="text-blue-500">
            🏁 <span>Hoàn thành</span>
        </router-link>
        <router-link to="/failed" class="flex flex-col items-center text-sm" active-class="text-blue-500">
            ❌ <span>Thất bại</span>
        </router-link>
        <router-link to="/notes" class="flex flex-col items-center text-sm" active-class="text-blue-500">
            📝 <span>Ghi chú</span>
        </router-link>
    </nav>
</template>
