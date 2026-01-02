<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <!-- Header với nút quay lại - Ẩn khi ở trang Home -->
        <header v-if="!isHomePage" class="bg-white/80 backdrop-blur-lg border-b border-gray-200/50 shadow-sm sticky top-0 z-30">
            <div class="flex items-center gap-4 px-4 py-3">
                <button
                    v-if="showBackButton"
                    @click="goBack"
                    class="p-2 rounded-xl hover:bg-gray-100 transition"
                    title="Quay lại"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button>
                <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent flex-1">
                    {{ pageTitle }}
                </h1>
                <button
                    @click="logout"
                    class="p-2 rounded-xl hover:bg-gray-100 transition"
                    title="Đăng xuất"
                >
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-4 lg:p-6">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../api'

const route = useRoute()
const router = useRouter()

const pageTitle = computed(() => route.meta?.pageTitle || 'Trang')
const showBackButton = computed(() => route.meta?.showBackButton !== false && route.path !== '/' && route.path !== '/home')
const isHomePage = computed(() => route.path === '/' || route.path === '/home')

const goBack = () => {
    if (window.history.length > 1) {
        router.back()
    } else {
        router.push('/home')
    }
}

const logout = async () => {
    const token = localStorage.getItem('token')
    if (!token) return router.push('/login')

    try {
        await api.post('/logout')
    } catch (error) {
        console.warn('Logout API error:', error.response?.data || error)
    } finally {
        localStorage.removeItem('token')
        router.push('/login')
    }
}
</script>

