# Integrasi API Thesis Apps untuk Curriculum App / OBE App FIKOM

Dokumen ini adalah pegangan teknis untuk menghubungkan Curriculum App / OBE
App FIKOM ke API login Thesis Apps. Integrasi yang dipakai adalah
server-to-server, sehingga request API dilakukan dari backend Curriculum App,
bukan langsung dari browser pengguna.

## Ringkasan Integrasi

| Item | Nilai |
| --- | --- |
| Nama aplikasi pemakai | Curriculum App / OBE App FIKOM |
| Nama teknis client | curriculum-app-obe-fikom |
| Penanggung jawab teknis | Huzain Azis |
| Email kontak | huzain.azis@umi.ac.id |
| Telepon/WA kontak | 08114484875 |
| Mode integrasi | Server-to-server |
| Role Thesis Apps yang dipakai | Dosen |
| Base URL produksi | https://thesis.fikom.app/api/v1 |
| Format autentikasi | Bearer token |
| Masa berlaku token default | 30 hari |

Gunakan hostname `https://thesis.fikom.app`, bukan IP address langsung. IP
hosting dapat berubah. Whitelist IP server Curriculum App belum diwajibkan pada
kontrak API ini karena proteksi utama memakai HTTPS, kredensial user Thesis
Apps, dan Bearer token.

## Credential Yang Harus Disiapkan

Credential tidak ditulis di dokumen ini. Simpan credential di secret manager
atau file environment backend Curriculum App.

Pengelola Thesis Apps perlu menyediakan satu akun khusus integrasi dengan role
Dosen:

```text
THESIS_API_IDENTIFIER=ISI_USERNAME_ATAU_EMAIL_AKUN_DOSEN_KHUSUS
THESIS_API_PASSWORD=ISI_PASSWORD_AKUN_DOSEN_KHUSUS
THESIS_API_CLIENT_NAME=curriculum-app-obe-fikom
THESIS_API_BASE_URL=https://thesis.fikom.app/api/v1
```

Rekomendasi keamanan:

- Gunakan akun khusus integrasi, bukan akun pribadi dosen.
- Akun harus memiliki role `Dosen` atau `level = 7`.
- Password dan access token tidak boleh disimpan di Git, JavaScript publik,
  URL, screenshot, atau log aplikasi.
- Jika akun integrasi diganti password, Curriculum App harus login ulang untuk
  mendapatkan token baru.

## Endpoint Yang Tersedia

### 1. Health Check

Dipakai untuk mengecek apakah API Thesis Apps tersedia. Endpoint ini tidak
membutuhkan token.

```http
GET /health
Accept: application/json
```

Contoh response:

```json
{
  "success": true,
  "data": {
    "service": "thesisapps-api",
    "version": "v1",
    "status": "ok"
  }
}
```

### 2. Login API

Dipakai backend Curriculum App untuk mendapatkan Bearer token.

```http
POST /auth/login
Accept: application/json
Content-Type: application/json
```

Request body:

```json
{
  "identifier": "ISI_USERNAME_ATAU_EMAIL_AKUN_DOSEN_KHUSUS",
  "password": "ISI_PASSWORD_AKUN_DOSEN_KHUSUS",
  "client_name": "curriculum-app-obe-fikom"
}
```

Contoh response berhasil:

```json
{
  "success": true,
  "message": "Login API berhasil.",
  "data": {
    "access_token": "TOKEN_RAHASIA_DARI_THESIS_APPS",
    "token_type": "Bearer",
    "expires_at": "2026-09-21T00:00:00+08:00",
    "user": {
      "id": 123,
      "name": "USERNAME_DOSEN",
      "email": "dosen@example.com",
      "level": 7
    }
  }
}
```

Validasi wajib di Curriculum App:

- Pastikan `success = true`.
- Simpan `data.access_token` secara rahasia di backend.
- Pastikan `data.user.level = 7`. Jika bukan 7, tolak sesi integrasi karena
  role yang diminta adalah Dosen.
- Gunakan `data.expires_at` untuk menentukan kapan token perlu diperbarui.

### 3. Cek Profil Token

Dipakai untuk memvalidasi token yang sedang disimpan Curriculum App.

```http
GET /auth/me
Accept: application/json
Authorization: Bearer TOKEN_RAHASIA_DARI_THESIS_APPS
```

Contoh response:

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 123,
      "name": "USERNAME_DOSEN",
      "email": "dosen@example.com",
      "level": 7
    }
  }
}
```

### 4. Logout / Cabut Token

Dipakai jika Curriculum App ingin mencabut token yang sedang digunakan.

```http
POST /auth/logout
Accept: application/json
Authorization: Bearer TOKEN_RAHASIA_DARI_THESIS_APPS
```

Contoh response:

```json
{
  "success": true,
  "message": "Token API berhasil dicabut."
}
```

Setelah endpoint ini dipanggil, token lama tidak bisa dipakai lagi.

## Contoh cURL

```bash
export THESIS_API_BASE_URL="https://thesis.fikom.app/api/v1"
export THESIS_API_IDENTIFIER="ISI_USERNAME_ATAU_EMAIL_AKUN_DOSEN_KHUSUS"
export THESIS_API_PASSWORD="ISI_PASSWORD_AKUN_DOSEN_KHUSUS"
export THESIS_API_CLIENT_NAME="curriculum-app-obe-fikom"

curl --fail-with-body --silent --show-error \
  "$THESIS_API_BASE_URL/health" \
  -H "Accept: application/json"

curl --fail-with-body --silent --show-error \
  -X POST "$THESIS_API_BASE_URL/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  --data "{
    \"identifier\":\"$THESIS_API_IDENTIFIER\",
    \"password\":\"$THESIS_API_PASSWORD\",
    \"client_name\":\"$THESIS_API_CLIENT_NAME\"
  }"

curl --fail-with-body --silent --show-error \
  "$THESIS_API_BASE_URL/auth/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_RAHASIA_DARI_THESIS_APPS"
```

## Contoh Implementasi Backend

### PHP Laravel

```php
use Illuminate\Support\Facades\Http;

$baseUrl = rtrim(env('THESIS_API_BASE_URL'), '/');

$response = Http::acceptJson()
    ->asJson()
    ->post($baseUrl . '/auth/login', [
        'identifier' => env('THESIS_API_IDENTIFIER'),
        'password' => env('THESIS_API_PASSWORD'),
        'client_name' => env('THESIS_API_CLIENT_NAME', 'curriculum-app-obe-fikom'),
    ]);

if (!$response->successful() || !$response->json('success')) {
    throw new RuntimeException('Login API Thesis Apps gagal.');
}

$token = $response->json('data.access_token');
$level = (int) $response->json('data.user.level');

if ($level !== 7) {
    throw new RuntimeException('Akun API Thesis Apps bukan role Dosen.');
}
```

### Node.js

```js
const baseUrl = process.env.THESIS_API_BASE_URL.replace(/\/$/, '');

const response = await fetch(`${baseUrl}/auth/login`, {
  method: 'POST',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    identifier: process.env.THESIS_API_IDENTIFIER,
    password: process.env.THESIS_API_PASSWORD,
    client_name: process.env.THESIS_API_CLIENT_NAME || 'curriculum-app-obe-fikom',
  }),
});

const payload = await response.json();

if (!response.ok || !payload.success) {
  throw new Error('Login API Thesis Apps gagal.');
}

if (Number(payload.data.user.level) !== 7) {
  throw new Error('Akun API Thesis Apps bukan role Dosen.');
}

const thesisAccessToken = payload.data.access_token;
```

## Error Yang Harus Ditangani

| HTTP | Kode error | Arti | Tindakan client |
| ---: | --- | --- | --- |
| 401 | invalid_credentials | Username/email atau password salah | Periksa credential integrasi |
| 401 | missing_token | Header Authorization tidak dikirim | Login ulang atau perbaiki request |
| 401 | invalid_token | Token salah atau sudah dicabut | Hapus token lokal dan login ulang |
| 401 | expired_token | Token melewati masa berlaku | Login ulang |
| 422 | validation error | Field request tidak lengkap/tidak valid | Perbaiki payload request |
| 429 | throttle | Terlalu banyak request | Gunakan backoff dan jangan retry terus-menerus |
| 500 | token_generation_failed | Token gagal dibuat di server | Coba ulang dan laporkan ke pengelola Thesis Apps |

## Batas Rate Limit

- Login: 10 request per menit.
- Endpoint yang memakai token: 60 request per menit.

Jangan melakukan login setiap request. Simpan token di backend, gunakan ulang
sampai hampir kedaluwarsa, lalu login ulang.

## Role Mapping Thesis Apps

| Level | Role |
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

Untuk Curriculum App / OBE App FIKOM, role yang diterima adalah `level = 7`.

## Catatan Keamanan

- Token adalah rahasia setara password sementara.
- Token dikirim hanya melalui header `Authorization: Bearer`.
- Jangan mengirim token melalui query string.
- Jangan menulis token atau password ke log aplikasi.
- Jangan memakai endpoint API lama di luar `/api/v1` untuk integrasi baru.
- Jika server Curriculum App ingin dibatasi lewat firewall, berikan IP public
  produksi Curriculum App kepada pengelola hosting Thesis Apps. Ini opsional
  dan belum menjadi syarat endpoint saat ini.

## Status Produksi Saat Dokumen Ini Dibuat

Endpoint produksi yang tersedia:

```text
https://thesis.fikom.app/api/v1/health
https://thesis.fikom.app/api/v1/auth/login
https://thesis.fikom.app/api/v1/auth/me
https://thesis.fikom.app/api/v1/auth/logout
```

Versi API ini hanya menyediakan autentikasi dan profil user. Endpoint data
akademik khusus Dosen, seperti daftar bimbingan atau jadwal mengajar, perlu
dibuat sebagai endpoint terpisah setelah kebutuhan data Curriculum App
ditetapkan.

## Checklist UAT Integrasi

Sebelum dipakai produksi oleh Curriculum App:

- `GET /health` mengembalikan `success = true`.
- `POST /auth/login` berhasil dengan `client_name = curriculum-app-obe-fikom`.
- Response login mengembalikan `data.user.level = 7`.
- `GET /auth/me` berhasil memakai token dari login.
- `POST /auth/logout` berhasil mencabut token.
- Token lama setelah logout mengembalikan 401.
- Password dan token sudah disimpan di secret manager atau environment server,
  bukan di source code.
