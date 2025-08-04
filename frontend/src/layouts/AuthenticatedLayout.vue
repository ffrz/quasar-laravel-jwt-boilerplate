<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useQuasar } from 'quasar';
import { useRoute } from 'vue-router';

// --- SETUP DASAR ---
const $q = useQuasar();
const route = useRoute(); // Untuk mendapatkan info route saat ini (misal: path URL)
// const router = useRouter(); // Untuk melakukan navigasi programatik (misal: logout)

// --- STATE MANAGEMENT UNTUK LAYOUT ---
const LEFT_DRAWER_STORAGE_KEY = 'amanah-pos.layout.left-drawer-open';
const leftDrawerOpen = ref<boolean>(
  JSON.parse(localStorage.getItem(LEFT_DRAWER_STORAGE_KEY) ?? 'true'),
);
const isDropdownOpen = ref<boolean>(false);

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value;
};

watch(leftDrawerOpen, (newValue) => {
  localStorage.setItem(LEFT_DRAWER_STORAGE_KEY, JSON.stringify(newValue));
});

onMounted(() => {
  if ($q.screen.lt.md) {
    leftDrawerOpen.value = false;
  }
});

// --- SIMULASI DATA BACKEND (PENGGANTI INERTIA) ---

// Definisikan tipe data untuk user dan company
interface User {
  name: string;
  role: 'ADMIN' | 'CASHIER';
}
interface Company {
  name: string;
}

// Data user dan company (sebelumnya dari page.props)
const auth = ref<{ user: User }>({
  user: {
    name: 'Admin Amanah',
    role: 'ADMIN',
  },
});

const company = ref<Company>({
  name: 'Toko Amanah Jaya',
});

// Definisikan konstanta role
const USER_ROLES = {
  ADMIN: 'Administrator',
  CASHIER: 'Kasir',
};

// --- SIMULASI NAVIGASI & MENU ---
// Mendefinisikan menu dalam bentuk array of objects agar template lebih bersih
const menuItems = ref([
  {
    label: 'Dashboard',
    icon: 'dashboard',
    to: '/admin/dashboard',
    requiredRole: ['ADMIN', 'CASHIER'],
  },
  { separator: true },
  {
    label: 'Sales',
    icon: 'storefront',
    requiredRole: ['ADMIN', 'CASHIER'],
    children: [{ label: 'Pesanan Penjualan', icon: 'shopping_cart', to: '/admin/sales-orders' }],
  },
  {
    label: 'Pembelian',
    icon: 'local_shipping',
    requiredRole: ['ADMIN', 'CASHIER'],
    children: [
      { label: 'Pesanan Pembelian', icon: 'shopping_cart', to: '/admin/purchase-orders' },
      { label: 'Supplier', icon: 'people', to: '/admin/suppliers' },
    ],
  },
  {
    label: 'Inventori',
    icon: 'inventory_2',
    requiredRole: ['ADMIN', 'CASHIER'],
    children: [
      { label: 'Penyesuaian Stok', icon: 'swap_vert', to: '/admin/stock-adjustments' },
      { label: 'Produk', icon: 'inventory', to: '/admin/products' },
      { label: 'Kategori Produk', icon: 'category', to: '/admin/product-categories' },
    ],
  },
  { separator: true },
  {
    label: 'Keuangan',
    icon: 'account_balance_wallet',
    requiredRole: ['ADMIN'],
    children: [
      { label: 'Transaksi', icon: 'receipt_long', to: '/admin/finance-transactions' },
      { label: 'Akun Kas', icon: 'savings', to: '/admin/finance-accounts' },
    ],
  },
  { separator: true },
  {
    label: 'Pengaturan',
    icon: 'settings',
    requiredRole: ['ADMIN', 'CASHIER'],
    children: [
      {
        label: 'Manajemen Pengguna',
        icon: 'group',
        to: '/admin/settings/users',
        requiredRole: ['ADMIN'],
      },
      { label: 'Profil Saya', icon: 'manage_accounts', to: '/admin/settings/profile' },
      {
        label: 'Profil Perusahaan',
        icon: 'apartment',
        to: '/admin/settings/company-profile',
        requiredRole: ['ADMIN'],
      },
    ],
  },
]);

// Fungsi untuk mengecek apakah menu harus ditampilkan berdasarkan role user
const canViewMenu = (item: { requiredRole?: string[] }): boolean => {
  if (!item.requiredRole) return true; // Jika tidak ada role, semua bisa lihat
  return item.requiredRole.includes(auth.value.user.role);
};

// Fungsi untuk menentukan apakah expansion item harus terbuka default
// const isGroupActive = (pathPrefix: string) => {
//   // Cari anak menu yang path-nya berawalan sama dengan pathPrefix
//   const group = menuItems.value.find((item) =>
//     item.children?.some((child) => child.to?.startsWith(pathPrefix)),
//   );
//   // Jika ada anak menu yang aktif, buka grupnya
//   return group?.children?.some((child) => route.path.startsWith(child.to ?? '')) ?? false;
// };

// Fungsi simulasi logout
const logout = () => {
  $q.notify({
    color: 'positive',
    icon: 'logout',
    message: 'Anda telah logout.',
  });
  // router.push('/login');
};
</script>

<template>
  <q-layout view="lHh LpR lFf">
    <q-header>
      <q-toolbar class="bg-grey-1 text-black toolbar-scrolled">
        <q-btn v-if="!leftDrawerOpen" flat dense aria-label="Menu" @click="toggleLeftDrawer">
          <q-icon class="material-symbols-outlined" name="dock_to_right" />
        </q-btn>
        <slot name="left-button"></slot>
        <q-toolbar-title :class="{ 'q-ml-sm': leftDrawerOpen }" style="font-size: 18px">
          <slot name="title">Amanah POS</slot>
        </q-toolbar-title>
        <q-space />
        <slot name="right-button"></slot>
      </q-toolbar>
      <slot name="header"></slot>
    </q-header>

    <q-drawer
      :breakpoint="768"
      v-model="leftDrawerOpen"
      bordered
      class="bg-grey-2"
      style="color: #444"
    >
      <div
        class="absolute-top"
        style="
          height: 50px;
          border-bottom: 1px solid #ddd;
          align-items: center;
          justify-content: center;
        "
      >
       <div style="width: 100%; padding: 8px; display: flex; justify-content: space-between">
          <q-btn-dropdown
            v-model="isDropdownOpen"
            class="profile-btn text-bold"
            flat
            :label="company.name"
            style="justify-content: spac e-between; flex-grow: 1; overflow: hidden"
            :class="{ 'profile-btn-active': isDropdownOpen }"
          >
            <q-list id="profile-btn-popup" style="color: #444">
              <q-item>
                <q-avatar style="margin-left: -15px">
                  <q-icon name="person" />
                </q-avatar>
                <q-item-section>
                  <q-item-label>
                    <div class="text-bold">{{ auth.user.name }}</div>
                    <div class="text-grey-8 text-caption">
                      {{ USER_ROLES[auth.user.role] }} @ {{ company.name }}
                    </div>
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable v-ripple to="/admin/settings/profile">
                <q-item-section>
                  <q-item-label
                    ><q-icon name="manage_accounts" class="q-mr-sm" /> Profil Saya</q-item-label
                  >
                </q-item-section>
              </q-item>
              <q-item
                v-if="auth.user.role === 'ADMIN'"
                clickable
                v-ripple
                to="/admin/settings/company-profile"
              >
                <q-item-section>
                  <q-item-label
                    ><q-icon name="home_work" class="q-mr-sm" /> Profil Perusahaan</q-item-label
                  >
                </q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable v-ripple @click="logout">
                <q-item-section>
                  <q-item-label><q-icon name="logout" class="q-mr-sm" /> Logout</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
          <q-btn v-if="leftDrawerOpen" flat dense aria-label="Menu" @click="toggleLeftDrawer">
            <q-icon class="material-symbols-outlined" name="dock_to_left" />
          </q-btn>
        </div>
      </div>

      <q-scroll-area style="height: calc(100% - 50px); margin-top: 50px">
        <q-list id="main-nav" style="margin-bottom: 50px">
          <template v-for="(item, index) in menuItems" :key="index">
            <div v-if="canViewMenu(item)">
              <q-separator v-if="item.separator" />
              <q-expansion-item
                v-else-if="item.children"
                :icon="item.icon"
                :label="item.label"
                :default-opened="
                  item.children.some((child) => route.path.startsWith(child.to ?? ''))
                "
              >
                <q-item
                  v-for="child in item.children"
                  :key="child.label"
                  class="subnav"
                  clickable
                  v-ripple
                  :to="child.to"
                  exact-active-class="q-item--active"
                >
                  <q-item-section avatar>
                    <q-icon :name="child.icon" />
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ child.label }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-expansion-item>
              <q-item v-else clickable v-ripple :to="item.to" exact-active-class="q-item--active">
                <q-item-section avatar>
                  <q-icon :name="item.icon" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ item.label }}</q-item-label>
                </q-item-section>
              </q-item>
            </div>
          </template>
        </q-list>
        <div class="absolute-bottom text-grey-6 q-pa-md">
          &copy; {{ new Date().getFullYear() }} - Amanah POS v1.0.0
        </div>
      </q-scroll-area>
    </q-drawer>

    <q-page-container class="bg-grey-1">
      <q-page padding>
        <router-view />
        <slot></slot>
      </q-page>
    </q-page-container>

    <slot name="footer"></slot>
  </q-layout>
</template>

<style>
.profile-btn span.block {
  text-align: left !important;
  width: 100% !important;
  margin-left: 10px !important;
}
</style>

<style scoped>
.q-toolbar {
  border-bottom: 1px solid transparent;
}
.toolbar-scrolled {
  box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
  border-bottom: 1px solid #ddd;
}
.profile-btn-active {
  background-color: #ddd !important;
}
.q-list .q-item--active {
  background-color: rgba(0, 0, 0, 0.1);
  color: #000;
  font-weight: bold;
}
.subnav.q-item--active {
  background-color: rgba(0, 0, 0, 0.05);
}
</style>
