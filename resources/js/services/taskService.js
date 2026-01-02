import api from '../api'
import { API_ENDPOINTS } from '../utils/constants'

/**
 * Task API service
 */
export const taskService = {
  /**
   * Get list of tasks with filters and pagination
   */
  list: async (params) => {
    const response = await api.post(API_ENDPOINTS.TASK.LIST, params)
    return response.data
  },

  /**
   * Get single task by ID
   */
  show: async (id) => {
    const response = await api.get(API_ENDPOINTS.TASK.SHOW(id))
    return response.data
  },

  /**
   * Create new task
   */
  store: async (data) => {
    const response = await api.post(API_ENDPOINTS.TASK.STORE, data)
    return response.data
  },

  /**
   * Update task
   */
  update: async (id, data) => {
    const response = await api.put(API_ENDPOINTS.TASK.UPDATE(id), data)
    return response.data
  },

  /**
   * Delete task
   */
  delete: async (id) => {
    const response = await api.delete(API_ENDPOINTS.TASK.DELETE(id))
    return response.data
  },
}

