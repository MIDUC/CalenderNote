<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-indigo-200 p-4">
        <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Đăng nhập</h2>
            
            <!-- 🔥 KHUNG THÔNG BÁO LỖI CHÍNH -->
            <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm flex items-start gap-2">
                <span class="mt-0.5">⚠️</span>
                <span>{{ errorMessage }}</span>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input v-model="email" type="email" placeholder="admin@example.com" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                    <input v-model="password" type="password" placeholder="••••••" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none transition" />
                </div>

                <button type="submit" 
                    :disabled="loading"
                    class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition flex justify-center items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span v-if="loading" class="animate-spin h-5 w-5 border-2 border-white rounded-full border-t-transparent"></span>
                    <span v-else>Đăng nhập</span>
                </button>
            </form>

            <!-- 🛠️ PHẦN DEBUG CHO MOBILE (Chỉ hiện khi có lỗi) -->
            <div v-if="debugInfo" class="mt-6 border-t pt-4">
                <p class="text-xs font-bold text-gray-500 mb-2">🔍 THÔNG TIN KỸ THUẬT (DEBUG):</p>
                <div class="bg-gray-100 p-3 rounded text-[10px] font-mono text-gray-700 overflow-x-auto whitespace-pre-wrap border border-gray-300">
                    {{ debugInfo }}
                </div>
                <p class="text-[10px] text-gray-400 mt-1 italic">Chụp màn hình này gửi cho dev nếu cần hỗ trợ.</p>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../store/auth'
import { useRouter } from 'vue-router'
import { useToast } from "vue-toastification";
import "vue-toastification/dist/index.css";

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const debugInfo = ref('') // Biến chứa log debug
const loading = ref(false)

const auth = useAuthStore()
const router = useRouter()
const toast = useToast()

const submit = async () => {
    loading.value = true;
    errorMessage.value = '';
    debugInfo.value = ''; // Reset debug info

    try {
        await auth.login({ 
            email: email.value, 
            password: password.value 
        });
        
        toast.success("Đăng nhập thành công! 🎉");
        router.push('/home');

    } catch (error) {
        console.error("Lỗi đăng nhập:", error);
        
        let userMsg = "Đã có lỗi xảy ra.";
        let debugMsg = "";

        // 1. Phân tích lỗi để hiển thị cho User
        if (error.response) {
            userMsg = error.response.data?.message || "Email hoặc mật khẩu không chính xác.";
            debugMsg += `[HTTP Error] Status: ${error.response.status}\n`;
            debugMsg += `Data: ${JSON.stringify(error.response.data, null, 2)}`;
        } else if (error.request) {
            userMsg = "Không thể kết nối đến Server. Vui lòng kiểm tra Wifi/4G.";
            debugMsg += `[Network Error] Không nhận được phản hồi từ Server.\n`;
            debugMsg += `URL: ${error.config?.baseURL || ''}${error.config?.url}\n`;
            debugMsg += `Kiểm tra xem điện thoại và máy tính có chung mạng Wifi không?`;
        } else {
            userMsg = "Lỗi ứng dụng: " + error.message;
            debugMsg += `[App Error] ${error.message}`;
        }

        // Thêm thông tin Config để check IP
        if (error.config) {
            debugMsg += `\n\n--- Config ---\n`;
            debugMsg += `Method: ${error.config.method}\n`;
            debugMsg += `URL đầy đủ: ${error.config.baseURL ? error.config.baseURL + error.config.url : error.config.url}\n`;
        }

        errorMessage.value = userMsg;
        debugInfo.value = debugMsg; // Hiển thị chi tiết ra màn hình
        toast.error(userMsg);
    } finally {
        loading.value = false;
    }
}
</script>