# Frontend Documentation

## Cấu trúc và cách sử dụng

### Composables

#### useFormat
Format dates, times, numbers, currency

```javascript
import { useFormat } from '../composables'

const { formatDate, formatTime, formatCurrency } = useFormat()

const date = formatDate('2024-01-01') // "01/01/2024"
const currency = formatCurrency(1000000) // "1.000.000 ₫"
```

#### useApi
Handle API calls with loading and error states

```javascript
import { useApi } from '../composables'
import { scheduleService } from '../services'

const { loading, error, execute } = useApi()

const loadData = async () => {
  const data = await execute(() => scheduleService.list({ page: 1 }))
  // data is the response
}
```

#### usePagination
Pagination logic

```javascript
import { usePagination } from '../composables'

const {
  page,
  itemPerPage,
  total,
  lastPage,
  setPage,
  setItemPerPage,
  setPaginationData,
} = usePagination(1, 10)

// After API call
setPaginationData(response.data)
```

#### useFilters
Filter and sort logic

```javascript
import { useFilters } from '../composables'

const {
  filters,
  sortBy,
  sortDirection,
  updateFilters,
  updateSort,
  getFilterParams,
} = useFilters({ title: '' }, 'created_at', 'desc')

// Update filters
updateFilters({ title: 'test' })

// Get params for API
const params = {
  ...getFilterParams(),
  page: page.value,
}
```

#### useModal
Modal state management

```javascript
import { useModal } from '../composables'

const {
  isOpen,
  modalType,
  modalTitle,
  activeItem,
  open,
  close,
} = useModal()

// Open modal
open('edit', schedule, 'Chỉnh sửa lịch')

// Close modal
close()
```

### Services

#### scheduleService
```javascript
import { scheduleService } from '../services'

// List schedules
const response = await scheduleService.list({
  filters: { title: 'test' },
  page: 1,
  item_per_page: 10,
})

// Create schedule
await scheduleService.store({
  title: 'New Schedule',
  // ... other fields
})

// Update schedule
await scheduleService.update(id, { title: 'Updated' })

// Delete schedule
await scheduleService.delete(id)

// Play schedule
await scheduleService.play(id)
```

#### taskService
```javascript
import { taskService } from '../services'

// Similar to scheduleService
await taskService.list(params)
await taskService.store(data)
await taskService.update(id, data)
await taskService.delete(id)
```

#### noteService
```javascript
import { noteService } from '../services'

// Similar to scheduleService
await noteService.list(params)
await noteService.store(data)
await noteService.update(id, data)
await noteService.delete(id)
```

### Utils

#### Constants
```javascript
import {
  TASK_STATUS,
  TASK_STATUS_LABELS,
  REPEAT_TYPES,
  API_ENDPOINTS,
  ROUTES,
} from '../utils/constants'

// Use constants
if (task.status === TASK_STATUS.DONE) { ... }
const label = TASK_STATUS_LABELS[TASK_STATUS.DONE]
```

#### Helpers
```javascript
import {
  getTodayDate,
  formatDateForInput,
  debounce,
  deepClone,
} from '../utils/helpers'

const today = getTodayDate() // "2024-01-01"
const cloned = deepClone(object)
```

#### Labels
```javascript
import {
  getRepeatLabel,
  getTaskStatusLabel,
  getTaskStatusColor,
} from '../utils/labels'

const label = getRepeatLabel('weekly', [1, 3, 5])
const statusLabel = getTaskStatusLabel('done')
const colorClass = getTaskStatusColor('done')
```

### Common Components

#### StatusBadge
```vue
<StatusBadge 
  :status="task.status" 
  type="task"
  :show-dot="true"
  :animate="true"
/>
```

#### LoadingSpinner
```vue
<LoadingSpinner 
  message="Đang tải..."
  size="md"
/>
```

#### EmptyState
```vue
<EmptyState
  icon="📭"
  title="Không có dữ liệu"
  message="Chưa có lịch trình nào"
  :show-action="true"
  action-label="Thêm mới"
  @action="handleAdd"
/>
```

## Ví dụ sử dụng trong component

```vue
<template>
  <div>
    <LoadingSpinner v-if="loading" />
    <EmptyState v-else-if="schedules.length === 0" @action="openAddModal" />
    <div v-else>
      <div v-for="schedule in schedules" :key="schedule.id">
        <StatusBadge :status="schedule.is_active" type="schedule" />
        {{ formatDate(schedule.start_date) }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useFormat } from '../composables'
import { useApi } from '../composables'
import { usePagination } from '../composables'
import { useFilters } from '../composables'
import { scheduleService } from '../services'
import LoadingSpinner from '../components/common/LoadingSpinner.vue'
import EmptyState from '../components/common/EmptyState.vue'
import StatusBadge from '../components/common/StatusBadge.vue'

const { formatDate } = useFormat()
const { loading, execute } = useApi()
const { page, itemPerPage, setPaginationData } = usePagination()
const { filters, getFilterParams } = useFilters()

const schedules = ref([])

const loadSchedules = async () => {
  const response = await execute(() => 
    scheduleService.list({
      ...getFilterParams(),
      page: page.value,
      item_per_page: itemPerPage.value,
    })
  )
  
  schedules.value = response.data?.data || []
  setPaginationData(response.data)
}

onMounted(loadSchedules)
</script>
```

