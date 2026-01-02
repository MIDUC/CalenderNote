import api from '../api'
import { API_ENDPOINTS } from '../utils/constants'

/**
 * Schedule API service
 */
export const scheduleService = {
  /**
   * Get list of schedules with filters and pagination
   */
  list: async (params) => {
    const response = await api.post(API_ENDPOINTS.SCHEDULE.LIST, params)
    return response.data
  },

  /**
   * Get single schedule by ID
   */
  show: async (id) => {
    const response = await api.get(API_ENDPOINTS.SCHEDULE.SHOW(id))
    return response.data
  },

  /**
   * Create new schedule
   */
  store: async (data) => {
    const response = await api.post(API_ENDPOINTS.SCHEDULE.STORE, data)
    return response.data
  },

  /**
   * Update schedule
   */
  update: async (id, data) => {
    const response = await api.put(API_ENDPOINTS.SCHEDULE.UPDATE(id), data)
    return response.data
  },

  /**
   * Delete schedule
   */
  delete: async (id) => {
    const response = await api.delete(API_ENDPOINTS.SCHEDULE.DELETE(id))
    return response.data
  },

  /**
   * Play/Activate schedule
   */
  play: async (id) => {
    const response = await api.post(API_ENDPOINTS.SCHEDULE.PLAY(id))
    return response.data
  },
}

