import { defineStore } from 'pinia';
import api from '@/lib/axios';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

export const useAuthStore = defineStore('auth', () => {
    const user = ref<any>(null);
    const isAuthenticated = computed(() => !!user.value);
    const router = useRouter();

    async function login(credentials: any) {
        try {
            // await api.get('/sanctum/csrf-cookie');
            const response = await api.post('/login', credentials);
            user.value = response.data.user;
            // Store token in localStorage if needed, or rely on cookie if using that mode. 
            // Since we implemented 'createToken', we expect a token response.
            if (response.data.access_token) {
                localStorage.setItem('token', response.data.access_token);
                // Set default header
                api.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
            }
            return true;
        } catch (error) {
            console.error('Login failed', error);
            throw error;
        }
    }

    async function logout() {
        try {
            await api.post('/logout');
        } catch (e) {
            // ignore
        } finally {
            user.value = null;
            localStorage.removeItem('token');
            delete api.defaults.headers.common['Authorization'];
            // router.push({ name: 'login' }); // handle in component
        }
    }

    // Try to restore session
    async function checkAuth() {
        const token = localStorage.getItem('token');
        if (token) {
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            try {
                const response = await api.get('/user');
                user.value = response.data;
            } catch (e) {
                logout();
            }
        }
    }

    return { user, isAuthenticated, login, logout, checkAuth };
});
