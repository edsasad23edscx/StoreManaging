import { ref, watch, onMounted } from 'vue'

type Theme = 'light' | 'dark'

const theme = ref<Theme>('light')

const STORAGE_KEY = 'theme-preference'

export function useTheme() {
    const initTheme = () => {
        // Check localStorage first
        const savedTheme = localStorage.getItem(STORAGE_KEY) as Theme | null

        if (savedTheme) {
            theme.value = savedTheme
        } else {
            // Check system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
            theme.value = prefersDark ? 'dark' : 'light'
        }

        applyTheme()
    }

    const applyTheme = () => {
        const root = document.documentElement

        if (theme.value === 'dark') {
            root.classList.add('dark')
        } else {
            root.classList.remove('dark')
        }
    }

    const toggleTheme = () => {
        theme.value = theme.value === 'light' ? 'dark' : 'light'
        localStorage.setItem(STORAGE_KEY, theme.value)
        applyTheme()
    }

    const setTheme = (newTheme: Theme) => {
        theme.value = newTheme
        localStorage.setItem(STORAGE_KEY, newTheme)
        applyTheme()
    }

    // Watch for system preference changes
    const watchSystemPreference = () => {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Only auto-update if user hasn't set a preference
            if (!localStorage.getItem(STORAGE_KEY)) {
                theme.value = e.matches ? 'dark' : 'light'
                applyTheme()
            }
        })
    }

    onMounted(() => {
        initTheme()
        watchSystemPreference()
    })

    return {
        theme,
        toggleTheme,
        setTheme,
        initTheme,
    }
}
