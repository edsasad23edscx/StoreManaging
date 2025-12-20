import { ref } from 'vue'

interface Notification {
  id: string
  type: 'success' | 'error' | 'info' | 'warning'
  message: string
  details?: string
  duration?: number
}

const currentNotification = ref<Notification | null>(null)

export const useNotification = () => {
  const show = (
    type: Notification['type'],
    message: string,
    details?: string,
    duration?: number,
  ) => {
    currentNotification.value = {
      id: Date.now().toString(),
      type,
      message,
      details,
      duration: duration || (type === 'error' ? 7000 : 5000),
    }
  }

  const success = (message: string, details?: string) => show('success', message, details)
  const error = (message: string, details?: string) => show('error', message, details)
  const warning = (message: string, details?: string) => show('warning', message, details)
  const info = (message: string, details?: string) => show('info', message, details)

  const clear = () => {
    currentNotification.value = null
  }

  return { currentNotification, show, success, error, warning, info, clear }
}
