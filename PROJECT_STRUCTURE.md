# Cấu trúc Project

## Tổng quan
Project sử dụng Laravel (Backend) + Vue 3 (Frontend) với kiến trúc tách biệt rõ ràng.

## Cấu trúc thư mục

```
CalenderNote/
├── app/                          # Laravel Backend
│   ├── Http/
│   │   ├── Controllers/         # API Controllers
│   │   ├── Middleware/          # Middleware
│   │   └── Requests/            # Form Requests (Validation)
│   ├── Models/                   # Eloquent Models
│   ├── Repositories/            # Repository Pattern
│   └── Providers/               # Service Providers
│
├── resources/
│   ├── js/                       # Vue 3 Frontend
│   │   ├── api/                  # API Configuration
│   │   │   ├── index.js          # Axios instance
│   │   │   ├── auth.js           # Auth API functions
│   │   │   └── interceptors.js   # Response/Request interceptors
│   │   │
│   │   ├── composables/          # Vue Composables (Reusable Logic)
│   │   │   ├── useFormat.js      # Date/Time formatting
│   │   │   ├── useApi.js         # API call with loading/error
│   │   │   ├── usePagination.js # Pagination logic
│   │   │   ├── useFilters.js     # Filter & Sort logic
│   │   │   ├── useModal.js       # Modal state management
│   │   │   └── index.js          # Export all composables
│   │   │
│   │   ├── components/           # Vue Components
│   │   │   ├── App.vue           # Root component
│   │   │   ├── common/           # Common/Shared components
│   │   │   │   ├── StatusBadge.vue
│   │   │   │   ├── LoadingSpinner.vue
│   │   │   │   └── EmptyState.vue
│   │   │   ├── layouts/         # Layout components
│   │   │   │   ├── FilterBar.vue
│   │   │   │   ├── Pagination.vue
│   │   │   │   └── ...
│   │   │   └── pages/            # Page components
│   │   │       ├── Login.vue
│   │   │       ├── Home.vue
│   │   │       ├── Calendar.vue
│   │   │       ├── Tasks.vue
│   │   │       └── ...
│   │   │
│   │   ├── services/             # API Services
│   │   │   ├── scheduleService.js
│   │   │   ├── taskService.js
│   │   │   ├── noteService.js
│   │   │   └── index.js
│   │   │
│   │   ├── store/                # Pinia Stores
│   │   │   └── auth.js           # Auth store
│   │   │
│   │   ├── utils/                # Utility Functions
│   │   │   ├── constants.js     # App constants
│   │   │   ├── helpers.js       # Helper functions
│   │   │   ├── labels.js        # Label getters
│   │   │   └── index.js
│   │   │
│   │   ├── router/               # Vue Router
│   │   │   └── index.js
│   │   │
│   │   └── app.js                # Main entry point
│   │
│   └── css/
│       └── app.css               # Global styles
│
├── routes/
│   └── api.php                   # API routes
│
└── database/
    ├── migrations/               # Database migrations
    ├── seeders/                  # Database seeders
    └── factories/                # Model factories
```

## Quy tắc tổ chức code

### 1. Composables (`resources/js/composables/`)
- Chứa các logic có thể tái sử dụng
- Tên file bắt đầu với `use` (theo Vue 3 convention)
- Export functions, không export components

**Ví dụ:**
```javascript
// useFormat.js
export function useFormat() {
  const formatDate = (dateStr) => { ... }
  return { formatDate }
}
```

### 2. Services (`resources/js/services/`)
- Chứa các API calls được nhóm theo domain
- Mỗi service tương ứng với một resource (Schedule, Task, Note)
- Sử dụng API instance từ `api/index.js`

**Ví dụ:**
```javascript
// scheduleService.js
export const scheduleService = {
  list: async (params) => { ... },
  store: async (data) => { ... },
}
```

### 3. Utils (`resources/js/utils/`)
- **constants.js**: Tất cả các hằng số (status, routes, endpoints)
- **helpers.js**: Các hàm tiện ích (formatDate, debounce, etc.)
- **labels.js**: Các hàm lấy label (getTaskStatusLabel, etc.)

### 4. Components
- **common/**: Components dùng chung (StatusBadge, LoadingSpinner, EmptyState)
- **layouts/**: Layout components (FilterBar, Pagination)
- **pages/**: Page components (Calendar, Tasks, etc.)

### 5. API (`resources/js/api/`)
- **index.js**: Axios instance với base config
- **auth.js**: Auth-related API functions
- **interceptors.js**: Request/Response interceptors

## Best Practices

### 1. Import paths
```javascript
// ✅ Good - Use relative paths
import { useFormat } from '../composables'
import { scheduleService } from '../services'
import { TASK_STATUS } from '../utils/constants'

// ❌ Bad - Absolute paths (unless configured)
import { useFormat } from '@/composables'
```

### 2. Component structure
```vue
<template>
  <!-- Template -->
</template>

<script setup>
// 1. Imports
import { ref, computed } from 'vue'
import { useFormat } from '../composables'

// 2. Props & Emits
const props = defineProps({ ... })
const emit = defineEmits([...])

// 3. Composables
const { formatDate } = useFormat()

// 4. State
const loading = ref(false)

// 5. Computed
const computedValue = computed(() => { ... })

// 6. Methods
const handleClick = () => { ... }

// 7. Lifecycle
onMounted(() => { ... })
</script>
```

### 3. Naming conventions
- **Components**: PascalCase (Calendar.vue, StatusBadge.vue)
- **Composables**: camelCase với prefix `use` (useFormat.js, useApi.js)
- **Services**: camelCase với suffix `Service` (scheduleService.js)
- **Utils**: camelCase (helpers.js, constants.js)
- **Constants**: UPPER_SNAKE_CASE (TASK_STATUS, API_ENDPOINTS)

### 4. Code organization trong component
1. Template
2. Script setup với thứ tự:
   - Imports
   - Props/Emits
   - Composables
   - State (refs)
   - Computed
   - Methods
   - Lifecycle hooks

## Tài liệu tham khảo

- [Vue 3 Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
- [Pinia Store](https://pinia.vuejs.org/)
- [Vue Router](https://router.vuejs.org/)
- [Axios](https://axios-http.com/)

