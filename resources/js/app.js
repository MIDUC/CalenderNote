import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './components/App.vue'
import axios from 'axios'
import '../css/app.css'
import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import { setupInterceptors } from './api/interceptors'

// ⚙️ Cấu hình axios (fallback for direct axios usage)
axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// Setup API interceptors for error handling
setupInterceptors()

// 💡 Gắn axios vào toàn bộ app (optional)
const app = createApp(App)
app.config.globalProperties.$axios = axios

// 📦 Kích hoạt Pinia + Router
const pinia = createPinia()
app.use(pinia)
app.use(router)
const options = {
  // Bạn có thể tùy chỉnh vị trí, thời gian tự động đóng, v.v.
  position: POSITION.TOP_RIGHT, // Hiển thị ở góc trên bên phải
  timeout: 3000, // Tự động đóng sau 3 giây
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  icon: true,
};
app.use(Toast, options)
// 🚀 Mount Vue
app.mount('#app')
