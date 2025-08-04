<script setup lang="ts">
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';

const API_ENDPOINT = import.meta.env.VITE_API_ENDPOINT || 'http://localhost:8000';
const $q = useQuasar();
const router = useRouter();
const isLoading = ref(false);
const email = ref('');
const password = ref('');

async function onSubmit() {
  isLoading.value = true;

  try {
    const response = await fetch(API_ENDPOINT + '/api/v1/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    });

    const data = await response.json();

    if (response.ok) {
      const accessToken = data.data.access_token;

      if (accessToken) {
        localStorage.setItem('auth_token', accessToken);

        $q.notify({
          color: 'positive',
          icon: 'check_circle',
          message: data.message || 'Login berhasil!',
        });

        void router.push('/admin/dashboard');
      } else {
        throw new Error('Respons login tidak mengandung access_token.');
      }
    } else {
      const errorMessage = data.message || data.error || `Error: ${response.statusText}`; 
      $q.notify({
        color: 'negative',
        message: errorMessage,
      });
    }
  } catch (error: any) {
    console.error('Login error:', error);
    $q.notify({
      color: 'negative',
      message: 'Gagal terhubung ke server. Periksa koneksi internet Anda.',
    });
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <q-layout view="hHh lpR fff" style="background: #f5f5f5">
    <q-header elevated class="bg-secondary text-white">
      <q-toolbar style="max-width: 980px; margin: 0 auto">
        <q-toolbar-title align="center">
          <q-avatar>
            <img src="https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg" />
          </q-avatar>
          <a class="q-pl-md text-white">StaterPos</a>
        </q-toolbar-title>
      </q-toolbar>
    </q-header>
    <q-page-container style="max-width: 1024px; margin: 20px auto 0">
      <slot></slot>
    </q-page-container>
    <q-page class="row items-center justify-evenly bg-grey-2">
      <q-card class="q-pa-md shadow-2" style="width: 400px; max-width: 90vw">
        <q-card-section class="text-center">
          <div class="text-grey-9 text-h5 text-weight-bold">Selamat Datang</div>
          <div class="text-grey-8">Silakan login untuk melanjutkan</div>
        </q-card-section>

        <q-form @submit.prevent="onSubmit">
          <q-card-section>
            <q-input
              filled
              v-model="email"
              label="Email"
              type="email"
              lazy-rules
              :rules="[(val) => !!val || 'Email tidak boleh kosong']"
            />
            <q-input
              class="q-mt-md"
              filled
              v-model="password"
              label="Password"
              type="password"
              lazy-rules
              :rules="[(val) => !!val || 'Password tidak boleh kosong']"
            />
          </q-card-section>

          <q-card-section class="row items-center justify-end q-pt-none">
            <router-link
              to="/auth/forgot-password"
              class="text-secondary"
              style="text-decoration: none"
            >
              Lupa Password?
            </router-link>
          </q-card-section>

          <q-card-section>
            <q-btn
              label="Login"
              type="submit"
              color="secondary"
              icon="login"
              class="full-width"
              size="lg"
              :loading="isLoading"
              :disable="isLoading"
            />
          </q-card-section>
        </q-form>

        <q-card-section class="text-center q-pt-none">
          <span class="text-grey-8">Belum punya akun? </span>
          <router-link to="/auth/register" class="text-secondary" style="text-decoration: none">
            Registrasi di sini
          </router-link>
        </q-card-section>
      </q-card>
    </q-page>
    <q-footer class="transparent">
      <div class="q-mt-lg q-mb-md justify-center items-center flex text-grey-6">
        <span>&copy; 2025 - StaterPos v1.0.0</span>
      </div>
    </q-footer>
  </q-layout>
</template>
