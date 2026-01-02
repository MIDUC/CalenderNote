import { ref, computed } from 'vue'

/**
 * Composable for pagination logic
 */
export function usePagination(initialPage = 1, initialItemPerPage = 10) {
  const page = ref(initialPage)
  const itemPerPage = ref(initialItemPerPage)
  const total = ref(0)
  const lastPage = ref(1)

  const currentPage = computed(() => page.value)
  const totalPages = computed(() => lastPage.value)
  const totalItems = computed(() => total.value)
  const itemsPerPage = computed(() => itemPerPage.value)

  const setPage = (newPage) => {
    if (newPage >= 1 && newPage <= lastPage.value) {
      page.value = newPage
    }
  }

  const setItemPerPage = (newSize) => {
    itemPerPage.value = newSize
    page.value = 1 // Reset to first page when changing page size
  }

  const setPaginationData = (data) => {
    total.value = data?.total || 0
    lastPage.value = data?.last_page || 1
    page.value = data?.current_page || page.value
  }

  const nextPage = () => {
    if (page.value < lastPage.value) {
      page.value++
    }
  }

  const prevPage = () => {
    if (page.value > 1) {
      page.value--
    }
  }

  const goToFirstPage = () => {
    page.value = 1
  }

  const goToLastPage = () => {
    page.value = lastPage.value
  }

  const hasNextPage = computed(() => page.value < lastPage.value)
  const hasPrevPage = computed(() => page.value > 1)

  return {
    // State
    page,
    itemPerPage,
    total,
    lastPage,
    
    // Computed
    currentPage,
    totalPages,
    totalItems,
    itemsPerPage,
    hasNextPage,
    hasPrevPage,
    
    // Methods
    setPage,
    setItemPerPage,
    setPaginationData,
    nextPage,
    prevPage,
    goToFirstPage,
    goToLastPage,
  }
}

