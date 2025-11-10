import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../store/auth'
import Login from '../components/pages/Login.vue'
import Dashboard from '../components/pages/Dashboard.vue'
import DashboardLayout from '../components/layouts/DashboardLayout.vue'
import Home from '../components/pages/Home.vue'
import Calendar from '../components/pages/Calendar.vue'
import Tasks from '../components/pages/Tasks.vue'
import TasksDone from '../components/pages/TasksDone.vue'
import TasksFailed from '../components/pages/TasksFailed.vue'
import Notes from '../components/pages/Notes.vue'

const routes = [
  { path: '/login', component: Login },
  { 
    path: '/dashboard', 
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  { 
    path: '/home', 
    component: Home,
    meta: { requiresAuth: true }
  },
  {
   path: '/',
    component: DashboardLayout,
    children: [
      { path: '', name: 'home', component: Home },
      { path: 'calendar', name: 'calendar', component: Calendar },
      { path: 'tasks', name: 'tasks', component: Tasks },
      { path: 'tasks/done', name: 'tasks-done', component: TasksDone },
      { path: 'tasks/failed', name: 'tasks-failed', component: TasksFailed },
      { path: 'notes', name: 'notes', component: Notes },
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
