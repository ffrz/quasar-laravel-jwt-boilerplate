<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';

const props = defineProps<{
  token: string;
}>();

const $q = useQuasar();
const router = useRouter();
const isLoading = ref(false);
const password = ref('');
const confirmPassword = ref('');

onMounted(() => {
  console.log('Token dari URL:', props.token);
});

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
      message: 'Password berhasil diubah! Silakan login.',
    });
    isLoading.value = false;
    void router.push('/auth/login');
  }, 2000);
}
</script>

<template>
  <q-page class="row items-center justify-evenly bg-grey-2">
    <q-card class="q-pa-md shadow-2" style="width: 400px; max-width: 90vw">
      <q-card-section>
        <div class="text-h5 text-weight-bold">Reset Password Anda</div>
        <div class="text-grey-8 q-mt-sm">Masukkan password baru Anda di bawah ini.</div>
      </q-card-section>

      <q-form @submit.prevent="onSubmit">
        <q-card-section>
          <q-input
            filled
            v-model="password"
            label="Password Baru"
            type="password"
            lazy-rules
            :rules="[(val) => (val && val.length > 5) || 'Password minimal 6 karakter']"
          />
          <q-input
            class="q-mt-md"
            filled
            v-model="confirmPassword"
            label="Konfirmasi Password Baru"
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
            label="Simpan Password Baru"
            type="submit"
            color="primary"
            class="full-width"
            size="lg"
            :loading="isLoading"
            :disable="isLoading"
          />
        </q-card-section>
      </q-form>
    </q-card>
  </q-page>
</template>
