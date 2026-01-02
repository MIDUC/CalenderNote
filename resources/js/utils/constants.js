/**
 * Application constants
 */

// Task statuses
export const TASK_STATUS = {
  PENDING: 'pending',
  DONE: 'done',
  FAILED: 'failed',
  BLOCKED: 'blocked',
}

// Task status labels
export const TASK_STATUS_LABELS = {
  [TASK_STATUS.PENDING]: 'Chưa thực hiện',
  [TASK_STATUS.DONE]: 'Hoàn thành',
  [TASK_STATUS.FAILED]: 'Thất bại',
  [TASK_STATUS.BLOCKED]: 'Tạm hoãn',
}

// Task status colors
export const TASK_STATUS_COLORS = {
  [TASK_STATUS.PENDING]: 'bg-gray-500',
  [TASK_STATUS.DONE]: 'bg-green-500',
  [TASK_STATUS.FAILED]: 'bg-red-500',
  [TASK_STATUS.BLOCKED]: 'bg-yellow-500',
}

// Schedule repeat types
export const REPEAT_TYPES = {
  NONE: 'none',
  DAILY: 'daily',
  WEEKLY: 'weekly',
  MONTHLY: 'monthly',
}

// Schedule repeat labels
export const REPEAT_TYPE_LABELS = {
  [REPEAT_TYPES.NONE]: 'Một lần',
  [REPEAT_TYPES.DAILY]: 'Hàng ngày',
  [REPEAT_TYPES.WEEKLY]: 'Hàng tuần',
  [REPEAT_TYPES.MONTHLY]: 'Hàng tháng',
}

// Schedule types
export const SCHEDULE_TYPES = {
  FIXED: 'fixed',
  FLEXIBLE: 'flexible',
}

// Schedule type labels
export const SCHEDULE_TYPE_LABELS = {
  [SCHEDULE_TYPES.FIXED]: 'Cố định',
  [SCHEDULE_TYPES.FLEXIBLE]: 'Linh hoạt',
}

// Days of week
export const DAYS_OF_WEEK = [
  { label: 'Thứ 2', value: 1 },
  { label: 'Thứ 3', value: 2 },
  { label: 'Thứ 4', value: 3 },
  { label: 'Thứ 5', value: 4 },
  { label: 'Thứ 6', value: 5 },
  { label: 'Thứ 7', value: 6 },
  { label: 'Chủ nhật', value: 7 },
]

// Sort directions
export const SORT_DIRECTION = {
  ASC: 'asc',
  DESC: 'desc',
}

// Default pagination
export const DEFAULT_PAGE_SIZE = 10
export const PAGE_SIZE_OPTIONS = [10, 20, 50, 100]

// API endpoints (không có /api prefix vì đã có trong baseURL của axios instance)
export const API_ENDPOINTS = {
  AUTH: {
    LOGIN: '/login',
    LOGOUT: '/logout',
    REGISTER: '/register',
    ME: '/me',
  },
  SCHEDULE: {
    LIST: '/schedule/listing',
    STORE: '/schedule/store',
    SHOW: (id) => `/schedule/show/${id}`,
    UPDATE: (id) => `/schedule/update/${id}`,
    DELETE: (id) => `/schedule/delete/${id}`,
    PLAY: (id) => `/schedule/play/${id}`,
  },
  TASK: {
    LIST: '/task/listing',
    STORE: '/task/store',
    SHOW: (id) => `/task/show/${id}`,
    UPDATE: (id) => `/task/update/${id}`,
    DELETE: (id) => `/task/delete/${id}`,
  },
  NOTE: {
    LIST: '/note/listing',
    STORE: '/note/store',
    SHOW: (id) => `/note/show/${id}`,
    UPDATE: (id) => `/note/update/${id}`,
    DELETE: (id) => `/note/delete/${id}`,
  },
}

// Routes
export const ROUTES = {
  LOGIN: '/login',
  HOME: '/home',
  CALENDAR: '/calendar',
  TASKS: '/tasks',
  TASKS_DONE: '/tasks/done',
  TASKS_FAILED: '/tasks/failed',
  NOTES: '/notes',
}

// Local storage keys
export const STORAGE_KEYS = {
  TOKEN: 'token',
  USER: 'user',
  THEME: 'theme',
  LANGUAGE: 'language',
}

