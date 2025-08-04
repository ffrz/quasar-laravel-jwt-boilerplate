<script setup lang="ts">
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import type { QTableProps } from 'quasar';
import EditorDialog from './EditorDialog.vue';

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
const formData = ref<User>({
  id: null,
  name: '',
  email: '',
  status: 'Aktif',
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

function onSubmit(user: User) {
  if (editMode.value && user.id != null) {
    const index = users.value.findIndex((u) => u.id === user.id);
    if (index !== -1) {
      users.value[index] = { ...user };
      $q.notify({ color: 'positive', message: 'Data pengguna diperbarui' });
    }
  } else {
    const newId = Math.max(...users.value.map((u) => u.id)) + 1;
    users.value.push({ ...user, id: newId });
    $q.notify({ color: 'positive', message: 'Pengguna baru ditambahkan' });
  }
  isDialogOpen.value = false;
}

function onCancel() {
  isDialogOpen.value = false;
}

function confirmDelete(user: User) {
  $q.dialog({
    title: 'Konfirmasi Hapus',
    message: `Yakin ingin hapus "${user.name}"?`,
    cancel: true,
    persistent: true,
  }).onOk(() => {
    const index = users.value.findIndex((u) => u.id === user.id);
    if (index !== -1) {
      users.value.splice(index, 1);
      $q.notify({ color: 'positive', message: 'Pengguna dihapus' });
    }
  });
}
</script>

<template>
  <div class="row items-center justify-between">
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
  <EditorDialog
    v-model:is-open="isDialogOpen"
    :form-data="formData"
    :edit-mode="editMode"
    @submit="onSubmit"
    @cancel="onCancel"
  />
</template>
