<template>
  <div class="layout">
    <Navbar title="Activity Log" :user="authUser" />

    <main class="content">
      <div class="container">
        <div class="page-head">
          <h2 class="page-title">Activity Log</h2>

          <!-- FILTER BAR -->
          <div class="filters">
            <div class="filter-item">
              <label class="filter-label">Mode</label>
              <select v-model="mode" class="filter-input" @change="onModeChange">
                <option value="day">Tanggal</option>
                <option value="month">Bulan</option>
                <option value="year">Tahun</option>
              </select>
            </div>

            <div class="filter-item" v-if="mode === 'day'">
              <label class="filter-label">Tanggal</label>
              <input type="date" v-model="date" class="filter-input" />
            </div>

            <div class="filter-item" v-if="mode === 'month'">
              <label class="filter-label">Bulan</label>
              <select v-model="month" class="filter-input">
                <option v-for="m in months" :key="m.value" :value="m.value">
                  {{ m.label }}
                </option>
              </select>
            </div>

            <div class="filter-item" v-if="mode === 'month'">
              <label class="filter-label">Tahun</label>
              <select v-model="year" class="filter-input">
                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
              </select>
            </div>

            <div class="filter-item" v-if="mode === 'year'">
              <label class="filter-label">Tahun</label>
              <select v-model="year" class="filter-input">
                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
              </select>
            </div>

            <button class="btn primary" @click="fetchLogs(1)" :disabled="loading">
              {{ loading ? 'Loading...' : 'Terapkan' }}
            </button>

            <button class="btn" @click="resetFilter" :disabled="loading">
              Reset
            </button>
          </div>
        </div>

        <!-- TABLE -->
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Waktu</th>
                <th>User</th>
                <th>Aksi</th>
                <th>Deskripsi</th>
                <th>IP</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="5">Memuat...</td></tr>
              <tr v-else-if="logs.length === 0"><td colspan="5">Belum ada data.</td></tr>

              <tr v-else v-for="row in logs" :key="row.id">
                <td>{{ formatDate(row.created_at) }}</td>
                <td>{{ row.user?.name || row.user?.email || '-' }}</td>
                <td>
                  <span class="pill" :class="row.action">{{ row.action }}</span>
                </td>
                <td>{{ row.description }}</td>
                <td>{{ row.ip_address || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PAGINATION -->
        <div v-if="meta" class="pagination">
          <button class="btn" @click="fetchLogs(meta.current_page - 1)" :disabled="loading || meta.current_page <= 1">
            Prev
          </button>

          <span>Page {{ meta.current_page }} / {{ meta.last_page }}</span>

          <button class="btn" @click="fetchLogs(meta.current_page + 1)" :disabled="loading || meta.current_page >= meta.last_page">
            Next
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '../components/Navbar.vue'

const authUser = ref(window.__AUTH__?.user || {})
const logs = ref([])
const loading = ref(false)
const meta = ref(null)

// filter state
const mode = ref('day') // day | month | year
const date = ref(todayISO())
const month = ref(String(new Date().getMonth() + 1).padStart(2, '0'))
const year = ref(String(new Date().getFullYear()))

const months = [
  { value: '01', label: 'Januari' },
  { value: '02', label: 'Februari' },
  { value: '03', label: 'Maret' },
  { value: '04', label: 'April' },
  { value: '05', label: 'Mei' },
  { value: '06', label: 'Juni' },
  { value: '07', label: 'Juli' },
  { value: '08', label: 'Agustus' },
  { value: '09', label: 'September' },
  { value: '10', label: 'Oktober' },
  { value: '11', label: 'November' },
  { value: '12', label: 'Desember' },
]

const years = (() => {
  const now = new Date().getFullYear()
  // 5 tahun ke belakang + 1 tahun ke depan (biar fleksibel)
  return Array.from({ length: 7 }, (_, i) => String(now + 1 - i))
})()

function todayISO() {
  const d = new Date()
  const yyyy = d.getFullYear()
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  return `${yyyy}-${mm}-${dd}`
}

function formatDate(iso) {
  try {
    return new Date(iso).toLocaleString('id-ID')
  } catch {
    return iso
  }
}

function onModeChange() {
  // set default value yang masuk akal
  if (mode.value === 'day' && !date.value) date.value = todayISO()
  if (mode.value === 'month') {
    if (!month.value) month.value = String(new Date().getMonth() + 1).padStart(2, '0')
    if (!year.value) year.value = String(new Date().getFullYear())
  }
  if (mode.value === 'year' && !year.value) year.value = String(new Date().getFullYear())
}

function buildQuery(page = 1) {
  const params = new URLSearchParams()
  params.set('page', String(page))

  if (mode.value === 'day' && date.value) {
    params.set('date', date.value)
  } else if (mode.value === 'month') {
    if (month.value) params.set('month', month.value)
    if (year.value) params.set('year', year.value)
  } else if (mode.value === 'year') {
    if (year.value) params.set('year', year.value)
  }

  return params.toString()
}

function resetFilter() {
  mode.value = 'day'
  date.value = todayISO()
  month.value = String(new Date().getMonth() + 1).padStart(2, '0')
  year.value = String(new Date().getFullYear())
  fetchLogs(1)
}

async function fetchLogs(page = 1) {
  loading.value = true
  try {
    const qs = buildQuery(page)
    const res = await fetch(`/dashboard/api/activity-logs?${qs}`, {
      headers: { Accept: 'application/json' },
    })
    const data = await res.json()

    logs.value = data.data || []
    meta.value = data.current_page
      ? { current_page: data.current_page, last_page: data.last_page }
      : null
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchLogs(1))
</script>

<style>
.page-head{
  display:flex;
  align-items:flex-start;
  justify-content: space-between;
  gap: 14px;
  flex-wrap: wrap;
  margin-bottom: 10px;
}

.page-title{
  margin: 6px 0 0;
  font-size: 18px;
  font-weight: 900;
  color: rgba(15,23,42,0.88);
}

/* filter bar */
.filters{
  display:flex;
  align-items:flex-end;
  gap: 10px;
  flex-wrap: wrap;

  padding: 12px;
  border-radius: 16px;
  background: #fff;
  border: 1px solid rgba(15,23,42,0.10);
  box-shadow: 0 10px 22px rgba(15,23,42,0.06);
}

.filter-item{
  display:flex;
  flex-direction: column;
  gap: 6px;
  min-width: 150px;
}

.filter-label{
  font-size: 12px;
  font-weight: 800;
  color: rgba(15,23,42,0.65);
}

.filter-input{
  padding: 10px 12px;
  border-radius: 14px;
  border: 1.6px solid rgba(15,23,42,0.14);
  outline: none;
  font-size: 13px;
  background: #fff;
  color: rgba(15,23,42,0.88);
}

.filter-input:focus{
  border-color: rgba(233,30,99,0.55);
  box-shadow: 0 0 0 4px rgba(233,30,99,0.14);
}

/* pill */
.pill{
  display:inline-flex;
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 800;
  border: 1px solid rgba(15,23,42,0.10);
  background: rgba(15,23,42,0.04);
  color: rgba(15,23,42,0.75);
}

.pill.login{
  background: rgba(233,30,99,0.08);
  border-color: rgba(233,30,99,0.22);
  color: rgba(142,42,106,0.95);
}

.pill.update{
  background: rgba(15,23,42,0.04);
  border-color: rgba(15,23,42,0.12);
}

.pill.delete{
  background: rgba(239,68,68,0.10);
  border-color: rgba(239,68,68,0.22);
  color: rgba(185,28,28,0.95);
}

/* pagination (kalau CSS global dashboard sudah ada, ini optional) */
.pagination{
  margin-top: 16px;
  display:flex;
  gap: 10px;
  align-items:center;
  justify-content:center;
  padding: 12px;
  border-radius: 16px;
  background: #fff;
  border: 1px solid rgba(15,23,42,0.10);
  box-shadow: 0 10px 22px rgba(15,23,42,0.06);
}

/* responsive */
@media (max-width: 520px){
  .filters{ width: 100%; }
  .filter-item{ min-width: 100%; }
}
</style>
