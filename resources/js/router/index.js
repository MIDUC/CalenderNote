import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../store/auth'
import Login from '../components/pages/Login.vue'
import SimpleLayout from '../components/layouts/SimpleLayout.vue'
import Home from '../components/pages/Home.vue'
import Calendar from '../components/pages/Calendar.vue'
import Tasks from '../components/pages/Tasks.vue'
import TasksDone from '../components/pages/TasksDone.vue'
import TasksFailed from '../components/pages/TasksFailed.vue'
import Notes from '../components/pages/Notes.vue'
import Shop from '../components/pages/Shop.vue'
import NPCs from '../components/pages/NPCs.vue'
import Quests from '../components/pages/Quests.vue'
import Battles from '../components/pages/Battles.vue'
import Achievements from '../components/pages/Achievements.vue'
import Inventory from '../components/pages/Inventory.vue'
import CharacterStats from '../components/pages/CharacterStats.vue'
import DailyRewards from '../components/pages/DailyRewards.vue'
import Equipment from '../components/pages/Equipment.vue'
import Statistics from '../components/pages/Statistics.vue'
import Leaderboard from '../components/pages/Leaderboard.vue'
import PersonalInfo from '../components/pages/PersonalInfo.vue'
import Exercises from '../components/pages/Exercises.vue'
import Stories from '../components/pages/Stories.vue'
import Characters from '../components/pages/Characters.vue'
import CharacterQuests from '../components/pages/CharacterQuests.vue'
import AdminDashboard from '../components/pages/AdminDashboard.vue'

const routes = [
  { path: '/login', component: Login },
  {
    path: '/',
    component: SimpleLayout,
    children: [
      { 
        path: '', 
        name: 'home', 
        component: Home,
        meta: { pageTitle: 'Trang chủ', showBackButton: false }
      },
      { 
        path: 'home', 
        name: 'home-alt', 
        component: Home,
        meta: { pageTitle: 'Trang chủ', showBackButton: false }
      },
      { 
        path: 'calendar', 
        name: 'calendar', 
        component: Calendar,
        meta: { pageTitle: 'Lịch', showBackButton: true }
      },
      { 
        path: 'tasks', 
        name: 'tasks', 
        component: Tasks,
        meta: { pageTitle: 'Task', showBackButton: true }
      },
      { 
        path: 'tasks/done', 
        name: 'tasks-done', 
        component: TasksDone,
        meta: { pageTitle: 'Hoàn thành', showBackButton: true }
      },
      { 
        path: 'tasks/failed', 
        name: 'tasks-failed', 
        component: TasksFailed,
        meta: { pageTitle: 'Thất bại', showBackButton: true }
      },
      { 
        path: 'notes', 
        name: 'notes', 
        component: Notes,
        meta: { pageTitle: 'Ghi chú', showBackButton: true }
      },
      { 
        path: 'shop', 
        name: 'shop', 
        component: Shop,
        meta: { pageTitle: 'Cửa hàng', showBackButton: true }
      },
      { 
        path: 'npcs', 
        name: 'npcs', 
        component: NPCs,
        meta: { pageTitle: 'NPC', showBackButton: true }
      },
      { 
        path: 'quests', 
        name: 'quests', 
        component: Quests,
        meta: { pageTitle: 'Nhiệm vụ', showBackButton: true }
      },
      { 
        path: 'battles', 
        name: 'battles', 
        component: Battles,
        meta: { pageTitle: 'Chiến đấu', showBackButton: true }
      },
      { 
        path: 'achievements', 
        name: 'achievements', 
        component: Achievements,
        meta: { pageTitle: 'Thành tích', showBackButton: true }
      },
      { 
        path: 'inventory', 
        name: 'inventory', 
        component: Inventory,
        meta: { pageTitle: 'Túi đồ', showBackButton: true }
      },
      { 
        path: 'character', 
        name: 'character', 
        component: CharacterStats,
        meta: { pageTitle: 'Thông số nhân vật', showBackButton: true }
      },
      { 
        path: 'daily-rewards', 
        name: 'daily-rewards', 
        component: DailyRewards,
        meta: { pageTitle: 'Phần thưởng đăng nhập', showBackButton: true }
      },
      { 
        path: 'equipment', 
        name: 'equipment', 
        component: Equipment,
        meta: { pageTitle: 'Trang bị', showBackButton: true }
      },
      { 
        path: 'statistics', 
        name: 'statistics', 
        component: Statistics,
        meta: { pageTitle: 'Thống kê', showBackButton: true }
      },
      { 
        path: 'leaderboard', 
        name: 'leaderboard', 
        component: Leaderboard,
        meta: { pageTitle: 'Bảng xếp hạng', showBackButton: true }
      },
      { 
        path: 'personal-info', 
        name: 'personal-info', 
        component: PersonalInfo,
        meta: { pageTitle: 'Thông tin cá nhân', showBackButton: true }
      },
      { 
        path: 'exercises', 
        name: 'exercises', 
        component: Exercises,
        meta: { pageTitle: 'Luyện tập', showBackButton: true }
      },
      { 
        path: 'stories', 
        name: 'stories', 
        component: Stories,
        meta: { pageTitle: 'Cốt Truyện', showBackButton: true }
      },
      { 
        path: 'characters/story/:storyId', 
        name: 'characters', 
        component: Characters,
        meta: { pageTitle: 'Nhân Vật', showBackButton: true }
      },
      { 
        path: 'character-quests/character/:characterId', 
        name: 'character-quests', 
        component: CharacterQuests,
        meta: { pageTitle: 'Nhiệm Vụ Nhân Vật', showBackButton: true }
      },
      { 
        path: 'admin', 
        name: 'admin', 
        component: AdminDashboard,
        meta: { pageTitle: 'Admin Dashboard', showBackButton: true, requiresAuth: true }
      },
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Kiểm tra đăng nhập trước khi vào trang có meta.requiresAuth
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.token) next('/login')
  else next()
})

export default router
