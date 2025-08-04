<script setup lang="ts">
import { ref } from 'vue';
import { useMeta } from 'quasar';

const title = 'Dasbor Admin';
useMeta({ title });

const stats = ref([
  {
    title: 'Santri Aktif',
    icon: 'groups',
    mainValue: '83',
    subTitle: 'Total Saldo Santri',
    subValue: 'Rp. 20.283.500',
    color: 'blue',
  },
  {
    title: 'Pengguna Aktif',
    icon: 'manage_accounts',
    mainValue: '5',
    subTitle: 'Total Utang Santri',
    subValue: 'Rp. 0',
    color: 'blue',
  },
  {
    title: 'Total Saldo Koperasi',
    icon: 'account_balance',
    mainValue: 'Rp. 0',
    subTitle: 'Total Piutang Santri',
    subValue: 'Rp. 20.283.500',
    color: 'orange',
  },
  {
    title: 'Total Piutang Pemasok',
    icon: 'local_shipping',
    mainValue: 'Rp. 0',
    subTitle: 'Total Piutang Pemasok',
    subValue: 'Rp. 20.283.500',
    color: 'red',
  },
]);

</script>

<template>
  <q-page class="q-pa-md bg-grey-3">
    <q-toolbar class="bg-white q-mb-md rounded-borders">
      <q-toolbar-title class="text-weight-medium">
        Dashboard
      </q-toolbar-title>
    </q-toolbar>

    <div class="text-subtitle1 text-grey-8 q-mb-sm">Statistik Akun</div>

    <div class="row q-col-gutter-md">
      <div v-for="stat in stats" :key="stat.title" class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <div :class="`bg-${stat.color}`" style="height: 50px;">
            <q-card-section class="q-pa-sm row items-center no-wrap">
              <q-icon :name="stat.icon" color="white" size="md" class="q-mr-sm" />
              <div class="text-white text-subtitle2">{{ stat.title }}</div>
            </q-card-section>
          </div>
          <q-card-section>
            <div class="text-h4 text-weight-bold">{{ stat.mainValue }}</div>
          </q-card-section>
          <q-separator />
          <q-card-section>
            <div class="text-caption text-grey-7">{{ stat.subTitle }}</div>
            <div class="text-subtitle1 text-weight-medium">{{ stat.subValue }}</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    </q-page>
</template>

<style scoped>
/* Tidak perlu style khusus untuk saat ini */
</style>
