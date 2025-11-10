import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './components/App.vue'
import axios from 'axios'
import '../css/app.css';
// ⚙️ Cấu hình axios
axios.defaults.baseURL = 'http://127.0.0.1:8000'
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// 💡 Gắn axios vào toàn bộ app (optional)
const app = createApp(App)
app.config.globalProperties.$axios = axios

// 📦 Kích hoạt Pinia + Router
const pinia = createPinia()
app.use(pinia)
app.use(router)

// 🚀 Mount Vue
app.mount('#app')
