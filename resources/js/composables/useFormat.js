import { computed } from 'vue'

/**
 * Composable for formatting dates and times
 */
export function useFormat() {
  const formatDate = (dateStr, includeTime = false) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    
    if (includeTime) {
      return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    }
    
    return date.toLocaleDateString('vi-VN', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    })
  }

  const formatTime = (timeStr) => {
    if (!timeStr) return ''
    return timeStr
  }

  const formatDateTime = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleString('vi-VN', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  }

  const formatCurrency = (amount, currency = 'VND') => {
    if (amount === null || amount === undefined) return '0'
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: currency,
    }).format(amount)
  }

  const formatNumber = (number) => {
    if (number === null || number === undefined) return '0'
    return new Intl.NumberFormat('vi-VN').format(number)
  }

  return {
    formatDate,
    formatTime,
    formatDateTime,
    formatCurrency,
    formatNumber,
  }
}

