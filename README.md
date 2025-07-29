# Quasar + Laravel + JWT Auth + Swagger Starter Kit

Starter kit ini menggabungkan frontend Quasar (Vue 3 + TypeScript) dan backend
Laravel 10 menggunakan autentikasi JWT serta dokumentasi API otomatis dengan L5
Swagger. Cocok untuk pengembangan aplikasi SPA (Single Page Application) yang
skalabel dan terstruktur.

## 📦 Fitur Utama

### Backend (Laravel 10)

- Autentikasi menggunakan JWT (via `tymon/jwt-auth`)
- Dokumentasi API otomatis (via `l5-swagger`)
- Struktur modular untuk controller, request, dan response
- Middleware `auth:api` untuk proteksi endpoint
- Command artisan custom: `generate-api-docs`
- Autogenerate `openapi.json` setelah install dan update

### Frontend (Quasar + Vue 3 + TypeScript)

- Quasar SPA mode dengan Axios dan Pinia
- Modul auth dengan JWT (login, logout, refresh)
- Routing dengan middleware berbasis meta
- Layout sistematis dan extensible

## 📂 Struktur Direktori

```bash
├── backend/                 # Laravel 10 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Requests/
│   ├── config/
│   ├── routes/
│   └── app/OpenApiDocumentation.php
├── frontend/                # Quasar Project
│   ├── src/
│   │   ├── boot/
│   │   ├── pages/
│   │   ├── router/
│   │   └── stores/
├── .env
├── README.md
```

## 🚀 Cara Instalasi

### 1. Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
composer generate-api-docs
```

### 2. Frontend

```bash
cd frontend
npm install
quasar dev
```

## 🛡️ Autentikasi JWT

- Endpoint login: `POST /api/v1/auth/login`
- Endpoint logout: `POST /api/v1/auth/logout`
- Token akan dikirim via header: `Authorization: Bearer <token>`
- Gunakan middleware `auth:api` untuk melindungi route

## 📚 Dokumentasi API

- URL: `http://localhost:8000/api/v1/documentation`
- Sumber: `app/OpenApiDocumentation.php`
- Diperbarui otomatis saat `composer install` atau `composer update`

## 🧰 Perintah Artisan Khusus

```bash
composer generate-api-docs
```

Menjalankan `php artisan l5-swagger:generate` untuk update dokumentasi.

## 📝 Catatan Pengembangan

- Gunakan anotasi `@OA\Tag` dan `@OA\SecurityScheme` hanya sekali pada file
  `OpenApiDocumentation.php`
- Pastikan semua controller memiliki tag yang sesuai dan tidak duplikat
- Frontend dan backend dapat dijalankan terpisah atau digabung via proxy

---

🛠️ Dibuat oleh Developer untuk Developer. Lisensi: MIT.
