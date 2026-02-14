<template>
  <aside class="sidebar" :data-collapsed="collapsed">
    <!-- Brand -->
    <div class="brand">
      <div class="brand-mark" aria-hidden="true"></div>

      <div class="brand-text" v-if="!collapsed">
        <div class="brand-title">Wafa</div>
        <div class="brand-subtitle">{{ title }}</div>
      </div>

      <button
        class="collapse-btn"
        type="button"
        @click="collapsed = !collapsed"
        :title="collapsed ? 'Expand' : 'Collapse'"
        aria-label="Toggle sidebar"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path
            d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
          />
        </svg>
      </button>
    </div>

    <!-- Menu -->
    <nav class="menu" aria-label="Main">
      <router-link class="menu-item" to="/dashboard/leads" :class="{ active: isActive('/dashboard/leads') }">
        <span class="dot" aria-hidden="true"></span>
        <span class="label" v-if="!collapsed">Dashboard</span>
        <span class="sr-only" v-else>Dashboard</span>
      </router-link>

      <router-link class="menu-item" to="/dashboard/activity-log" :class="{ active: isActive('/dashboard/activity-log') }">
        <span class="dot" aria-hidden="true"></span>
        <span class="label" v-if="!collapsed">Activity Log</span>
        <span class="sr-only" v-else>Activity Log</span>
      </router-link>
    </nav>

    <!-- Footer -->
    <div class="footer">
      <div class="user" :title="userLabel">
        <div class="avatar" aria-hidden="true">{{ initials }}</div>

        <div class="user-meta" v-if="!collapsed">
          <div class="user-name">{{ userLabel }}</div>
          <div class="user-email">{{ user?.email || '' }}</div>
        </div>
      </div>

      <button @click="logout" class="logout-btn" type="button">
        <span v-if="!collapsed">Logout</span>
        <span class="sr-only" v-else>Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const props = defineProps({
  title: { type: String, default: 'Dashboard' },
  user: { type: Object, required: true },
})

const collapsed = ref(false)

function csrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

async function logout() {
  await fetch('/logout', {
    method: 'POST',
    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
  })
  window.location.href = '/login'
}

const userLabel = computed(() => props.user.username || props.user.name || props.user.email || 'User')

const initials = computed(() => {
  const s = String(userLabel.value || '').trim()
  if (!s) return 'U'
  const parts = s.split(/\s+/).slice(0, 2)
  return parts.map(p => p[0]?.toUpperCase() || '').join('') || 'U'
})

function isActive(path) {
  // aktif jika path persis, atau path adalah prefix (kalau nanti ada subpage)
  return route.path === path || route.path.startsWith(path + '/')
}
</script>

<style scoped>
/* A11y helper */
.sr-only{
  position:absolute;
  width:1px;height:1px;
  padding:0;margin:-1px;
  overflow:hidden;clip:rect(0,0,0,0);
  white-space:nowrap;border:0;
}

/* Sidebar base */
.sidebar{
  position: sticky;
  top: 0;
  height: 100vh;
  width: 268px;
  flex: 0 0 268px;

  display: flex;
  flex-direction: column;
  gap: 14px;

  padding: 16px 14px;

  background: rgba(255,255,255,0.92);
  border-right: 1px solid rgba(15,23,42,0.10);
  box-shadow: 10px 0 22px rgba(15,23,42,0.05);

  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

/* Collapsed state */
.sidebar[data-collapsed="true"]{
  width: 84px;
  flex-basis: 84px;
  padding: 16px 10px;
}

/* Brand header */
.brand{
  position: relative;
  display: flex;
  gap: 12px;
  align-items: center;

  padding: 12px 12px;
  border-radius: 16px;

  background: rgba(233,30,99,0.06);
  border: 1px solid rgba(233,30,99,0.16);
}

.brand::before{
  content:"";
  position:absolute;
  left:0; top:0;
  width:100%;
  height:4px;
  border-radius: 16px 16px 0 0;
  background: linear-gradient(135deg, var(--brand-900), var(--brand-600));
}

.brand-mark{
  width: 34px;
  height: 34px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--brand-900), var(--brand-600));
  box-shadow: 0 10px 18px rgba(142,42,106,0.18);
  flex: 0 0 auto;
}

.brand-text{
  min-width: 0;
}

.brand-title{
  font-weight: 950;
  letter-spacing: .2px;
  color: rgba(15,23,42,0.9);
  line-height: 1.1;
}

.brand-subtitle{
  font-size: 12.5px;
  font-weight: 750;
  color: rgba(15,23,42,0.55);
  margin-top: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 155px;
}

/* Collapse button */
.collapse-btn{
  margin-left: auto;
  border: 1px solid rgba(15,23,42,0.10);
  background: rgba(255,255,255,0.78);
  color: rgba(15,23,42,0.72);
  border-radius: 12px;
  padding: 8px 9px;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}

.collapse-btn:hover{
  transform: translateY(-1px);
  border-color: rgba(233,30,99,0.25);
  box-shadow: 0 10px 18px rgba(15,23,42,0.08);
}

.collapse-btn:active{
  transform: translateY(0);
}

/* Menu */
.menu{
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 4px 2px;
}

.menu-item{
  display: flex;
  align-items: center;
  gap: 10px;

  padding: 10px 12px;
  border-radius: 14px;
  text-decoration: none;

  color: rgba(15,23,42,0.78);
  font-weight: 850;
  font-size: 13px;

  border: 1px solid rgba(15,23,42,0.10);
  background: #fff;

  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease, background .15s ease;
}

.sidebar[data-collapsed="true"] .menu-item{
  justify-content: center;
  padding: 12px 10px;
}

.menu-item:hover{
  transform: translateY(-1px);
  box-shadow: 0 10px 18px rgba(15,23,42,0.08);
  border-color: rgba(233,30,99,0.25);
}

.menu-item .dot{
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: rgba(15,23,42,0.18);
  flex: 0 0 auto;
}

.menu-item.active{
  background: rgba(233,30,99,0.08);
  border-color: rgba(233,30,99,0.26);
  color: rgba(15,23,42,0.88);
}

.menu-item.active .dot{
  background: linear-gradient(135deg, var(--brand-900), var(--brand-600));
}

.label{
  white-space: nowrap;
}

/* Footer user area */
.footer{
  margin-top: auto;
  display: grid;
  gap: 10px;
  padding-top: 10px;
  border-top: 1px solid rgba(15,23,42,0.08);
}

.user{
  display: flex;
  gap: 10px;
  align-items: center;
  padding: 10px 10px;
  border-radius: 16px;
  background: rgba(15,23,42,0.02);
  border: 1px solid rgba(15,23,42,0.08);
}

.sidebar[data-collapsed="true"] .user{
  justify-content: center;
}

.avatar{
  width: 36px;
  height: 36px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  color: #fff;
  font-weight: 900;
  font-size: 12px;
  background: linear-gradient(135deg, var(--brand-900), var(--brand-600));
  box-shadow: 0 10px 18px rgba(142,42,106,0.16);
  flex: 0 0 auto;
}

.user-meta{
  min-width: 0;
}

.user-name{
  font-size: 13px;
  font-weight: 900;
  color: rgba(15,23,42,0.88);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 170px;
}

.user-email{
  margin-top: 2px;
  font-size: 12px;
  font-weight: 650;
  color: rgba(15,23,42,0.55);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 170px;
}

/* Logout */
.logout-btn{
  width: 100%;
  border: none;
  color: #fff;
  background: linear-gradient(135deg, var(--brand-900), var(--brand-600));
  padding: 10px 12px;
  border-radius: 14px;
  cursor: pointer;
  font-weight: 900;
  font-size: 13px;
  box-shadow: 0 12px 22px rgba(142,42,106,0.16);
  transition: transform .16s ease, box-shadow .16s ease, filter .16s ease;
}

.logout-btn:hover{
  transform: translateY(-1px);
  box-shadow: 0 16px 28px rgba(142,42,106,0.20);
  filter: brightness(1.02);
}

.logout-btn:active{
  transform: translateY(0);
  filter: brightness(0.98);
}

/* Mobile: sidebar becomes topbar */
@media (max-width: 860px){
  .sidebar{
    position: sticky;
    top: 0;
    height: auto;
    width: 100%;
    flex: none;
    border-right: none;
    border-bottom: 1px solid rgba(15,23,42,0.10);
    box-shadow: 0 10px 22px rgba(15,23,42,0.06);
  }

  .sidebar[data-collapsed="true"]{
    width: 100%;
    flex-basis: auto;
    padding: 14px 12px;
  }

  .menu{
    flex-direction: row;
    flex-wrap: wrap;
    gap: 10px;
  }

  .menu-item{
    flex: 1;
    justify-content: center;
  }

  .sidebar[data-collapsed="true"] .menu-item{
    flex: 1;
  }

  .user-name, .user-email{
    max-width: 260px;
  }
}
</style>
