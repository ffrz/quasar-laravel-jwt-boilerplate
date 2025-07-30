<script setup lang="ts">
import { ref } from 'vue';
import { Line } from 'vue-chartjs';
import type { QTableProps } from 'quasar';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Filler,
  type ScriptableContext,
} from 'chart.js';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Filler,
);

const stats = ref([
  { label: 'Total Pengguna', value: '1,250', icon: 'o_people', color: 'primary' },
  { label: 'Penjualan Hari Ini', value: 'Rp 3.5M', icon: 'o_shopping_cart', color: 'positive' },
  { label: 'Pesanan Baru', value: '75', icon: 'o_receipt_long', color: 'orange' },
  { label: 'Pending Review', value: '12', icon: 'o_rate_review', color: 'negative' },
]);

// --- Data Dummy untuk Grafik ---
const chartData = ref({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
  datasets: [
    {
      label: 'Penjualan (Juta Rp)',
      backgroundColor: (context: ScriptableContext<'line'>) => {
        const ctx = context.chart.ctx;
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(3, 169, 244, 0.5)');
        gradient.addColorStop(1, 'rgba(3, 169, 244, 0)');
        return gradient;
      },
      borderColor: '#03A9F4',
      data: [40, 39, 60, 55, 75, 65, 80],
      fill: true,
      tension: 0.4,
    },
  ],
});

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    y: { beginAtZero: true },
    x: { grid: { display: false } },
  },
});

const userColumns = ref<QTableProps['columns']>([
  { name: 'name', label: 'Nama', align: 'left', field: 'name', sortable: true },
  { name: 'email', label: 'Email', align: 'left', field: 'email' },
  { name: 'status', label: 'Status', align: 'center', field: 'status' },
]);

const recentUsers = ref([
  { name: 'Budi Santoso', email: 'budi.s@example.com', status: 'Aktif' },
  { name: 'Citra Lestari', email: 'citra.l@example.com', status: 'Aktif' },
  { name: 'Agus Wijaya', email: 'agus.w@example.com', status: 'Nonaktif' },
  { name: 'Dewi Anggraini', email: 'dewi.a@example.com', status: 'Aktif' },
  { name: 'Eko Prasetyo', email: 'eko.p@example.com', status: 'Aktif' },
]);
</script>

<template>
  <q-page padding class="q-gutter-y-md">
    <div class="row items-center justify-between">
      <div class="col-auto">
        <div class="text-h4 text-weight-medium">Dasbor Admin</div>
        <div class="text-subtitle1 text-grey-7">Ringkasan data aplikasi Anda.</div>
      </div>
    </div>

    <div class="row q-col-gutter-lg">
      <div v-for="stat in stats" :key="stat.label" class="col-12 col-sm-6 col-md-3">
        <q-card class="kpi-card" flat bordered>
          <q-card-section>
            <div class="row items-center no-wrap">
              <div class="col">
                <div class="text-subtitle1 text-grey-7">{{ stat.label }}</div>
                <span class="text-h5 text-weight-bold">{{ stat.value }}</span>
              </div>
              <div class="col-auto">
                <q-icon :name="stat.icon" :color="stat.color" size="48px" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-lg">
      <div class="col-12 col-lg-7">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-h6">Analisis Penjualan</div>
            <div class="text-subtitle2 text-grey-6">7 bulan terakhir</div>
          </q-card-section>
          <q-card-section>
            <Line :data="chartData" :options="chartOptions" style="height: 300px" />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-lg-5">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-h6">Pengguna Terbaru</div>
          </q-card-section>
          <q-table
            :rows="recentUsers"
            :columns="userColumns"
            row-key="name"
            flat
            :rows-per-page-options="[5]"
          >
            <template v-slot:body-cell-status="props">
              <q-td :props="props">
                <q-badge :color="props.value === 'Aktif' ? 'positive' : 'negative'">
                  {{ props.value }}
                </q-badge>
              </q-td>
            </template>
          </q-table>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<style scoped lang="scss">
.kpi-card {
  border-left: 4px solid var(--q-color-primary);
}
.kpi-card:nth-child(2) {
  border-left-color: var(--q-color-positive);
}
.kpi-card:nth-child(3) {
  border-left-color: var(--q-color-orange);
}
.kpi-card:nth-child(4) {
  border-left-color: var(--q-color-negative);
}
</style>
