# Stage 0 — Fondasi

**Tujuan:** project siap dikembangkan: paket terpasang, konfigurasi dasar, format respons seragam, error selalu JSON, dokumentasi API aktif.

## A. MANUAL (user) — kerjakan SEBELUM menjalankan agent

Semua ini murah dikerjakan sendiri dan hemat token.

1. Pastikan PHP, Composer, Git, dan DBMS terpasang (mis. Laragon). Cek `php -v` memenuhi syarat di `composer.json`.
2. Buat database kosong, mis. `schooly_shop`.
3. Edit `.env`:
   ```
   APP_TIMEZONE=Asia/Jakarta
   DB_CONNECTION=mysql
   DB_DATABASE=schooly_shop
   DB_USERNAME=...
   DB_PASSWORD=...
   QUEUE_CONNECTION=database
   MAIL_MAILER=log
   FILESYSTEM_DISK=public
   ```
4. Jalankan di terminal:
   ```
   php artisan install:api
   composer require dedoc/scramble
   php artisan storage:link
   git init && git add . && git commit -m "init laravel"
   ```
5. Daftar akun **sandbox** Midtrans dan **Biteship** (Testing Mode). Simpan key di `.env` (jangan dikirim ke agent atau chat):
   ```
   MIDTRANS_SERVER_KEY=
   MIDTRANS_CLIENT_KEY=
   MIDTRANS_IS_PRODUCTION=false
   BITESHIP_API_KEY=
   BITESHIP_BASE_URL=
   SCHOOL_ORIGIN_AREA_ID=
   SCHOOL_ORIGIN_NAME=
   SCHOOL_ORIGIN_PHONE=
   SCHOOL_ORIGIN_ADDRESS=
   SCHOOL_ORIGIN_POSTAL=
   ```
   `SCHOOL_ORIGIN_AREA_ID` bisa diisi nanti (stage 5) setelah mencari area sekolah lewat API area Biteship.
6. (Boleh ditunda sampai stage 6) pasang tunnel gratis (Cloudflare Tunnel atau ngrok) untuk webhook.

## B. AGENT — tugas

1. Verifikasi `laravel/sanctum` dan `dedoc/scramble` sudah terpasang. Jangan pasang paket lain.
2. Atur Scramble: dokumentasi tersedia di `/docs/api`, hanya aktif di environment non-production.
3. Buat `config/schooly.php` dengan: `payment_expiry_minutes=60`, `auto_confirm_hours=48`, `return_window_hours=48`, `origin` (area_id, nama, telepon, alamat, kode pos, dibaca dari env), dan `config/services.php` entri `midtrans` dan `biteship` (dibaca dari env).
4. Buat `app/Support/ApiResponse.php` (helper `success`, `error`, `paginated`) sesuai envelope di PRD §3.
5. Di `bootstrap/app.php`: semua request `api/*` selalu dirender JSON. Seragamkan error: 401, 403, 404, 405, 422 (dengan `errors`), 429, dan 500 (pesan generik di production, tanpa stack trace).
6. Buat `routes/api.php` dengan prefix `v1` dan endpoint `GET /api/v1/health` yang mengembalikan status aplikasi dan koneksi database.
7. Buat folder kosong terstruktur sesuai PRD §3 (`Services/External`, `Enums`, `Http/Requests`, `Http/Resources`) dengan `.gitkeep`.
8. Buat `docs/DECISIONS.md` (judul saja + tabel D1–D8 disalin dari PRD §2).
9. Perbarui `.env.example` dengan semua variabel di bagian A (tanpa nilai rahasia).
10. Satu feature test untuk `/api/v1/health` dan satu test bahwa route tak dikenal mengembalikan JSON 404 (bukan HTML).

## C. Definition of Done

- [ ] `GET /api/v1/health` mengembalikan envelope sukses.
- [ ] Request ke route yang tidak ada di `/api/*` mengembalikan JSON 404.
- [ ] `/docs/api` terbuka dan menampilkan endpoint health.
- [ ] 2 test lulus, `.env.example` terisi, tidak ada secret di commit.
- [ ] Status Stage 0 → `DONE` dan Stage 1 → `IN PROGRESS` di PRD §9.

## D. Setelah agent selesai — MANUAL (user)

1. `php artisan serve`, buka `/api/v1/health` dan `/docs/api`.
2. Jika oke, `git log` untuk memastikan commit stage ada, lalu minta stage berikutnya.
