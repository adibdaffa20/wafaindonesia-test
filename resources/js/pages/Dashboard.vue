<template>
  <div class="layout">
    <Navbar title="Dashboard Leads" :user="authUser" />

    <!-- CONTENT -->
      <div class="container">
      <p class="subtitle">Lihat, cari, edit, dan hapus data pendaftaran.</p>

      <!-- SEARCH -->
      <div class="search-bar">
        <input
          v-model="q"
          @keyup.enter="fetchLeads(1)"
          placeholder="Cari nama / WA / email / lembaga..."
        />

        <!-- FILTER TANGGAL/BULAN/TAHUN -->
        <select v-model="mode" class="filter-input" @change="onModeChange">
          <option value="day">Tanggal</option>
          <option value="month">Bulan</option>
          <option value="year">Tahun</option>
        </select>

        <input
          v-if="mode === 'day'"
          type="date"
          v-model="date"
          class="filter-input"
          title="Filter tanggal"
        />

        <select v-if="mode === 'month'" v-model="month" class="filter-input" title="Filter bulan">
          <option v-for="m in months" :key="m.value" :value="m.value">
            {{ m.label }}
          </option>
        </select>

        <select v-if="mode === 'month' || mode === 'year'" v-model="year" class="filter-input" title="Filter tahun">
          <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>

        <button class="btn" @click="resetFilter" :disabled="loading" title="Reset filter tanggal/bulan/tahun">
          Reset Filter
        </button>

        <button class="btn primary" @click="fetchLeads(1)" :disabled="loading">
          {{ loading ? 'Loading...' : 'Cari' }}
        </button>

        <button @click="resetSearch" :disabled="loading">Reset</button>

        <button @click="fetchLeads(meta?.current_page || 1)" :disabled="loading" title="Ambil data ulang">
          Refresh
        </button>
      </div>

      <!-- FLASH -->
      <div v-if="flash" class="success">{{ flash }}</div>
      <div v-if="serverError" class="error">{{ serverError }}</div>

      <!-- TABLE -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama</th>
              <th>No. WA</th>
              <th>Email</th>
              <th>Lembaga</th>
              <th>Tanggal</th>
              <th style="width: 160px;">Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="loading">
              <td colspan="7">Memuat data...</td>
            </tr>

            <tr v-else-if="leads.length === 0">
              <td colspan="7">Data tidak ditemukan.</td>
            </tr>

            <tr v-else v-for="row in leads" :key="row.id">
              <td>{{ row.id }}</td>
              <td>{{ row.nama }}</td>
              <td>{{ row.no_wa }}</td>
              <td>{{ row.email }}</td>
              <td>{{ row.nama_lembaga }}</td>
              <td>{{ formatDate(row.created_at) }}</td>
              <td>
                <button class="btn" @click="openEdit(row)">Edit</button>
                <button class="btn danger" @click="remove(row)">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="meta" class="pagination">
        <button
          class="btn"
          @click="fetchLeads(meta.current_page - 1)"
          :disabled="loading || meta.current_page <= 1"
        >
          Prev
        </button>

        <span>Page {{ meta.current_page }} / {{ meta.last_page }}</span>

        <button
          class="btn"
          @click="fetchLeads(meta.current_page + 1)"
          :disabled="loading || meta.current_page >= meta.last_page"
        >
          Next
        </button>
      </div>
    </div>

    <!-- MODAL EDIT -->
    <div v-if="editOpen" class="modal-backdrop" @click.self="closeEdit">
      <div class="modal">
        <div class="modal-header">
          <h3 style="margin:0;">Edit Data Leads #{{ editForm.id }}</h3>
          <button class="btn" @click="closeEdit" title="Tutup (Esc)">X</button>
        </div>

        <div class="modal-body">
        <div class="field">
            <label class="field-label">Nama</label>
            <input class="field-input" v-model="editForm.nama" />
            <div v-if="errors.nama" class="field-error">{{ errors.nama }}</div>
        </div>

        <div class="field">
            <label class="field-label">No. WA</label>
            <input class="field-input" v-model="editForm.no_wa" />
            <div v-if="errors.no_wa" class="field-error">{{ errors.no_wa }}</div>
        </div>

        <div class="field">
            <label class="field-label">Email</label>
            <input class="field-input" type="email" v-model="editForm.email" />
            <div v-if="errors.email" class="field-error">{{ errors.email }}</div>
        </div>

        <div class="field">
            <label class="field-label">Nama Lembaga</label>
            <input class="field-input" v-model="editForm.nama_lembaga" />
            <div v-if="errors.nama_lembaga" class="field-error">{{ errors.nama_lembaga }}</div>
        </div>
        </div>

        <div class="modal-footer">
          <button class="btn" @click="closeEdit">Batal</button>
          <button class="btn primary" @click="saveEdit" :disabled="saving">
            {{ saving ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, reactive, ref } from 'vue'
import Navbar from '../components/Navbar.vue'

const q = ref('')
const loading = ref(false)
const saving = ref(false)
const flash = ref('')
const serverError = ref('')

const leads = ref([])
const meta = ref(null)

// user login dari Blade: window.__AUTH__ = { user: {...} }
const authUser = ref(window.__AUTH__?.user || {})

// --- Edit modal state
const editOpen = ref(false)
const editForm = reactive({
  id: null,
  nama: '',
  no_wa: '',
  email: '',
  nama_lembaga: '',
})

const errors = reactive({
  nama: '',
  no_wa: '',
  email: '',
  nama_lembaga: '',
})

function csrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

function resetErrors() {
  errors.nama = ''
  errors.no_wa = ''
  errors.email = ''
  errors.nama_lembaga = ''
}

function resetSearch() {
  q.value = ''
  fetchLeads(1)
}

function formatDate(iso) {
  if (!iso) return '-'
  return String(iso).slice(0, 10)
}

// ===== Filter Tanggal/Bulan/Tahun =====
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
  return Array.from({ length: 7 }, (_, i) => String(now + 1 - i))
})()

function todayISO() {
  const d = new Date()
  const yyyy = d.getFullYear()
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  return `${yyyy}-${mm}-${dd}`
}

function onModeChange() {
  if (mode.value === 'day' && !date.value) date.value = todayISO()
  if (mode.value === 'month') {
    if (!month.value) month.value = String(new Date().getMonth() + 1).padStart(2, '0')
    if (!year.value) year.value = String(new Date().getFullYear())
  }
  if (mode.value === 'year' && !year.value) year.value = String(new Date().getFullYear())
}

function resetFilter() {
  mode.value = 'day'
  date.value = todayISO()
  month.value = String(new Date().getMonth() + 1).padStart(2, '0')
  year.value = String(new Date().getFullYear())
  fetchLeads(1)
}


async function fetchLeads(page = 1) {
  loading.value = true
  serverError.value = ''
  // jangan reset flash tiap fetch kalau kamu mau pesan tetap, tapi biasanya enak direset
  // flash.value = ''

  try {
    const params = new URLSearchParams()
    if (q.value.trim()) params.set('q', q.value.trim())
    params.set('page', String(page))
  // filter created_at
if (mode.value === 'day' && date.value) {
  params.set('date', date.value)
} else if (mode.value === 'month') {
  if (month.value) params.set('month', month.value)
  if (year.value) params.set('year', year.value)
} else if (mode.value === 'year') {
  if (year.value) params.set('year', year.value)
}

    const res = await fetch(`/dashboard/api/leads?${params.toString()}`, {
      headers: { Accept: 'application/json' },
    })

    if (!res.ok) {
      serverError.value = 'Gagal memuat data.'
      leads.value = []
      meta.value = null
      return
    }

    const data = await res.json()
    leads.value = data.data || []
    meta.value = {
      current_page: data.current_page,
      last_page: data.last_page,
    }
  } catch (e) {
    serverError.value = 'Gagal memuat data.'
  } finally {
    loading.value = false
  }
}

function openEdit(row) {
  resetErrors()
  flash.value = ''
  serverError.value = ''

  editForm.id = row.id
  editForm.nama = row.nama
  editForm.no_wa = row.no_wa
  editForm.email = row.email
  editForm.nama_lembaga = row.nama_lembaga
  editOpen.value = true
}

function closeEdit() {
  editOpen.value = false
}

async function saveEdit() {
  saving.value = true
  flash.value = ''
  serverError.value = ''
  resetErrors()

  try {
    const res = await fetch(`/dashboard/api/leads/${editForm.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf(),
      },
      body: JSON.stringify({
        nama: editForm.nama,
        no_wa: editForm.no_wa,
        email: editForm.email,
        nama_lembaga: editForm.nama_lembaga,
      }),
    })

    if (res.status === 422) {
      const data = await res.json()
      const v = data.errors || {}
      errors.nama = v.nama?.[0] || ''
      errors.no_wa = v.no_wa?.[0] || ''
      errors.email = v.email?.[0] || ''
      errors.nama_lembaga = v.nama_lembaga?.[0] || ''
      return
    }

    const data = await res.json().catch(() => ({}))
    if (!res.ok) {
      serverError.value = data?.message || 'Gagal menyimpan.'
      return
    }

    flash.value = data.message || 'Berhasil disimpan.'
    editOpen.value = false
    await fetchLeads(meta.value?.current_page || 1)
  } catch (e) {
    serverError.value = 'Tidak bisa terhubung ke server.'
  } finally {
    saving.value = false
  }
}

async function remove(row) {
  const ok = confirm(`Yakin hapus Lead #${row.id}?`)
  if (!ok) return

  flash.value = ''
  serverError.value = ''

  try {
    const res = await fetch(`/dashboard/api/leads/${row.id}`, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf(),
      },
    })

    const data = await res.json().catch(() => ({}))
    if (!res.ok) {
      serverError.value = data?.message || 'Gagal menghapus.'
      return
    }

    flash.value = data.message || 'Berhasil dihapus.'

    // kalau page terakhir jadi kosong setelah delete, mundur 1 page
    const current = meta.value?.current_page || 1
    const last = meta.value?.last_page || 1

    // Refresh list; kalau current page > last page (kasus delete item terakhir), geser
    await fetchLeads(Math.min(current, last))
  } catch (e) {
    serverError.value = 'Tidak bisa terhubung ke server.'
  }
}

function onKeydown(e) {
  if (e.key === 'Escape' && editOpen.value) closeEdit()
}

onMounted(() => {
  fetchLeads(1)
  window.addEventListener('keydown', onKeydown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeydown)
})

</script>

<style>
/* ====== Brand tokens (Wafa) ====== */
:root{
  --brand-900:#8E2A6A;   /* ungu-magenta */
  --brand-600:#E91E63;   /* pink-magenta */

  /* neutrals */
  --bg:#f6f6f8;
  --card:#ffffff;
  --text:#0f172a;
  --muted:rgba(15,23,42,0.62);
  --line:rgba(15,23,42,0.10);

  /* effects */
  --radius:18px;
  --radius-sm:14px;
  --shadow:0 18px 45px rgba(15,23,42,0.10);
  --shadow-sm:0 10px 22px rgba(15,23,42,0.08);
  --focus:0 0 0 4px rgba(233,30,99,0.18);

  /* state */
  --danger:#ef4444;
}

/* ====== Reset ====== */
*,
*::before,
*::after{ box-sizing:border-box; }

body {
  font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
}

/* ====== Content ====== */
.container{
  max-width: 1100px;
  margin: 26px auto;
  padding: 0 16px 28px;
}

/* Heading/subtitle should be readable on light bg */
.subtitle{
  margin: 14px 0 0;
  color: var(--muted);
  font-size: 14px;
  line-height: 1.6;
}

/* ====== Search Bar (clean + branded accent) ====== */
.search-bar{
  margin: 18px 0 16px;
  padding: 14px;
  border-radius: var(--radius);
  background: var(--card);
  border: 1px solid var(--line);
  box-shadow: var(--shadow-sm);

  display:flex;
  gap: 10px;
  flex-wrap: wrap;
  align-items:center;
}

.search-bar input{
  flex: 1;
  min-width: 260px;
  padding: 12px 14px;
  border-radius: var(--radius-sm);
  border: 1.6px solid rgba(15,23,42,0.14);
  background: #fff;
  outline: none;
  font-size: 14px;
  transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease;
}

.search-bar input::placeholder{
  color: rgba(15,23,42,0.45);
}

.search-bar input:focus{
  border-color: rgba(233,30,99,0.55);
  box-shadow: var(--focus);
  transform: translateY(-1px);
}

/* ====== Buttons (elegant, not too loud) ====== */
.btn{
  padding: 10px 14px;
  border-radius: var(--radius-sm);
  cursor:pointer;
  font-weight: 750;
  font-size: 13px;

  background: #fff;
  color: rgba(15,23,42,0.86);
  border: 1px solid rgba(15,23,42,0.14);
  box-shadow: 0 2px 0 rgba(15,23,42,0.02);

  transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease, filter .16s ease;
}

.btn:hover{
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
  border-color: rgba(233,30,99,0.28);
}

.btn:active{
  transform: translateY(0);
  filter: brightness(0.98);
}

.btn:disabled{
  opacity: .55;
  cursor:not-allowed;
  transform:none !important;
  box-shadow:none !important;
}

/* Primary: brand gradient as accent */
.btn.primary{
  border: none;
  color:#fff;
  background: linear-gradient(135deg, var(--brand-900), var(--brand-600));
  box-shadow: 0 10px 22px rgba(142,42,106,0.18);
}

.btn.primary:hover{
  box-shadow: 0 14px 28px rgba(142,42,106,0.22);
}

/* Danger: outline brand-ish but still readable */
.btn.danger{
  background:#fff;
  color: var(--brand-900);
  border: 1.8px solid rgba(233,30,99,0.55);
}

.btn.danger:hover{
  border-color: rgba(233,30,99,0.75);
  box-shadow: 0 12px 24px rgba(233,30,99,0.10);
}

/* In-table buttons a bit smaller */
td .btn{
  padding: 8px 10px;
  border-radius: 12px;
  font-size: 12.5px;
}

/* ====== Flash Messages (keep calm) ====== */
.success,
.error{
  margin-top: 12px;
  padding: 12px 14px;
  border-radius: var(--radius-sm);
  font-weight: 650;
  font-size: 13.5px;
  box-shadow: var(--shadow-sm);
}

.success{
  background: #f0fdf4;
  border: 1px solid rgba(34,197,94,0.25);
  color: #166534;
}

.error{
  background: #fff1f2;
  border: 1px solid rgba(244,63,94,0.25);
  color: #9f1239;
}

/* ====== Table (readability first) ====== */
.table-wrapper{
  margin-top: 14px;
  border-radius: var(--radius);
  overflow: hidden;
  background: var(--card);
  border: 1px solid var(--line);
  box-shadow: var(--shadow);
}

.table-wrapper{
  overflow-x:auto; /* responsive */
}

table{
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  min-width: 900px;
  color: var(--text);
}

/* Header: light background + brand underline (elegant) */
thead th{
  position: sticky;
  top: 0;
  z-index: 2;

  background: #ffffff;
  color: rgba(15,23,42,0.72);
  font-size: 12px;
  letter-spacing: .35px;
  text-transform: uppercase;

  border-bottom: 2px solid rgba(233,30,99,0.25);
}

th, td{
  padding: 12px 14px;
  text-align: left;
  border-bottom: 1px solid rgba(15,23,42,0.06);
  white-space: nowrap;
}

/* zebra + hover for scanability */
tbody tr:nth-child(even){
  background: rgba(15,23,42,0.018);
}

tbody tr:hover{
  background: rgba(233,30,99,0.06);
}

/* action column align */
td:last-child{
  display:flex;
  gap: 8px;
}

/* Make long fields readable */
td:nth-child(4){ /* Email */
  max-width: 260px;
  overflow: hidden;
  text-overflow: ellipsis;
}
td:nth-child(5){ /* Lembaga */
  max-width: 260px;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ====== Pagination (subtle) ====== */
.pagination{
  margin-top: 16px;
  display:flex;
  gap: 10px;
  align-items:center;
  justify-content:center;

  padding: 12px;
  border-radius: var(--radius);
  background: var(--card);
  border: 1px solid var(--line);
  box-shadow: var(--shadow-sm);

  color: rgba(15,23,42,0.70);
  font-weight: 650;
}

/* Filter controls (tanggal/bulan/tahun) */
.filter-input{
  padding: 10px 12px;
  border-radius: var(--radius-sm);
  border: 1.6px solid rgba(15,23,42,0.14);
  background: #fff;
  outline: none;
  font-size: 13px;
  color: rgba(15,23,42,0.86);
}

.filter-input:focus{
  border-color: rgba(233,30,99,0.55);
  box-shadow: var(--focus);
}


/* ====== Modal (clean + branded top strip) ====== */
.modal-backdrop{
  position: fixed;
  inset: 0;
  background: rgba(2,6,23,0.55);
  display:grid;
  place-items:center;
  padding: 18px;
  z-index: 50;
}

.modal{
  width: 560px;
  max-width: 100%;
  border-radius: 20px;
  background: var(--card);
  border: 1px solid rgba(255,255,255,0.6);
  box-shadow: 0 30px 80px rgba(0,0,0,0.28);
  overflow:hidden;
  animation: pop .18s ease-out;
}

@keyframes pop{
  from{ transform: translateY(10px) scale(0.98); opacity:0; }
  to{ transform: translateY(0) scale(1); opacity:1; }
}

/* modal header: white + thin gradient bar */
.modal-header{
  display:flex;
  justify-content: space-between;
  align-items:center;
  gap: 10px;
  padding: 14px 16px;
  background: #fff;
  border-bottom: 1px solid rgba(15,23,42,0.08);
  position: relative;
}

.modal-header::before{
  content:"";
  position:absolute;
  left:0;
  top:0;
  height:4px;
  width:100%;
  background: linear-gradient(135deg, var(--brand-900), var(--brand-600));
}

.modal-header h3{
  margin:0;
  font-size: 15px;
  font-weight: 900;
  color: rgba(15,23,42,0.88);
}

/* close button stays neutral */
.modal-header .btn{
  padding: 8px 10px;
  border-radius: 12px;
}

.modal-body{
  padding: 16px;
  display:grid;
  gap: 12px;
}

.field{ margin:0; }

.field-label{
  display:block;
  font-weight: 800;
  margin-bottom: 8px;
  color: rgba(15,23,42,0.78);
  font-size: 13px;
}

.field-input{
  width:100%;
  padding: 12px 12px;
  border-radius: var(--radius-sm);
  border: 1.6px solid rgba(15,23,42,0.14);
  background: #fff;
  outline:none;
  transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease;
}

.field-input:focus{
  border-color: rgba(233,30,99,0.55);
  box-shadow: var(--focus);
  transform: translateY(-1px);
}

.field-error{
  margin-top: 6px;
  color: #b91c1c;
  font-size: 12.5px;
  font-weight: 650;
}

.modal-footer{
  padding: 14px 16px;
  display:flex;
  justify-content:flex-end;
  gap: 10px;
  background: rgba(15,23,42,0.02);
  border-top: 1px solid rgba(15,23,42,0.08);
}

/* ====== Responsive ====== */
@media (max-width: 520px){
  .subtitle{ font-size: 13px; }
  .search-bar{ padding: 12px; }
  .search-bar input{ min-width: 100%; }
  td:last-child{ flex-direction: column; align-items: stretch; }
  td .btn{ width: 100%; }
}
.layout{ display:flex; min-height:100vh; }
.content{ flex:1; }
@media (max-width: 860px){
  .layout{ flex-direction: column; }
}
</style>
