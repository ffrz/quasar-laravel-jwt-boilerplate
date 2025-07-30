<script setup lang="ts">
import { ref } from 'vue';
import { useQuasar } from 'quasar';

const $q = useQuasar();
const isLoading = ref(false);
const email = ref('');

function onSubmit() {
  isLoading.value = true;
  setTimeout(() => {
    $q.notify({
      color: 'positive',
      icon: 'check_circle',
      message: `Jika email terdaftar, link reset telah dikirim ke ${email.value}`,
    });
    isLoading.value = false;
  }, 2000);
}
</script>

<template>
  <q-page class="row items-center justify-evenly bg-grey-2">
    <q-card class="q-pa-md shadow-2" style="width: 400px; max-width: 90vw">
      <q-card-section>
        <div class="text-h5 text-weight-bold">Lupa Password</div>
        <div class="text-grey-8 q-mt-sm">
          Masukkan email Anda dan kami akan mengirimkan link untuk me-reset password Anda.
        </div>
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
        </q-card-section>

        <q-card-section>
          <q-btn
            label="Kirim Link Reset"
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
        <router-link to="/auth/login" class="text-primary" style="text-decoration: none">
          Kembali ke Login
        </router-link>
      </q-card-section>
    </q-card>
  </q-page>
</template>
