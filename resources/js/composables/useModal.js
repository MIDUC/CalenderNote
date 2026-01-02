import { ref } from 'vue'

/**
 * Composable for modal state management
 */
export function useModal() {
  const isOpen = ref(false)
  const modalType = ref('')
  const modalTitle = ref('')
  const activeItem = ref(null)

  const open = (type, item = null, title = '') => {
    modalType.value = type
    activeItem.value = item
    modalTitle.value = title
    isOpen.value = true
  }

  const close = () => {
    isOpen.value = false
    modalType.value = ''
    modalTitle.value = ''
    activeItem.value = null
  }

  const setType = (type) => {
    modalType.value = type
  }

  const setTitle = (title) => {
    modalTitle.value = title
  }

  const setActiveItem = (item) => {
    activeItem.value = item
  }

  return {
    isOpen,
    modalType,
    modalTitle,
    activeItem,
    open,
    close,
    setType,
    setTitle,
    setActiveItem,
  }
}

