# Stage 2 — Autentikasi, Role, Profil (+ perbaikan hasil review Stage 0–1)

**Branch:** `stage-02-auth` (lihat PRD §0 "Git Workflow", buat branch SEBELUM mengubah apa pun).
**Tujuan:** User dan Admin bisa register/login/logout dengan token Sanctum, akses dibatasi per role, User bisa melihat dan mengubah profil. Ditambah perbaikan kecil dari review Stage 0–1.

## A. MANUAL (user) — sebelum menjalankan agent

1. Pastikan `PRD.md` dan file ini sudah di-commit ke `main` (docs saja), lalu `git checkout main && git pull`.
2. Tambahkan ke `.env` lokal (nilai bebas, jangan memakai kata sandi yang kamu pakai di tempat lain):
   ```
   SEED_ADMIN_PASSWORD=
   SEED_USER_PASSWORD=
   SANCTUM_TOKEN_EXPIRATION=10080
   ```
3. (Opsional tapi disarankan) `AGENTS.md` dan `CLAUDE.md` bawaan starter Laravel bisa bentrok dengan PRD (mis. menyuruh memasang `laravel/boost`). Ganti isi keduanya dengan:
   ```
   Baca PRD.md dan file stage aktif di docs/stages/. Ikuti §0 PRD.md (terutama Git Workflow). Jangan memasang paket di luar yang disebut stage.
   ```
4. Jalankan agent dengan prompt: *"Baca PRD.md dan docs/stages/stage-02-auth.md, lalu kerjakan hanya stage itu."*

## B. AGENT — Tugas 0: perbaikan hasil review (kerjakan dulu, satu commit per butir)

- **0.1 Health check.** Pindahkan closure `/api/v1/health` ke `HealthController`. Jangan tampilkan pesan error DB kecuali `APP_ENV` = `local`/`testing`. Jika DB tidak terhubung: status `unhealthy`, HTTP **503**. Perbarui test.
- **0.2 `ApiResponse::paginated`.** Ubah agar menerima class Resource: `paginated(LengthAwarePaginator $p, string $resourceClass, string $message = 'Success')`, dan isi `data` dari `$resourceClass::collection($p->getCollection())->resolve()`. Meta tetap sama. Tambah 1 unit test (atribut yang tidak ada di Resource tidak boleh muncul).
- **0.3 Seeder.**
  - Data dummy (kategori, penjual, produk, user, alamat) hanya di-seed jika `app()->environment(['local','testing'])`.
  - Admin: `updateOrCreate`, kata sandi dari `SEED_ADMIN_PASSWORD`; di luar `local`/`testing` env ini wajib terisi (jika kosong lempar exception berpesan jelas). Di `local`/`testing` default boleh `password`.
  - Kata sandi user dummy dari `SEED_USER_PASSWORD` (default `password` hanya di `local`/`testing`).
  - Tambahkan variabel itu ke `.env.example` (kosong).
- **0.4 Migration.** Ubah `riwayat_pengiriman.keterangan` dan `riwayat_retur.keterangan` menjadi `nullable` (edit file migration yang ada; DB belum dipakai di produksi, user menjalankan `migrate:fresh --seed`).
- **0.5 Relasi `Admin`.** Tambahkan `hasMany`: `pengajuanProduk` (id_admin), `pengiriman` (id_admin), `retur` (admin_id), `bantuan` (id_admin). Tambahkan ke test relasi satu assert.
- **0.6 PR template.** Buat `.github/pull_request_template.md` berisi bagian: Ringkasan, Perubahan utama, Cara menguji, Checklist DoD (salin dari PRD §10), Catatan untuk reviewer.
- Jangan menyentuh `AGENTS.md` dan `CLAUDE.md` (urusan user).

## C. AGENT — Tugas utama: autentikasi

**Keputusan** (PRD §2 D8): dua model terpisah `Admin` dan `User`, token Sanctum dengan ability `admin` atau `user`.

| Method | Path | Akses | Keterangan |
|---|---|---|---|
| POST | `/api/v1/auth/register` | publik | nama, email, no_hp, password (min 8, `password_confirmation`). Email dinormalisasi lowercase dan unik di tabel `user`. Respons 201 berisi data user, **tanpa token** (User lalu login). Alamat diatur di Stage 4. |
| POST | `/api/v1/auth/login` | publik, throttle | Token ability `user`. Kredensial salah → 401 `Email atau kata sandi salah`. `status_akun` nonaktif → 403 `Akun tidak aktif`. |
| POST | `/api/v1/auth/logout` | `role.user` | Mencabut token yang sedang dipakai. |
| GET | `/api/v1/profile` | `role.user` | Data profil User. |
| PUT | `/api/v1/profile` | `role.user` | Ubah `nama` dan `no_hp`. Email tidak dapat diubah. |
| PUT | `/api/v1/profile/password` | `role.user` | `password_lama`, `password` baru + konfirmasi. Setelah berhasil, cabut semua token lain milik User. |
| POST | `/api/v1/admin/auth/login` | publik, throttle | Token ability `admin`. Pesan gagal sama dengan login User. |
| POST | `/api/v1/admin/auth/logout` | `role.admin` | Mencabut token saat ini. |
| GET | `/api/v1/admin/auth/me` | `role.admin` | Data Admin yang login. |

Ketentuan implementasi:

1. Middleware alias `role.user` dan `role.admin` (daftarkan di `bootstrap/app.php`), sudah mencakup `auth:sanctum`. `role.user`: pengguna harus instance `User`, token punya ability `user`, dan `status_akun` aktif (jika tidak → 403). `role.admin`: instance `Admin` dengan ability `admin`. Tanpa token → 401, role salah → 403 (envelope standar via `ApiResponse`).
2. Rate limiter bernama `login`: 5 percobaan per menit per kombinasi email + IP. Berlebih → 429 (handler yang sudah ada).
3. Masa berlaku token dari `SANCTUM_TOKEN_EXPIRATION` (menit) via `config/sanctum.php`.
4. Logika di `app/Services/AuthService` (register, login, logout, ubah kata sandi). Controller tipis. Input via Form Request, output via `UserResource` dan `AdminResource` (tanpa `password`, `remember_token`). Respons login berisi `token`, `token_type`, dan objek user/admin.
5. Catat `log_aktivitas` untuk: login admin berhasil, login admin gagal, ubah kata sandi User.
6. Pesan error dalam bahasa Indonesia.

## D. AGENT — Verifikasi dokumentasi (Scramble)

Setelah endpoint login selesai, buka `/docs/api` (atau periksa lewat perintah Scramble) dan pastikan isi `data` pada respons login dan profile **terbaca** (field user, token). Jika `data` tampil generik karena helper `ApiResponse`:
- pilih cara paling sederhana yang didukung Scramble (mis. docblock `@response` atau pola Resource dengan envelope), terapkan ke endpoint Stage 2,
- catat keputusannya sebagai **D9** di `docs/DECISIONS.md`,
- jangan membangun sistem anotasi manual yang besar.

## E. Test (feature test)

Register sukses dan gagal (email duplikat, password lemah); login sukses/gagal/akun nonaktif; throttle login; logout mencabut token (token lama → 401); `role.user` menolak token admin (403) dan sebaliknya; tanpa token → 401; update profil; ubah kata sandi (lama salah → 422; sukses mencabut token lain); `/health` (Tugas 0.1); paginated (Tugas 0.2).

## F. Definition of Done

- [ ] Dikerjakan di branch `stage-02-auth`, branch sudah di-push.
- [ ] Semua butir Tugas 0 selesai (0.1–0.6).
- [ ] Semua endpoint pada tabel C berfungsi dan tampil di `/docs/api` dengan skema `data` yang benar (D).
- [ ] Test pada bagian E lulus.
- [ ] Status Stage 2 → `DONE` di PRD §9, laporan singkat + Draft PR diberikan.

## G. Setelah agent selesai — MANUAL (user)

1. `git checkout stage-02-auth && git pull`, lalu `php artisan migrate:fresh --seed` dan `php artisan test`.
2. `php artisan serve`, buka `/docs/api`. Coba login admin dan user lewat "Try it" atau Postman, cek `data` pada respons. Coba panggil `/api/v1/profile` dengan token admin (harus 403).
3. Di GitHub: buat **Pull Request** dari `stage-02-auth` ke `main`, tempel Draft PR dari laporan agent, baca tab *Files changed*.
4. (Opsional) kirim nomor PR ke chat untuk direview sebelum merge.
5. Jika sudah oke: **Merge**, lalu `git checkout main && git pull` dan hapus branch lokal (`git branch -d stage-02-auth`). Baru minta stage berikutnya.
