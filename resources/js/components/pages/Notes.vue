<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Ghi chú cá nhân
            </h1>
            <button 
                @click="openModal('add')"
                class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-200 hover:scale-105 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Thêm ghi chú</span>
            </button>
        </div>

        <LoadingSpinner v-if="loading" />
        <EmptyState 
            v-else-if="notes.length === 0"
            icon="📝"
            title="Chưa có ghi chú nào"
            message="Hãy thêm ghi chú mới để bắt đầu!"
            :show-action="true"
            action-label="Thêm ghi chú"
            @action="openModal('add')"
        />
        <div v-else class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="note in notes" :key="note.id" 
                    class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100/50 p-5 group">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-bold text-lg text-gray-800 group-hover:text-indigo-600 transition-colors">
                            {{ note.title }}
                        </h3>
                        <div class="flex gap-2">
                            <button @click="openModal('edit', note)"
                                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="handleDelete(note.id)"
                                class="p-2 rounded-lg bg-red-100 hover:bg-red-200 transition-colors">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 line-clamp-3">{{ note.content }}</p>
                    <div class="mt-3 pt-3 border-t border-gray-200 text-xs text-gray-400">
                        {{ formatDate(note.created_at, true) }}
                    </div>
                </div>
            </div>

            <!-- Phân trang -->
            <Pagination 
                v-if="!loading && total > 0" 
                :currentPage="page" 
                :totalPages="lastPage" 
                :totalItems="total"
                :itemPerPage="itemPerPage" 
                @page-changed="loadNotes" 
                @item-per-page-changed="handleItemPerPageChange" 
            />
        </div>
    </div>

    <!-- Modal -->
    <transition name="modal">
        <div v-if="showModal" 
            @click.self="closeModal"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-3xl p-6 lg:p-8 w-full max-w-2xl shadow-2xl relative overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        {{ modalTitle }}
                    </h2>
                    <button @click="closeModal" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <input v-model="form.title" type="text" placeholder="Tiêu đề ghi chú" 
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required />
                    <textarea v-model="form.content" placeholder="Nội dung ghi chú" rows="8"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="closeModal" 
                            class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-all">
                            Hủy
                        </button>
                        <button type="submit" 
                            class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold shadow-lg hover:shadow-xl transition-all">
                            {{ modalType === 'add' ? 'Thêm mới' : 'Cập nhật' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { useFormat } from '../../composables'
import { usePagination } from '../../composables'
import { noteService } from '../../services'
import LoadingSpinner from '../common/LoadingSpinner.vue'
import EmptyState from '../common/EmptyState.vue'
import Pagination from '../layouts/Pagination.vue'

const toast = useToast()
const { formatDate } = useFormat()

const notes = ref([])
const loading = ref(true)

// Pagination
const {
    page,
    itemPerPage,
    total,
    lastPage,
    setPaginationData,
    setItemPerPage,
} = usePagination(1, 12)

// Modal
const showModal = ref(false)
const modalType = ref('')
const modalTitle = ref('')
const activeNote = ref(null)
const form = ref({
    title: '',
    content: '',
})

const loadNotes = async (newPage = 1) => {
    page.value = newPage
    loading.value = true
    try {
        const res = await noteService.list({
            filters: {},
            sort_by: ['created_at'],
            sort_direction: ['desc'],
            page: page.value,
            item_per_page: itemPerPage.value,
        })
        const data = res?.data
        notes.value = data?.data || []
        setPaginationData(data)
    } catch (error) {
        console.error('Lỗi tải notes:', error)
        toast.error('Không thể tải danh sách ghi chú')
    } finally {
        loading.value = false
    }
}

const handleItemPerPageChange = (newSize) => {
    setItemPerPage(newSize)
    loadNotes(1)
}

const openModal = (type, note = null) => {
    modalType.value = type
    activeNote.value = note
    if (type === 'add') {
        modalTitle.value = 'Thêm ghi chú mới'
        form.value = { title: '', content: '' }
    } else if (type === 'edit') {
        modalTitle.value = 'Chỉnh sửa ghi chú'
        form.value = { title: note.title, content: note.content }
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    modalType.value = ''
    activeNote.value = null
    form.value = { title: '', content: '' }
}

const handleSubmit = async () => {
    try {
        if (modalType.value === 'add') {
            await noteService.store(form.value)
            toast.success('Đã thêm ghi chú thành công!')
        } else if (modalType.value === 'edit') {
            await noteService.update(activeNote.value.id, form.value)
            toast.success('Đã cập nhật ghi chú thành công!')
        }
        closeModal()
        await loadNotes()
    } catch (error) {
        toast.error('Không thể lưu ghi chú')
    }
}

const handleDelete = async (id) => {
    if (!confirm('Bạn có chắc muốn xóa ghi chú này?')) return
    
    try {
        await noteService.delete(id)
        toast.success('Đã xóa ghi chú thành công!')
        await loadNotes()
    } catch (error) {
        toast.error('Không thể xóa ghi chú')
    }
}

onMounted(loadNotes)
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.95) translateY(-10px);
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
