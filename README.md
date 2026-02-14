Dashboard Lead - Wafa Indonesia
Sistem Pendaftaran & Monitoring Admin

<img src="https://github.com/adibdaffa20/wafaindonesia-test/blob/main/public/assets/mobileform.jpeg?raw=true" width="300">

![alt text](https://github.com/adibdaffa20/wafaindonesia-test/blob/main/public/assets/mobileform.jpeg)
![alt text](https://github.com/adibdaffa20/wafaindonesia-test/blob/main/public/assets/admin.jpeg)
![alt text](https://github.com/adibdaffa20/wafaindonesia-test/blob/main/public/assets/dashboard.jpeg)
![alt text](https://github.com/adibdaffa20/wafaindonesia-test/blob/main/public/assets/activitylog.jpeg)


Dashboard Lead adalah sistem pendaftaran berbasis Laravel + Vue 3 yang dilengkapi dengan Dashboard Admin, Activity Log, dan proteksi Google reCAPTCHA.

📝 Landing Page Form Pendaftaran

📊 Dashboard Leads (CRUD + Search + Pagination)

📜 Activity Log (Login & Aksi Admin)

🔐 Google reCAPTCHA Protection

🎨 Modern UI (Inter + Magenta Pink Brand)

✨ Features

Form pendaftaran terintegrasi database

Dashboard SPA (Vue Router)

Logging aktivitas admin

Login rate limiting

Multi Admin Seeder

Responsive UI

Google reCAPTCHA v2 checkbox

📸 Screenshot

Simpan gambar di folder docs/screenshots/

Landing Page	Dashboard

	
Activity Log	Login

	
🏗 Tech Stack

Wafa Dashboard menggunakan beberapa teknologi berikut:

Laravel
 - Backend Framework

Vue 3
 - Frontend Framework

Vue Router
 - SPA Routing

Vite
 - Frontend Build Tool

MySQL
 - Database

Google reCAPTCHA
 - Bot Protection

[Inter Font] - Typography

📂 Project Structure
wafa-dashboard/
│
├── app/
│   ├── Http/Controllers/
│   │   ├── RegistrationController.php
│   │   └── RegistrationDashboardController.php
│   ├── Helpers/activity.php
│   └── Providers/EventServiceProvider.php
│
├── database/
│   ├── migrations/
│   └── seeders/AdminSeeder.php
│
├── resources/
│   ├── js/
│   │   ├── pages/
│   │   │   ├── Landing.vue
│   │   │   ├── Dashboard.vue
│   │   │   └── ActivityLog.vue
│   │   ├── components/Navbar.vue
│   │   ├── app.js
│   │   └── dashboard.js
│   │
│   └── views/
│       ├── home/index.blade.php
│       └── dashboard/index.blade.php
│
└── routes/web.php

🚀 Installation

Wafa Dashboard membutuhkan:

PHP 8.1+

Composer

Node.js 16+

MySQL

1️⃣ Clone Repository
git clone https://github.com/your-username/wafa-dashboard.git
cd wafa-dashboard

2️⃣ Install Dependencies
composer install
npm install

3️⃣ Setup Environment
cp .env.example .env


Edit .env:

DB_DATABASE=wafa_dashboard
DB_USERNAME=root
DB_PASSWORD=

RECAPTCHA_SITE=your_site_key
RECAPTCHA_SECRET=your_secret_key

4️⃣ Generate Key
php artisan key:generate

5️⃣ Run Migration
php artisan migrate

6️⃣ Seed Admin
php artisan db:seed --class=AdminSeeder

7️⃣ Run Project
php artisan serve
npm run dev


Open:

http://127.0.0.1:8000

🔐 Default Admin Accounts
Username	Email	Password
admindesy	admindesy@wafaindonesia.com
	Admin12345!
adminputri	adminputri@wafaindonesia.com
	Admin12345!
adminbudi	adminbudi@wafaindonesia.com
	Admin12345!
🔎 Routes Overview
Public
GET  /form
POST /leads

Dashboard (Auth Required)
GET  /dashboard/leads
GET  /dashboard/activity-log
GET  /dashboard/api/leads
GET  /dashboard/api/activity-logs
PUT  /dashboard/api/leads/{id}
DELETE /dashboard/api/leads/{id}

🛡 Security

CSRF Protection

Login Rate Limiter

Google reCAPTCHA v2

Activity Logging

IP Tracking

📦 Production Build
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

📈 Roadmap

Export CSV Leads

Advanced Filtering

Role-based Access

Dashboard Statistics

Email Notification

Dark Mode

📄 License

Internal Use Only
© Wafa Indonesia
