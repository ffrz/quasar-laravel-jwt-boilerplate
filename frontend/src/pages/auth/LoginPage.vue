<script setup lang="ts">
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';

const $q = useQuasar();
const router = useRouter();

const isLoading = ref(false);
const email = ref('');
const password = ref('');

function onSubmit() {
  isLoading.value = true;
  setTimeout(() => {
    $q.notify({
      color: 'positive',
      icon: 'check_circle',
      message: `Login berhasil!`,
    });
    localStorage.setItem('authToken', 'dummy-token-for-testing');

    isLoading.value = false;
    void router.push('/admin/dashboard');
  }, 2000);
}
</script>

<template>
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
            class="text-primary"
            style="text-decoration: none"
          >
            Lupa Password?
          </router-link>
        </q-card-section>

        <q-card-section>
          <q-btn
            label="Login"
            type="submit"
            color="primary"
            class="full-width"
            size="lg"
            :loading="isLoading"
            :disable="isLoading"
          />
        </q-card-section>
      </q-form>

      <q-card-section class="text-center q-pt-none">
        <span class="text-grey-8">Belum punya akun? </span>
        <router-link to="/auth/register" class="text-primary" style="text-decoration: none">
          Registrasi di sini
        </router-link>
      </q-card-section>
    </q-card>
  </q-page>
</template>
