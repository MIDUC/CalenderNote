import {
  REPEAT_TYPE_LABELS,
  SCHEDULE_TYPE_LABELS,
  TASK_STATUS_LABELS,
} from './constants'

/**
 * Get repeat type label
 */
export const getRepeatLabel = (type, days = null) => {
  if (type === 'weekly' && days) {
    if (Array.isArray(days)) {
      return `Hàng tuần (${days.join(', ')})`
    }
    return `Hàng tuần`
  }
  return REPEAT_TYPE_LABELS[type] || 'Không xác định'
}

/**
 * Get schedule type label
 */
export const getScheduleTypeLabel = (type) => {
  return SCHEDULE_TYPE_LABELS[type] || 'Không xác định'
}

/**
 * Get task status label
 */
export const getTaskStatusLabel = (status) => {
  return TASK_STATUS_LABELS[status] || 'Không xác định'
}

/**
 * Get task status color class
 */
export const getTaskStatusColor = (status) => {
  const colors = {
    pending: 'bg-gray-500',
    done: 'bg-green-500',
    failed: 'bg-red-500',
    blocked: 'bg-yellow-500',
  }
  return colors[status] || 'bg-gray-500'
}

