import { ref } from 'vue'
import api from '../api'

/**
 * Composable for handling API calls with loading and error states
 */
export function useApi() {
  const loading = ref(false)
  const error = ref(null)

  const execute = async (apiCall) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiCall()
      return response
    } catch (err) {
      error.value = err.response?.data || err.message || 'Có lỗi xảy ra'
      throw err
    } finally {
      loading.value = false
    }
  }

  const resetError = () => {
    error.value = null
  }

  return {
    loading,
    error,
    execute,
    resetError,
  }
}

