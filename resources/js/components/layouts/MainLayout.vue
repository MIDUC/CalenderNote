// Không dùn cái này đâu
<template>
  <div v-if="!isAuthPage" class="flex h-screen">
    <!-- Sidebar -->
    <aside
      class="w-64 bg-gray-100 border-r p-3 flex flex-col justify-between"
      v-if="showSidebar"
    >
      <div>
        <h1 class="text-xl font-bold mb-4 flex items-center gap-2">📋 MySchedule</h1>

        <nav class="space-y-2">
          <RouterLink to="/" class="flex items-center gap-2 hover:text-blue-600"
            >🏠 Trang chủ</RouterLink
          >
          <RouterLink to="/calendar" class="flex items-center gap-2 hover:text-blue-600"
            >📅 Lịch</RouterLink
          >
          <RouterLink to="/tasks" class="flex items-center gap-2 hover:text-blue-600"
            >🧾 Task</RouterLink
          >
          <RouterLink to="/completed" class="flex items-center gap-2 hover:text-blue-600"
            >✅ Hoàn thành
          </RouterLink>
          <RouterLink to="/failed" class="flex items-center gap-2 hover:text-blue-600"
            >❌ Thất bại</RouterLink
          >
          <RouterLink to="/notes" class="flex items-center gap-2 hover:text-blue-600"
            >📝 Ghi chú</RouterLink
          >
        </nav>
      </div>

      <button
        @click="logout"
        class="mt-4 bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600"
      >
        Đăng xuất
      </button>
    </aside>

     <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <header class="p-2 bg-gray-50 border-b flex justify-between items-center">
        <button @click="toggleSidebar" class="p-3 bg-gray-200 rounded hover:bg-gray-300">
          ☰
        </button>
        <h3 class="text-lg font-semibold">{{ pageTitle }}</h3>
      </header>
      <main class="p-1 overflow-y-auto">
        <router-view />
      </main>
    </div> 
  </div>

  <!-- Nếu là trang login thì chỉ hiển thị nội dung của login -->
  <router-view v-else />
</template>

<script setup>
import { ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const showSidebar = ref(true);
const toggleSidebar = () => (showSidebar.value = !showSidebar.value);

// xác định có phải trang đăng nhập không
const isAuthPage = computed(() => route.path === "/login");

const pageTitle = computed(() => {
  const titles = {
    "/": "Trang chủ",
    "/calendar": "Lịch",
    "/tasks": "Task",
    "/completed": "Hoàn thành",
    "/failed": "Thất bại",
    "/notes": "Ghi chú",
  };
  return titles[route.path] || "Trang";
});

const logout = async () => {
  const token = localStorage.getItem("token");
  if (!token) return router.push("/login");

  try {
    await axios.post("http://127.0.0.1:8000/api/logout", null, {
      headers: { Authorization: `Bearer ${token}` },
    });
  } catch (error) {
    console.warn("Logout API error:", error.response?.data || error);
  } finally {
    localStorage.removeItem("token");
    router.push("/login");
  }
};
</script>
