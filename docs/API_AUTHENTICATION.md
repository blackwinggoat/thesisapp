# Thesis Apps API Authentication v1

Dokumen ini adalah kontrak integrasi untuk aplikasi lain yang perlu login
melalui Thesis Apps. Gunakan endpoint versi v1; route API lama di luar
/api/v1 bukan bagian dari kontrak integrasi baru.

## Alamat produksi

~~~text
API_BASE_URL=https://thesis.fikom.app/api/v1
~~~

Gunakan hostname HTTPS tersebut, bukan IP address langsung. IP hosting dapat
berubah atau berada di belakang proxy/CDN. Jika server aplikasi lain
memerlukan whitelist firewall berbasis IP, minta tim hosting memvalidasi IP
produksi pada saat konfigurasi; jangan menanam IP tersebut sebagai alamat API
di source code.

Base URL lokal:

~~~text
http://127.0.0.1:8001/api/v1
~~~

## Informasi yang harus disiapkan aplikasi pemakai

Sebelum integrasi, kirimkan informasi berikut kepada pengelola Thesis Apps:

1. Nama aplikasi pemakai, misalnya Portal Akademik.
2. Nama teknis client yang akan dikirim sebagai client_name.
3. Nama penanggung jawab dan kontak teknis.
4. Apakah request berasal dari backend server atau langsung dari browser.
5. Jika langsung dari browser, domain/origin resmi aplikasi yang akan
   diizinkan. Integrasi server-to-server tidak membutuhkan CORS.
6. User Thesis Apps khusus untuk integrasi dan role yang dibutuhkan. Jangan
   memakai akun pribadi jika aplikasi akan berjalan otomatis.

Thesis Apps tidak membutuhkan callback URL, database aplikasi pemakai, atau
akses SSH/cPanel aplikasi pemakai untuk endpoint autentikasi ini.

## Login

~~~http
POST /auth/login
Content-Type: application/json
Accept: application/json
~~~

Request:

~~~json
{
  "identifier": "USERNAME_ATAU_EMAIL",
  "password": "PASSWORD_USER_THESIS",
  "client_name": "NAMA_APLIKASI_PEMAKAI"
}
~~~

identifier dapat berupa username Thesis Apps atau email user. Password hanya
dikirim melalui HTTPS ke endpoint login dan tidak disimpan oleh endpoint API.

Response 200:

~~~json
{
  "success": true,
  "message": "Login API berhasil.",
  "data": {
    "access_token": "SIMPAN_TOKEN_INI_DI_SECRET_MANAGER",
    "token_type": "Bearer",
    "expires_at": "2026-09-21T00:00:00+08:00",
    "user": {
      "id": 123,
      "name": "USERNAME",
      "email": "user@example.com",
      "level": 8
    }
  }
}
~~~

Token mentah hanya dikirim pada response login. Aplikasi pemakai harus
menyimpannya di secret manager atau penyimpanan rahasia server dan tidak
menuliskannya di Git, JavaScript publik, URL, atau log.

Error umum:

- 401 invalid_credentials: identifier atau password salah.
- 422: field request tidak lengkap atau tidak valid.
- 429: terlalu banyak percobaan login. Login dibatasi 10 request per menit
  sesuai konfigurasi server.
- 500 token_generation_failed: coba kembali dan periksa log server.

## Membaca user yang sedang login

~~~http
GET /auth/me
Authorization: Bearer ACCESS_TOKEN
Accept: application/json
~~~

Response 200:

~~~json
{
  "success": true,
  "data": {
    "user": {
      "id": 123,
      "name": "USERNAME",
      "email": "user@example.com",
      "level": 8
    }
  }
}
~~~

Arti level yang saat ini digunakan Thesis Apps:

| Level | Peran |
| ---: | --- |
| 1 | Admin |
| 2 | Dekan |
| 3 | Wakil Dekan |
| 4 | Akademik Fakultas |
| 5 | Prodi |
| 6 | Akademik Prodi |
| 7 | Dosen |
| 8 | Mahasiswa |
| 9 | Keuangan Fakultas |

Angka level adalah data kompatibilitas. Aplikasi pemakai tidak boleh
mengasumsikan bahwa semua endpoint data tersedia untuk semua level.

## Logout atau pencabutan token

~~~http
POST /auth/logout
Authorization: Bearer ACCESS_TOKEN
Accept: application/json
~~~

Response 200:

~~~json
{
  "success": true,
  "message": "Token API berhasil dicabut."
}
~~~

Setelah logout, token tidak dapat dipakai lagi. Token juga memiliki masa
berlaku default 30 hari. Jumlah token aktif per user dibatasi maksimal 10;
token aktif tertua akan dicabut ketika batas itu tercapai.

## Health check

~~~http
GET /health
Accept: application/json
~~~

Endpoint ini tidak membutuhkan token dan hanya memeriksa ketersediaan API:

~~~json
{
  "success": true,
  "data": {
    "service": "thesisapps-api",
    "version": "v1",
    "status": "ok"
  }
}
~~~

## Contoh cURL

~~~bash
API_BASE_URL="https://thesis.fikom.app/api/v1"

curl --fail-with-body --silent --show-error \
  -X POST "$API_BASE_URL/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  --data '{"identifier":"USERNAME","password":"PASSWORD","client_name":"Portal Akademik"}'

curl --fail-with-body --silent --show-error \
  "$API_BASE_URL/auth/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer ACCESS_TOKEN"
~~~

## Aturan implementasi client

- Selalu memakai HTTPS produksi.
- Mengirim token pada header Authorization, bukan query string.
- Tidak mencatat password atau access token ke log.
- Menangani 401 dengan menghapus token lokal dan melakukan login ulang.
- Menangani 429 dengan backoff, bukan mengulang request terus-menerus.
- Menganggap field tambahan dapat muncul di masa depan dan mengabaikannya.
- Tidak memanggil endpoint legacy di luar /api/v1 untuk login baru.

## Batasan v1

Versi ini menyediakan autentikasi dan profil user saja. Endpoint data akademik
belum otomatis terbuka hanya karena user berhasil login. Setiap endpoint data
berikutnya harus dibuat dengan scope dan hak akses yang terpisah.
