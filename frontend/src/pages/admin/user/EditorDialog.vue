<script setup lang="ts">
import { ref, watch } from 'vue';
import { useQuasar } from 'quasar';

const props = defineProps<{
  isOpen: boolean;
  editMode: boolean;
  formData: {
    id: number | null;
    name: string;
    email: string;
    status: 'Aktif' | 'Nonaktif';
  };
}>();

const emit = defineEmits<{
  (e: 'submit', user: typeof props.formData): void;
  (e: 'cancel'): void;
}>();

const $q = useQuasar();

const localData = ref({ ...props.formData });

watch(
  () => props.formData,
  (newVal) => {
    localData.value = { ...newVal };
  },
  { deep: true, immediate: true },
);

function submitForm() {
  emit('submit', localData.value);
}

function cancelForm() {
  emit('cancel');
}
</script>

<template>
  <q-dialog v-model="props.isOpen" persistent>
    <q-card style="min-width: 400px">
      <q-card-section>
        <div class="text-h6">
          {{ props.editMode ? 'Edit Pengguna' : 'Tambah Pengguna' }}
        </div>
      </q-card-section>

      <q-card-section>
        <q-input v-model="localData.name" label="Nama" />
        <q-input v-model="localData.email" label="Email" />
        <q-select v-model="localData.status" label="Status" :options="['Aktif', 'Nonaktif']" />
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="Batal" color="grey" @click="cancelForm" />
        <q-btn flat label="Simpan" color="primary" @click="submitForm" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
