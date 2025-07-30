<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';

const $q = useQuasar();
const router = useRouter();
const leftDrawerOpen = ref(false);

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value;
}

function logout() {
  $q.dialog({
    title: 'Konfirmasi',
    message: 'Apakah Anda yakin ingin logout?',
    cancel: true,
    persistent: true,
  }).onOk(() => {
    console.log('User logged out');

    void router.push('/auth/login');
  });
}
</script>

<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />

        <q-toolbar-title> Aplikasi Saya </q-toolbar-title>

        <div>Quasar v{{ $q.version }}</div>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <q-list>
        <q-item-label header> Menu Navigasi </q-item-label>

        <q-item clickable to="/" exact>
          <q-item-section avatar> <q-icon name="home" /> </q-item-section>
          <q-item-section> <q-item-label>Home</q-item-label> </q-item-section>
        </q-item>

        <q-item clickable to="/admin/users" exact>
          <q-item-section avatar>
            <q-icon name="o_manage_accounts" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Manajemen Pengguna</q-item-label>
          </q-item-section>
        </q-item>

        <q-item clickable to="/profile" exact>
          <q-item-section avatar> <q-icon name="account_circle" /> </q-item-section>
          <q-item-section> <q-item-label>Profil</q-item-label> </q-item-section>
        </q-item>

        <q-item clickable @click="logout"> </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
