<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-indigo-200">
        <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Đăng nhập</h2>
            <form @submit.prevent="submit" class="space-y-4">
                <input v-model="email" type="email" placeholder="Email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                <input v-model="password" type="password" placeholder="Mật khẩu" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                <button type="submit"
                    class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                    Đăng nhập
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../store/auth'

const email = ref('')
const password = ref('')
const auth = useAuthStore()

const submit = async () => {
    await auth.login({ email: email.value, password: password.value })
}
</script>
