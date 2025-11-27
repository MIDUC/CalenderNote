import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './components/App.vue'
import axios from 'axios'
import '../css/app.css';
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

// ⚙️ CẤU HÌNH API URL THÔNG MINH (Smart Config)
// Logic: 
// 1. Kiểm tra xem trong file .env có biến VITE_API_BASE_URL không.
// 2. Nếu CÓ: Dùng cái trong .env (Thường dùng cho Production, ví dụ: https://api.myweb.com)
// 3. Nếu KHÔNG: Tự động lấy IP của máy đang truy cập và ghép với port 8000 (Dùng cho Dev/LAN)

const envUrl = import.meta.env.VITE_API_BASE_URL;
const dynamicUrl = `http://${window.location.hostname}:8000`;

axios.defaults.apiBaseURL = envUrl || dynamicUrl;

// Debug để bạn biết nó đang dùng cái nào
console.log(`🔌 API connected to: ${axios.defaults.baseURL}`);

// Config Interceptors
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token')
    if (token) config.headers.Authorization = `Bearer ${token}`
    return config
})

// 💡 Gắn axios vào toàn bộ app
const app = createApp(App)
app.config.globalProperties.$axios = axios

// 📦 Kích hoạt Pinia + Router
const pinia = createPinia()
app.use(pinia)
app.use(router)

// Config Toast
const options = {
    position: POSITION.TOP_RIGHT,
    timeout: 3000,
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