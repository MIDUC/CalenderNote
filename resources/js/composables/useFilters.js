import { ref, watch } from 'vue'

/**
 * Composable for filter and sort logic
 */
export function useFilters(initialFilters = {}, initialSortBy = 'created_at', initialSortDirection = 'desc') {
  const filters = ref({ ...initialFilters })
  const sortBy = ref(initialSortBy)
  const sortDirection = ref(initialSortDirection)

  const updateFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters }
  }

  const updateSort = (field, direction = null) => {
    sortBy.value = field
    if (direction) {
      sortDirection.value = direction
    }
  }

  const toggleSortDirection = () => {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  }

  const resetFilters = () => {
    filters.value = { ...initialFilters }
    sortBy.value = initialSortBy
    sortDirection.value = initialSortDirection
  }

  const getFilterParams = () => ({
    filters: filters.value,
    sort_by: [sortBy.value],
    sort_direction: [sortDirection.value],
  })

  return {
    filters,
    sortBy,
    sortDirection,
    updateFilters,
    updateSort,
    toggleSortDirection,
    resetFilters,
    getFilterParams,
  }
}

