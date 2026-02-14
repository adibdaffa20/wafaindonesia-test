<template>
  <div class="page">
    
    <main class="shell">
      <header class="hero">
        <div class="badge">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
          </svg>
          Wafa Indonesia
        </div>
        <h1 class="hero-title">Form Pendaftaran</h1>
        <p class="hero-subtitle">
          Silakan isi data berikut. Data akan masuk ke dashboard admin untuk diproses.
        </p>
      </header>

      <section class="card">
        <form @submit.prevent="submitForm" class="form">
          <div class="field">
            <label>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              Nama
            </label>
            <input 
              type="text" 
              v-model.trim="form.nama" 
              placeholder="Nama lengkap"
              :class="{ 'has-error': errors.nama }"
            />
            <small v-if="errors.nama" class="err">{{ errors.nama }}</small>
          </div>

          <div class="field">
            <label>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              No. WhatsApp
            </label>
            <input 
              type="text" 
              v-model.trim="form.no_wa" 
              placeholder="contoh: 08123456789"
              :class="{ 'has-error': errors.no_wa }"
            />
            <small v-if="errors.no_wa" class="err">{{ errors.no_wa }}</small>
          </div>

          <div class="field">
            <label>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              Email
            </label>
            <input 
              type="email" 
              v-model.trim="form.email" 
              placeholder="contoh: kamu@email.com"
              :class="{ 'has-error': errors.email }"
            />
            <small v-if="errors.email" class="err">{{ errors.email }}</small>
          </div>

          <div class="field">
            <label>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
              </svg>
              Nama Lembaga
            </label>
            <input 
              type="text" 
              v-model.trim="form.nama_lembaga" 
              placeholder="Nama lembaga/instansi"
              :class="{ 'has-error': errors.nama_lembaga }"
            />
            <small v-if="errors.nama_lembaga" class="err">{{ errors.nama_lembaga }}</small>
          </div>

          <!-- CAPTCHA -->
        <div class="field captcha-field">
          <div class="captcha-box">
            <div 
              class="g-recaptcha"
              data-sitekey="6LegKmssAAAAACggy-SuXH8LJMNC5DAf-hMc1mGd">
            </div>
          </div>
          <small v-if="errors.captcha" class="err">{{ errors.captcha }}</small>
        </div>


          <button class="btn" type="submit" :disabled="loading">
            <span v-if="!loading" class="btn-content">
              <span>Daftar Sekarang</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M5 12h14M12 5l7 7-7 7"/>
              </svg>
            </span>
            <span v-else class="btn-loading">
              <span class="spinner"></span>
              Mengirim...
            </span>
          </button>

          <transition name="fade">
            <div v-if="message" class="alert success">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
              </svg>
              {{ message }}
            </div>
          </transition>

          <transition name="fade">
            <div v-if="serverError" class="alert error">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
              </svg>
              {{ serverError }}
            </div>
          </transition>

        </form>
      </section>

      <footer class="footer">
        <div class="footer-line"></div>
        © {{ new Date().getFullYear() }} Wafa Indonesia • All rights reserved
      </footer>
    </main>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'

const form = reactive({
  nama: '',
  no_wa: '',
  email: '',
  nama_lembaga: '',
  captcha: ''
})

const errors = reactive({
  nama: '',
  no_wa: '',
  email: '',
  nama_lembaga: ''
})

const loading = ref(false)
const message = ref('')
const serverError = ref('')

function resetErrors() {
  errors.nama = ''
  errors.no_wa = ''
  errors.email = ''
  errors.nama_lembaga = ''
}

function csrf() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

async function submitForm() {
  loading.value = true
  message.value = ''
  serverError.value = ''
  resetErrors()
  // ambil token recaptcha
  form.captcha = grecaptcha.getResponse()

  if (!form.captcha) {
    errors.captcha = 'Silakan centang captcha terlebih dahulu.'
    loading.value = false
    return
  }
  grecaptcha.reset()

  try {
    const response = await fetch('/leads', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf(),
      },
      body: JSON.stringify(form),
    })

    if (response.status === 422) {
      const data = await response.json()
      const v = data.errors || {}
      errors.nama = v.nama?.[0] || ''
      errors.no_wa = v.no_wa?.[0] || ''
      errors.email = v.email?.[0] || ''
      errors.nama_lembaga = v.nama_lembaga?.[0] || ''
      return
    }

    if (!response.ok) {
      const text = await response.text()
      serverError.value = 'Terjadi kesalahan: ' + text
      return
    }

    const data = await response.json()
    message.value = data.message || 'Pendaftaran berhasil!'

    form.nama = ''
    form.no_wa = ''
    form.email = ''
    form.nama_lembaga = ''
  } catch (e) {
    serverError.value = 'Tidak bisa terhubung ke server.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* ====== Page Background (Fullscreen) ====== */
.page {
  min-height: 100vh;
  width: 100%;
  display: grid;
  place-items: center;
  padding: 40px 20px;

  position: relative;   /* ✅ ubah dari fixed */
  overflow-x: hidden;   /* hanya cegah scroll horizontal */

  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

  background:
    linear-gradient(
      135deg,
      rgba(107,27,78,0.88) 0%,
      rgba(142,42,106,0.88) 35%,
      rgba(194,24,91,0.88) 70%,
      rgba(233,30,99,0.88) 100%
    ),
    url('/assets/bgform.png');

  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed; /* optional cinematic */
}

/* ====== Layout ====== */
.shell {
  width: 100%;
  max-width: 520px;
  position: relative;
  z-index: 1;
  margin: auto;
  animation: slideUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
}

@keyframes slideUp {
  from { 
    opacity: 0; 
    transform: translateY(30px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

/* ====== Hero ====== */
.hero {
  text-align: center;
  color: #fff;
  margin-bottom: 28px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 10px 18px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  font-weight: 700;
  font-size: 13px;
  letter-spacing: 0.3px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  animation: fadeInDown 0.6s ease-out;
}

.hero-title {
  margin: 20px 0 0;
  font-size: 36px;
  line-height: 1.15;
  letter-spacing: -0.8px;
  font-weight: 900;
  text-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  animation: fadeInDown 0.6s ease-out 0.1s both;
}

.hero-subtitle {
  margin: 12px auto 0;
  opacity: 0.95;
  line-height: 1.6;
  max-width: 46ch;
  font-size: 15px;
  animation: fadeInDown 0.6s ease-out 0.2s both;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ====== Card ====== */
.card {
  border-radius: 28px;
  background: #ffffff;
  box-shadow: 
    0 40px 100px rgba(0, 0, 0, 0.35),
    0 0 0 1px rgba(255, 255, 255, 0.1) inset;
  backdrop-filter: blur(10px);
  animation: fadeInUp 0.6s ease-out 0.3s both;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form {
  padding: 40px 36px;
}

/* ====== Fields ====== */
.field {
  margin-bottom: 22px;
}

.field label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 600;
  margin-bottom: 10px;
  color: #1e293b;
  font-size: 14px;
}

.field label svg {
  opacity: 0.6;
}

.field input {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid rgba(15, 23, 42, 0.08);
  border-radius: 16px;
  outline: none;
  background: rgba(248, 250, 252, 0.5);
  font-size: 15px;
  color: #1e293b;
  transition: all 0.2s ease;
  font-family: inherit;
}

.field input::placeholder {
  color: rgba(15, 23, 42, 0.4);
}

.field input:focus {
  border-color: #E91E63;
  background: #ffffff;
  box-shadow: 
    0 0 0 4px rgba(233, 30, 99, 0.1),
    0 4px 12px rgba(233, 30, 99, 0.15);
  transform: translateY(-1px);
}

.field input.has-error {
  border-color: rgba(239, 68, 68, 0.8);
  background: rgba(254, 242, 242, 0.9);
}

.err {
  display: block;
  margin-top: 8px;
  color: #dc2626;
  font-size: 13px;
  font-weight: 600;
}

.captcha-field {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.captcha-box {
  transform: scale(0.95);
  /* transform-origin: center; */
}


/* ====== Button ====== */
.btn {
  width: 100%;
  margin-top: 8px;
  padding: 15px;
  border: none;
  border-radius: 16px;
  cursor: pointer;
  font-weight: 700;
  font-size: 15px;
  color: #fff;
  background: linear-gradient(135deg, #8E2A6A 0%, #E91E63 100%);
  box-shadow: 
    0 4px 15px rgba(142, 42, 106, 0.25),
    0 8px 25px rgba(233, 30, 99, 0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  font-family: inherit;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.btn:hover::before {
  left: 100%;
}

.btn:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 
    0 8px 25px rgba(142, 42, 106, 0.35),
    0 12px 35px rgba(233, 30, 99, 0.3);
}

.btn:active:not(:disabled) {
  transform: translateY(-1px);
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-content,
.btn-loading {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  position: relative;
  z-index: 1;
}

.btn-content svg {
  transition: transform 0.3s ease;
}

.btn:hover:not(:disabled) .btn-content svg {
  transform: translateX(4px);
}

/* Spinner */
.spinner {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2.5px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  animation: spin 0.7s linear infinite;
}

@keyframes spin { 
  to { transform: rotate(360deg); } 
}

/* ====== Alerts ====== */
.alert {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-top: 20px;
  padding: 14px 16px;
  border-radius: 16px;
  font-size: 14px;
  line-height: 1.5;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.alert.success {
  background: linear-gradient(135deg, rgba(240, 253, 244, 0.95), rgba(220, 252, 231, 0.95));
  border: 1.5px solid rgba(134, 239, 172, 0.9);
  color: #166534;
}

.alert.error {
  background: linear-gradient(135deg, rgba(254, 242, 242, 0.95), rgba(254, 226, 226, 0.95));
  border: 1.5px solid rgba(252, 165, 165, 0.95);
  color: #991b1b;
}

/* Fade transition */
.fade-enter-active, 
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from { 
  opacity: 0; 
  transform: translateY(-10px); 
}

.fade-leave-to { 
  opacity: 0; 
  transform: translateY(10px); 
}

/* ====== Footer ====== */
.footer {
  margin-top: 24px;
  text-align: center;
  color: rgba(255, 255, 255, 0.95);
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.2px;
  animation: fadeInUp 0.6s ease-out 0.4s both;
}

.footer-line {
  width: 72px;
  height: 3px;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
  margin: 0 auto 12px;
  border-radius: 999px;
}

/* ====== Responsive ====== */
@media (max-width: 480px) {
  .page { 
    padding: 28px 16px; 
  }
  
  .hero-title { 
    font-size: 30px; 
  }
  
  .hero-subtitle {
    font-size: 14px;
  }
  
  .form { 
    padding: 32px 24px; 
  }
  
  .field input { 
    padding: 13px 14px; 
    font-size: 14px; 
  }
  
  .btn {
    padding: 14px;
    font-size: 14px;
  }
}
</style>