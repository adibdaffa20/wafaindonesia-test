import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'

import DashboardApp from './DashboardApp.vue'
import Dashboard from './pages/Dashboard.vue'
import ActivityLog from './pages/ActivityLog.vue'
import '@fontsource/inter/400.css'
import '@fontsource/inter/500.css'
import '@fontsource/inter/600.css'
import '@fontsource/inter/700.css'
import '@fontsource/inter/800.css'
import '@fontsource/inter/900.css'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/dashboard/leads', component: Dashboard },
    { path: '/dashboard/activity-log', component: ActivityLog },
    { path: '/:pathMatch(.*)*', redirect: '/dashboard/leads' },
  ],
})

createApp(DashboardApp).use(router).mount('#app')
