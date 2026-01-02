import api from '../api'
import { API_ENDPOINTS } from '../utils/constants'

/**
 * Note API service
 */
export const noteService = {
  /**
   * Get list of notes with filters and pagination
   */
  list: async (params) => {
    const response = await api.post(API_ENDPOINTS.NOTE.LIST, params)
    return response.data
  },

  /**
   * Get single note by ID
   */
  show: async (id) => {
    const response = await api.get(API_ENDPOINTS.NOTE.SHOW(id))
    return response.data
  },

  /**
   * Create new note
   */
  store: async (data) => {
    const response = await api.post(API_ENDPOINTS.NOTE.STORE, data)
    return response.data
  },

  /**
   * Update note
   */
  update: async (id, data) => {
    const response = await api.put(API_ENDPOINTS.NOTE.UPDATE(id), data)
    return response.data
  },

  /**
   * Delete note
   */
  delete: async (id) => {
    const response = await api.delete(API_ENDPOINTS.NOTE.DELETE(id))
    return response.data
  },
}

