<template>
    <div class="p-4 max-w-md mx-auto pt-10">
        
        <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center text-center border border-gray-100">
            <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center text-4xl mb-4 border-4 border-white shadow-md">
                👤
                </div>
            <h2 class="text-xl font-bold text-gray-800">Người dùng</h2>
            <p class="text-gray-500">user@example.com</p>
            
            <div class="mt-6 w-full space-y-3">
                <button class="w-full py-3 px-4 bg-gray-50 hover:bg-gray-100 rounded-xl flex items-center justify-between text-gray-700 font-medium transition">
                    <span>⚙️ Cài đặt chung</span>
                    <span>›</span>
                </button>
                <button class="w-full py-3 px-4 bg-gray-50 hover:bg-gray-100 rounded-xl flex items-center justify-between text-gray-700 font-medium transition">
                    <span>🔒 Đổi mật khẩu</span>
                    <span>›</span>
                </button>
            </div>
        </div>

        <div class="mt-8">
            <button @click="handleLogout" 
                class="w-full bg-red-50 text-red-600 font-bold py-4 rounded-xl shadow-sm border border-red-100 hover:bg-red-100 active:scale-95 transition-all flex items-center justify-center gap-2">
                🚪 Đăng xuất tài khoản
            </button>
            <p class="text-center text-xs text-gray-400 mt-4">Phiên bản 1.0.0</p>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useToast } from "vue-toastification";

const router = useRouter()
const toast = useToast()

const handleLogout = async () => {
    if(!confirm("Bạn chắc chắn muốn đăng xuất?")) return;

    const token = localStorage.getItem('token')
    try {
        await axios.post(`${import.meta.env.VITE_API_BASE_URL}/api/logout`, {}, {
             headers: { Authorization: `Bearer ${token}` }
        })
    } catch (e) {
        console.log(e)
    } finally {
        localStorage.removeItem('token')
        toast.info("Đã đăng xuất")
        router.push('/login')
    }
}
</script>