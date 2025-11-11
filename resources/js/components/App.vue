<template>
  <!-- Nếu KHÔNG phải trang login => hiển thị layout dashboard -->
  <div v-if="!isAuthPage" class="flex h-screen bg-gray-50 text-gray-800">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r shadow-sm p-5 flex flex-col justify-between transition-all duration-300"
      v-if="showSidebar">
      <div>
        <h1 class="text-2xl font-bold mb-8 flex items-center gap-2 text-blue-600">
          📋 MySchedule
        </h1>

        <nav class="space-y-2">
          <RouterLink v-for="(item, path) in navItems" :key="path" :to="path"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-blue-50 hover:text-blue-600"
            active-class="bg-blue-100 text-blue-700 font-semibold">
            <span class="text-lg">{{ item.icon }}</span>
            <span>{{ item.label }}</span>
          </RouterLink>
        </nav>
      </div>

      <button @click="logout"
        class="flex items-center justify-center gap-2 bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600 transition-colors duration-200 shadow-sm">
        🚪 Đăng xuất
      </button>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="p-1 bg-white border-b flex justify-between items-center shadow-sm sticky top-0 z-10">
        <button @click="toggleSidebar" class="p-2 rounded-lg hover:bg-gray-100 transition" title="Ẩn/Hiện menu">
          ☰
        </button>
        <h3 class="text-xl font-semibold text-blue-700">{{ pageTitle }}</h3>
        <div></div>
      </header>

      <!-- Nội dung trang -->
      <main class="p-4 overflow-y-auto bg-gray-50 flex-1">
        <router-view />
      </main>
    </div>
  </div>

  <!-- Nếu là trang login -->
  <router-view v-else />
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const showSidebar = ref(true)
const toggleSidebar = () => (showSidebar.value = !showSidebar.value)

// Xác định có đang ở trang login không
const isAuthPage = computed(() => route.path === '/login')

// Map navigation
const navItems = {
  '/': { label: 'Trang chủ', icon: '🏠' },
  '/calendar': { label: 'Lịch', icon: '📅' },
  '/tasks': { label: 'Task', icon: '🧾' },
  '/completed': { label: 'Hoàn thành', icon: '✅' },
  '/failed': { label: 'Thất bại', icon: '❌' },
  '/notes': { label: 'Ghi chú', icon: '📝' },
}

const pageTitle = computed(() => navItems[route.path]?.label || 'Trang')

const logout = async () => {
  const token = localStorage.getItem('token')
  if (!token) return router.push('/login')

  try {
    await axios.post('http://127.0.0.1:8000/api/logout', null, {
      headers: { Authorization: `Bearer ${token}` },
    })
  } catch (error) {
    console.warn('Logout API error:', error.response?.data || error)
  } finally {
    localStorage.removeItem('token')
    router.push('/login')
  }
}
</script>

<style scoped>
.router-link-active {
  background-color: #dbeafe;
  /* blue-100 */
  color: #1d4ed8;
  /* blue-700 */
  font-weight: 600;
}
</style>
