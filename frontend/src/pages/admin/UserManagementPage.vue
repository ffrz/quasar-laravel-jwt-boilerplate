<script setup lang="ts">
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import type { QTableProps } from 'quasar';
interface User {
  id: number;
  name: string;
  email: string;
  status: 'Aktif' | 'Nonaktif';
}

const $q = useQuasar();

const columns: QTableProps['columns'] = [
  { name: 'id', label: 'ID', align: 'left', field: 'id', sortable: true },
  { name: 'name', label: 'Nama', align: 'left', field: 'name', sortable: true },
  { name: 'email', label: 'Email', align: 'left', field: 'email' },
  { name: 'status', label: 'Status', align: 'center', field: 'status' },
  { name: 'actions', label: 'Aksi', align: 'right', field: '' },
];

const users = ref<User[]>([
  { id: 1, name: 'Budi Santoso', email: 'budi.s@example.com', status: 'Aktif' },
  { id: 2, name: 'Citra Lestari', email: 'citra.l@example.com', status: 'Aktif' },
  { id: 3, name: 'Agus Wijaya', email: 'agus.w@example.com', status: 'Nonaktif' },
  { id: 4, name: 'Dewi Anggraini', email: 'dewi.a@example.com', status: 'Aktif' },
]);

const isDialogOpen = ref(false);
const editMode = ref(false);
const formData = ref({
  id: null as number | null,
  name: '',
  email: '',
  status: 'Aktif' as 'Aktif' | 'Nonaktif',
});

function resetForm() {
  formData.value = { id: null, name: '', email: '', status: 'Aktif' };
}

function openAddDialog() {
  editMode.value = false;
  resetForm();
  isDialogOpen.value = true;
}

function openEditDialog(user: User) {
  editMode.value = true;
  formData.value = { ...user };
  isDialogOpen.value = true;
}

function onSubmit() {
  if (editMode.value && formData.value.id) {
    const index = users.value.findIndex((u) => u.id === formData.value.id);
    if (index !== -1) {
      users.value[index] = { ...formData.value, id: formData.value.id };
    }
    $q.notify({ color: 'positive', message: 'Data pengguna berhasil diperbarui' });
  } else {
    const newId = Math.max(...users.value.map((u) => u.id)) + 1;
    users.value.push({
      name: formData.value.name,
      email: formData.value.email,
      status: formData.value.status,
      id: newId,
    });
    $q.notify({ color: 'positive', message: 'Pengguna baru berhasil ditambahkan' });
  }
  isDialogOpen.value = false;
}

function confirmDelete(user: User) {
  $q.dialog({
    title: 'Konfirmasi Hapus',
    message: `Apakah Anda yakin ingin menghapus pengguna "${user.name}"?`,
    cancel: true,
    persistent: true,
  }).onOk(() => {
    const index = users.value.findIndex((u) => u.id === user.id);
    if (index !== -1) {
      users.value.splice(index, 1);
    }
    $q.notify({ color: 'positive', message: 'Pengguna berhasil dihapus' });
  });
}
</script>

<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="col-auto">
        <div class="text-h4 text-weight-medium">Manajemen Pengguna</div>
        <div class="text-subtitle1 text-grey-7">Tambah, ubah, atau hapus data pengguna.</div>
      </div>
      <div class="col-auto">
        <q-btn color="primary" icon="add" label="Tambah Pengguna" @click="openAddDialog" />
      </div>
    </div>

    <q-card flat bordered>
      <q-table :rows="users" :columns="columns" row-key="id" flat>
        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <q-btn dense round flat icon="edit" @click="openEditDialog(props.row)"></q-btn>
            <q-btn
              dense
              round
              flat
              icon="delete"
              color="negative"
              @click="confirmDelete(props.row)"
            ></q-btn>
          </q-td>
        </template>
        <template v-slot:body-cell-status="props">
          <q-td :props="props">
            <q-badge :color="props.value === 'Aktif' ? 'positive' : 'negative'">
              {{ props.value }}
            </q-badge>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <q-dialog v-model="isDialogOpen">
      <q-card style="width: 500px; max-width: 90vw">
        <q-card-section>
          <div class="text-h6">{{ editMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</div>
        </q-card-section>

        <q-form @submit.prevent="onSubmit">
          <q-card-section class="q-gutter-y-md">
            <q-input
              v-model="formData.name"
              label="Nama"
              outlined
              :rules="[(val) => !!val || 'Nama tidak boleh kosong']"
            />
            <q-input
              v-model="formData.email"
              label="Email"
              type="email"
              outlined
              :rules="[(val) => !!val || 'Email tidak boleh kosong']"
            />
            <q-select
              v-model="formData.status"
              :options="['Aktif', 'Nonaktif']"
              label="Status"
              outlined
            />
          </q-card-section>

          <q-card-actions align="right" class="q-pa-md">
            <q-btn flat label="Batal" v-close-popup />
            <q-btn
              type="submit"
              color="primary"
              :label="editMode ? 'Simpan Perubahan' : 'Simpan'"
            />
          </q-card-actions>
        </q-form>
      </q-card>
    </q-dialog>
  </q-page>
</template>
