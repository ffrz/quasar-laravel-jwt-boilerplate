<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue';
import type { QTableProps, QTableColumn } from 'quasar';
import { useQuasar } from 'quasar';
import { useRouter } from 'vue-router';

// --- INTERFACE & TIPE DATA ---
interface ProductCategory {
  id: number;
  name: string;
  description: string;
}

interface Pagination {
  sortBy: string;
  descending: boolean;
  page: number;
  rowsPerPage: number;
  rowsNumber: number;
}

// --- SETUP DASAR ---
const title = 'Kategori Produk';
const $q = useQuasar();
const router = useRouter();

// --- STATE MANAGEMENT ---
const showFilter = ref<boolean>(false);
const rows = ref<ProductCategory[]>([]);
const loading = ref<boolean>(true);
const filter = reactive({
  search: '',
});

const pagination = ref<Pagination>({
  sortBy: 'name',
  descending: false,
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0, // Awalnya 0, akan diupdate oleh fetchItems
});

const columns: QTableColumn[] = [
  { name: 'name', label: 'Nama Kategori', field: 'name', align: 'left', sortable: true },
  { name: 'description', label: 'Catatan', field: 'description', align: 'left' },
];

// --- SIMULASI BACKEND & DATA ---
// Mock database untuk simulasi
const MOCK_DATABASE: ProductCategory[] = Array.from({ length: 35 }, (_, i) => ({
  id: i + 1,
  name: `Kategori Produk ${i + 1}`,
  description: `Ini adalah deskripsi untuk kategori produk nomor ${i + 1}.`,
}));

/**
 * (SIMULASI) Mengambil data dari 'server' dengan filter, sort, dan paginasi.
 * Menggantikan handleFetchItems dari helper.
 */
const fetchItems = (props?: { pagination: Pagination }) => {
  loading.value = true;

  const newPagination = props?.pagination ?? pagination.value;

  // Simulasi network delay
  setTimeout(() => {
    let items = [...MOCK_DATABASE];

    // 1. Simulasi Filter (Pencarian)
    if (filter.search) {
      const searchTerm = filter.search.toLowerCase();
      items = items.filter(
        (item) =>
          item.name.toLowerCase().includes(searchTerm) ||
          item.description.toLowerCase().includes(searchTerm),
      );
    }

    // 2. Simulasi Sorting
    const { sortBy, descending } = newPagination;
    if (sortBy) {
      const sortFn = (a: ProductCategory, b: ProductCategory) => {
        const valA = (a as any)[sortBy] ?? '';
        const valB = (b as any)[sortBy] ?? '';
        return (valA > valB ? 1 : valA < valB ? -1 : 0) * (descending ? -1 : 1);
      };
      items.sort(sortFn);
    }

    // 3. Update jumlah total baris setelah filter
    pagination.value.rowsNumber = items.length;

    // 4. Simulasi Paginasi
    const { page, rowsPerPage } = newPagination;
    if (rowsPerPage > 0) {
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      items = items.slice(start, end);
    }

    // Update state
    rows.value = items;
    pagination.value.page = newPagination.page;
    pagination.value.rowsPerPage = newPagination.rowsPerPage;
    pagination.value.sortBy = newPagination.sortBy;
    pagination.value.descending = newPagination.descending;

    loading.value = false;
  }, 500); // delay 0.5 detik
};

/**
 * (SIMULASI) Menghapus item. Menggantikan handleDelete dari helper.
 */
const deleteItem = (row: ProductCategory) => {
  $q.dialog({
    title: 'Konfirmasi Hapus',
    message: `Apakah Anda yakin ingin menghapus kategori "${row.name}"?`,
    persistent: true,
    ok: {
      label: 'Hapus',
      color: 'negative',
      flat: true,
    },
    cancel: {
      label: 'Batal',
      flat: true,
    },
  }).onOk(() => {
    loading.value = true;
    const index = MOCK_DATABASE.findIndex((item) => item.id === row.id);
    if (index > -1) {
      MOCK_DATABASE.splice(index, 1);
      $q.notify({ type: 'positive', message: `Kategori "${row.name}" berhasil dihapus.` });
      fetchItems(); // Refresh tabel
    } else {
      $q.notify({ type: 'negative', message: 'Gagal menghapus: item tidak ditemukan.' });
      loading.value = false;
    }
  });
};

/**
 * (SIMULASI) Pengecekan role. Menggantikan check_role dari helper.
 */
const check_role = (role: string) => {
  // Untuk simulasi, kita anggap user adalah admin
  console.log(`Mengecek role: ${role}`);
  return true;
};

// --- COMPUTED & LIFECYCLE ---
const computedColumns = computed(() => {
  if ($q.screen.gt.sm) return columns;
  // Di layar kecil, hanya tampilkan kolom 'name' dan 'action'
  return columns.filter((col) => col.name === 'name' || col.name === 'action');
});

onMounted(() => {
  fetchItems();
});
</script>

<template>
  <AuthenticatedLayout>
    <template #title>{{ title }}</template>

    <template #right-button>
      <q-btn icon="add" dense color="primary" @click="router.push('/admin/product-category/add')" />
      <q-btn
        class="q-ml-sm"
        :icon="!showFilter ? 'filter_alt' : 'filter_alt_off'"
        color="grey"
        dense
        @click="showFilter = !showFilter"
      />
    </template>

    <template #header v-if="showFilter">
      <q-toolbar class="filter-bar">
        <div class="row q-col-gutter-xs items-center q-pa-sm full-width">
          <q-input
            class="col"
            outlined
            dense
            debounce="300"
            v-model="filter.search"
            placeholder="Cari berdasarkan nama atau deskripsi..."
            clearable
            @update:model-value="fetchItems()"
          >
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </div>
      </q-toolbar>
    </template>

    <div class="q-pa-sm">
      <q-table
        class="full-height-table"
        flat
        bordered
        square
        color="primary"
        row-key="id"
        virtual-scroll
        v-model:pagination="pagination"
        :loading="loading"
        :columns="computedColumns"
        :rows="rows"
        :rows-per-page-options="[10, 25, 50]"
        @request="fetchItems as QTableProps['onRequest']"
        binary-state-sort
      >
        <template v-slot:loading>
          <q-inner-loading showing color="primary" />
        </template>

        <template v-slot:no-data="{ message }">
          <div class="full-width row flex-center text-grey-8 q-gutter-sm q-pa-lg">
            <q-icon size="2em" name="sentiment_dissatisfied" />
            <span>{{ message }}</span>
          </div>
        </template>

        <template v-slot:body="props">
          <q-tr :props="props">
            <q-td key="name" :props="props" class="wrap-column">
              <span class="text-bold">{{ props.row.name }}</span>
              <template v-if="!$q.screen.gt.sm && props.row.description">
                <div class="text-grey-8 text-caption">
                  <q-icon name="notes" size="xs" class="q-mr-xs" /> {{ props.row.description }}
                </div>
              </template>
            </q-td>

            <q-td key="description" :props="props" class="wrap-column">
              {{ props.row.description }}
            </q-td>

            <q-td key="action" :props="props">
              <div class="flex justify-end no-wrap">
                <q-btn
                  icon="edit"
                  dense
                  flat
                  round
                  color="primary"
                  @click="router.push(`/admin/product-category/edit/${props.row.id}`)"
                />
                <q-btn
                  icon="content_copy"
                  dense
                  flat
                  round
                  color="grey-7"
                  @click="router.push(`/admin/product-category/duplicate/${props.row.id}`)"
                />
                <q-btn
                  :disabled="!check_role('ADMIN')"
                  icon="delete"
                  dense
                  flat
                  round
                  color="negative"
                  @click="deleteItem(props.row)"
                />
              </div>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.filter-bar {
  background-color: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
}
.wrap-column {
  white-space: normal;
}
</style>
