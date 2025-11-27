<template>
  <div v-if="!isAuthPage" class="flex h-screen w-full overflow-x-hidden bg-gray-50 text-gray-800 font-sans relative">
    
    <aside class="hidden md:flex w-64 bg-white border-r shadow-sm p-5 flex-col justify-between h-full fixed left-0 top-0 z-20">
      <div>
        <h1 class="text-2xl font-bold mb-8 flex items-center gap-2 text-blue-600 select-none">
          📋 MySchedule
        </h1>
        <nav class="space-y-2">
          <RouterLink v-for="item in navItems" :key="item.path" :to="item.path"
            class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 hover:bg-blue-50 hover:text-blue-600 group"
            active-class="bg-blue-100 text-blue-700 font-semibold shadow-sm">
            <span class="text-xl group-hover:scale-110 transition-transform">{{ item.icon }}</span>
            <span>{{ item.label }}</span>
          </RouterLink>
        </nav>
      </div>

      <RouterLink to="/profile" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors border border-transparent hover:border-gray-200">
          <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden border border-gray-300">
             <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" alt="User" class="w-full h-full object-cover"/>
          </div>
          <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-gray-700 truncate">User Name</p>
              <p class="text-xs text-gray-400 truncate">Cài đặt</p>
          </div>
      </RouterLink>
    </aside>

    <div v-if="isMenuOpen" 
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden transition-opacity duration-300"
         @click="isMenuOpen = false">
    </div>

    <div class="md:hidden fixed bottom-8 left-1/2 -translate-x-1/2 z-50 flex items-center justify-center">
        
        <div class="absolute inset-0 flex items-center justify-center">
             <RouterLink v-for="(item, index) in navItems" :key="item.path" :to="item.path"
                @click="isMenuOpen = false"
                class="absolute w-12 h-12 bg-white text-blue-600 rounded-full shadow-lg border border-gray-100 flex items-center justify-center transition-all duration-300 ease-out hover:bg-blue-50"
                :class="{
                    'opacity-0 scale-50 translate-y-0 translate-x-0 pointer-events-none': !isMenuOpen,
                    'opacity-100 scale-100 pointer-events-auto': isMenuOpen,
                    'bg-blue-600 text-white ring-2 ring-blue-200': route.path.includes(item.path)
                }"
                :style="isMenuOpen ? getRadialPosition(index, navItems.length) : ''"
             >
                <span class="text-xl">{{ item.icon }}</span>
                
                <span v-if="isMenuOpen" class="absolute -top-8 bg-gray-800 text-white text-[10px] px-2 py-0.5 rounded shadow whitespace-nowrap opacity-0 animate-fade-in delay-100">
                    {{ item.label }}
                </span>
             </RouterLink>
        </div>

        <button @click="isMenuOpen = !isMenuOpen" 
            class="relative w-16 h-16 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 active:scale-90 z-50"
            :class="isMenuOpen ? 'bg-red-500 rotate-45 shadow-red-500/40' : 'bg-blue-600 shadow-blue-600/40'"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white transition-transform duration-300" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>


    <div class="flex-1 flex flex-col h-full md:pl-64 transition-all duration-300 w-full max-w-full">
      
      <header class="p-4 bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-0 z-10 flex justify-between items-center w-full">
        <div class="flex items-center gap-2">
            <span class="md:hidden text-2xl">📋</span> 
            <h3 class="text-lg md:text-xl font-bold text-gray-800">{{ pageTitle }}</h3>
        </div>

        <RouterLink to="/profile" class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gray-100 border border-gray-200 overflow-hidden">
             <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" alt="User" class="w-full h-full object-cover"/>
        </RouterLink>
      </header>

      <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 pb-32 md:pb-8 scroll-smooth bg-gray-50/50 w-full">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>

  </div>

  <router-view v-else />
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isMenuOpen = ref(false) // Trạng thái đóng/mở menu

const isAuthPage = computed(() => route.path === '/login')

const navItems = [
  { path: '/home', label: 'Home', icon: '🏠' },
  { path: '/calendar', label: 'Lịch', icon: '📅' },
  { path: '/tasks', label: 'Task', icon: '🧾' },
  { path: '/notes', label: 'Note', icon: '📝' },
]

const pageTitle = computed(() => {
    if (route.path === '/profile') return 'Tài khoản'
    const item = navItems.find(i => route.path.includes(i.path))
    return item ? item.label : 'My Schedule'
})

// 🔥 Hàm tính toán vị trí icon bung ra hình vòng cung
const getRadialPosition = (index, total) => {
    // Chúng ta muốn các icon rải ra thành hình rẻ quạt phía trên nút
    // Góc từ -150 độ (trái) đến -30 độ (phải) (Hệ tọa độ: 0 là ngang bên phải, -90 là thẳng lên trên)
    
    const radius = 85; // Khoảng cách icon bay ra (px)
    const startAngle = -160; 
    const endAngle = -20;
    
    // Chia đều góc cho các item
    const step = (endAngle - startAngle) / (total - 1);
    const angleInDegrees = startAngle + (step * index);
    
    // Đổi ra Radian để tính sin/cos
    const angleInRadians = angleInDegrees * (Math.PI / 180);

    const x = Math.cos(angleInRadians) * radius;
    const y = Math.sin(angleInRadians) * radius;

    return {
        transform: `translate(${x}px, ${y}px)`
    };
}
</script>

<style scoped>
/* Sửa lỗi scroll ngang */
.overflow-x-hidden {
    overflow-x: hidden;
}

/* Hiệu ứng hiện label */
@keyframes fadeIn {
    to { opacity: 1; }
}
.animate-fade-in {
    animation: fadeIn 0.2s forwards;
}

/* Scrollbar đẹp */
main::-webkit-scrollbar { width: 4px; }
main::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }

/* Transition Fade trang */
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease-out; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>