import { defineStore } from 'pinia';
import { api } from 'boot/axios';
import { computed, ref } from 'vue';

// Definisi tipe User (sesuaikan dengan API Laravel Anda)
interface User {
  id: number;
  name: string;
  email: string;
  // Tambahkan field lain sesuai kebutuhan
}

// Tanggapan dari endpoint login
interface LoginResponse {
  access_token: string;
  token_type: string;
  expires_in: number;
}

// Tanggapan dari register (bisa sama dengan login jika kirim token juga)
interface RegisterResponse {
  user: User;
  token: string;
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(localStorage.getItem('token'));

  const isAuthenticated = computed(() => !!token.value);

  async function login(email: string, password: string) {
    const response = await api.post<LoginResponse>('/login', { email, password });
    token.value = response.data.access_token;
    localStorage.setItem('token', token.value);
    await fetchUser();
  }

  async function register(name: string, email: string, password: string) {
    const response = await api.post<RegisterResponse>('/register', { name, email, password });
    token.value = response.data.token;
    localStorage.setItem('token', token.value);
    user.value = response.data.user;
  }

  async function fetchUser() {
    if (!token.value) return;

    const response = await api.get<User>('/me', {
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    });
    user.value = response.data;
  }

  function logout() {
    token.value = null;
    user.value = null;
    localStorage.removeItem('token');
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    fetchUser,
    logout,
  };
});
