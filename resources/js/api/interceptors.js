import api from './index'
import router from '../router'
import { STORAGE_KEYS } from '../utils/constants'

// Import toast dynamically to avoid circular dependencies
let toast = null

/**
 * Get toast instance (lazy load)
 */
const getToast = () => {
  if (!toast) {
    // Dynamic import to avoid issues
    try {
      const { useToast } = require('vue-toastification')
      toast = useToast()
    } catch (e) {
      // Fallback if toast is not available
      console.error('Toast not available:', e)
      return {
        error: (msg) => console.error(msg),
        success: (msg) => console.log(msg),
        info: (msg) => console.info(msg),
        warning: (msg) => console.warn(msg),
      }
    }
  }
  return toast
}

/**
 * Setup API interceptors for error handling and token refresh
 */
export function setupInterceptors() {
  // Response interceptor
  api.interceptors.response.use(
    (response) => {
      return response
    },
    (error) => {
      const toastInstance = getToast()
      
      // Handle 401 Unauthorized
      if (error.response?.status === 401) {
        localStorage.removeItem(STORAGE_KEYS.TOKEN)
        localStorage.removeItem(STORAGE_KEYS.USER)
        router.push('/login')
        toastInstance.error('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.')
        return Promise.reject(error)
      }

      // Handle 403 Forbidden
      if (error.response?.status === 403) {
        toastInstance.error('Bạn không có quyền thực hiện hành động này.')
        return Promise.reject(error)
      }

      // Handle 404 Not Found
      if (error.response?.status === 404) {
        toastInstance.error('Không tìm thấy dữ liệu.')
        return Promise.reject(error)
      }

      // Handle 422 Validation Error
      if (error.response?.status === 422) {
        const message = error.response.data?.message || 'Dữ liệu không hợp lệ.'
        toastInstance.error(message)
        return Promise.reject(error)
      }

      // Handle 500 Server Error
      if (error.response?.status >= 500) {
        toastInstance.error('Lỗi máy chủ. Vui lòng thử lại sau.')
        return Promise.reject(error)
      }

      // Handle network errors
      if (!error.response) {
        toastInstance.error('Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.')
        return Promise.reject(error)
      }

      // Default error message
      const message = error.response.data?.message || error.message || 'Có lỗi xảy ra.'
      toastInstance.error(message)
      
      return Promise.reject(error)
    }
  )
}

