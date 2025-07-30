<script setup lang="ts">
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';

const $q = useQuasar();
const router = useRouter();
const isLoading = ref(false);
const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');

function onSubmit() {
  if (password.value !== confirmPassword.value) {
    $q.notify({ color: 'negative', message: 'Password tidak cocok!' });
    return;
  }
  isLoading.value = true;
  setTimeout(() => {
    $q.notify({
      color: 'positive',
      icon: 'check_circle',
      message: 'Registrasi berhasil! Silakan login.',
    });
    isLoading.value = false;
    void router.push('/auth/login');
  }, 2000);
}
</script>

<template>
  <q-page class="row items-center justify-evenly bg-grey-2">
    <q-card class="q-pa-md shadow-2" style="width: 400px; max-width: 90vw">
      <q-card-section class="text-center">
        <div class="text-grey-9 text-h5 text-weight-bold">Buat Akun Baru</div>
        <div class="text-grey-8">Isi data di bawah ini untuk mendaftar</div>
      </q-card-section>

      <q-form @submit.prevent="onSubmit">
        <q-card-section>
          <q-input
            filled
            v-model="name"
            label="Nama Lengkap"
            lazy-rules
            :rules="[(val) => !!val || 'Nama tidak boleh kosong']"
          />
          <q-input
            class="q-mt-md"
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
            :rules="[(val) => (val && val.length > 5) || 'Password minimal 6 karakter']"
          />
          <q-input
            class="q-mt-md"
            filled
            v-model="confirmPassword"
            label="Konfirmasi Password"
            type="password"
            lazy-rules
            :rules="[
              (val) => !!val || 'Konfirmasi password tidak boleh kosong',
              (val) => val === password || 'Password tidak cocok',
            ]"
          />
        </q-card-section>

        <q-card-section>
          <q-btn
            label="Registrasi"
            type="submit"
            color="secondary"
            class="full-width"
            size="lg"
            :loading="isLoading"
            :disable="isLoading"
          />
        </q-card-section>
      </q-form>

      <q-card-section class="text-center q-pt-none">
        <span class="text-grey-8">Sudah punya akun? </span>
        <router-link to="/auth/login" class="text-primary" style="text-decoration: none">
          Login di sini
        </router-link>
      </q-card-section>
    </q-card>
  </q-page>
</template>
