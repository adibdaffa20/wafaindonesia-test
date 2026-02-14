<template>
  <div class="layout">
    <Navbar title="Activity Log" :user="authUser" />

    <main class="content">
      <div class="container">
        <h2 class="page-title">Activity Log</h2>

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

function formatDate(iso){
  try { return new Date(iso).toLocaleString('id-ID') } catch { return iso }
}

async function fetchLogs(){
  loading.value = true
  try{
    const res = await fetch('/dashboard/api/activity-logs', { headers: { Accept: 'application/json' } })
    const data = await res.json()
    logs.value = data.data || []
  } finally {
    loading.value = false
  }
}

onMounted(fetchLogs)
</script>

<style>
/* pakai style yang sama dari Dashboard.vue (brand tokens + table wrapper) */
/* cukup tambah pill badge untuk action */

.page-title{
  margin: 6px 0 12px;
  font-size: 18px;
  font-weight: 900;
  color: rgba(15,23,42,0.88);
}

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
</style>
