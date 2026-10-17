# Stage 3a — Master Data Dasar (Kategori, Penjual, Data User, Bantuan)

**Branch:** `stage-03a-master-data` (PRD §0 "Git Workflow": buat branch SEBELUM mengubah apa pun).
**Tujuan:** Admin dapat mengelola kategori dan penjual, melihat data User (read-only), dan mengatur kontak WhatsApp Bantuan. Stage 3b (pengajuan produk dan produk) dikerjakan setelah ini ter-merge.

## A. MANUAL (user) — sebelum menjalankan agent

1. Pastikan Stage 2 sudah ter-merge: `git checkout main && git pull`.
2. Commit `PRD.md` dan file ini ke `main` (docs saja), lalu push.
3. (Opsional) rapikan format kode dengan `./vendor/bin/pint` dan commit terpisah `chore: pint`. Dengan begitu diff PR stage ini tidak bercampur perubahan format.
4. Tidak ada variabel `.env` baru di stage ini.
5. Jalankan agent dengan prompt: *"Baca PRD.md dan docs/stages/stage-03a-master-data.md, lalu kerjakan hanya stage itu. Satu commit per butir tugas bernomor."*

## B. AGENT — Tugas 0: persiapan dan utilitas bersama (satu commit per butir)

- **0.1 Test email case-insensitive.** Tambah test: register dengan `A@x.com` saat `a@x.com` sudah ada → 422 `Email sudah terdaftar.` (bukan 500).
- **0.2 `ActivityLogger`.** Buat `App\Services\ActivityLogger` dengan method `log(string $aktorTipe, ?int $aktorId, string $aksi, string $deskripsi, ?array $data = null): void`, lalu ganti pemanggilan `LogAktivitas::create(...)` di `AuthService` dengan helper ini. Test auth yang ada harus tetap lulus.
- **0.3 Helper paginasi.** Buat helper untuk membaca `per_page` dari request (default 15, minimum 1, maksimum 100) agar dipakai semua endpoint daftar. Tambah satu unit test.

## C. AGENT — Tugas utama

Semua endpoint `/api/v1/admin/*` memakai middleware `role.admin`. Controller ditaruh di `app/Http/Controllers/Admin/`. Setiap endpoint memakai Form Request dan API Resource. Daftar memakai `ApiResponse::paginated`. Logika di Service. Pesan error dan validasi dalam bahasa Indonesia.

### 1. Kategori

| Method | Path | Keterangan |
|---|---|---|
| GET | `/admin/kategori` | Daftar, paginasi, filter `search` (nama). |
| POST | `/admin/kategori` | `nama_kategori`: wajib, maks 100, unik. 201. |
| GET | `/admin/kategori/{id}` | Detail. |
| PUT | `/admin/kategori/{id}` | Ubah nama (unik, abaikan diri sendiri). |
| DELETE | `/admin/kategori/{id}` | Jika dipakai `produk` atau `pengajuan_produk` → 409 `Kategori masih digunakan.` |

### 2. Penjual (tanpa DELETE, sesuai analisis: hanya tambah, ubah, nonaktifkan)

| Method | Path | Keterangan |
|---|---|---|
| GET | `/admin/penjual` | Paginasi; filter `search` (nama atau email), `jenis_penjual`, `status`. |
| POST | `/admin/penjual` | `nama`, `jenis_penjual` (enum `JenisPenjual`), `kelas_jabatan`, `email` (email valid, unik di tabel penjual, disimpan lowercase), `kontak`, `status` (default `aktif`). 201. |
| GET | `/admin/penjual/{id}` | Detail + `jumlah_produk`. |
| PUT | `/admin/penjual/{id}` | Ubah data. Jika `status` berubah menjadi `nonaktif`, dalam satu transaksi nonaktifkan SEMUA produk penjual itu (`status_aktif = false`) dan sebutkan jumlahnya di pesan respons. Mengaktifkan kembali penjual TIDAK mengaktifkan ulang produk. |

Catat `log_aktivitas` saat penjual dinonaktifkan. Route DELETE tidak boleh ada (harus 405).

### 3. Data User (read-only, hanya lihat)

| Method | Path | Keterangan |
|---|---|---|
| GET | `/admin/users` | Paginasi; filter `search` (nama, email, no_hp), `status_akun`. |
| GET | `/admin/users/{id}` | Detail + `riwayat_pesanan` (maks 20 terbaru: `id_pesanan`, `kode_pesanan`, `tgl_pesanan`, `total_bayar`, `status_pesanan`). |

Tidak boleh ada route POST/PUT/PATCH/DELETE untuk User di sisi Admin (PRD §4). Respons tidak boleh memuat `password` atau `remember_token`.

### 4. Bantuan (WhatsApp)

| Method | Path | Akses | Keterangan |
|---|---|---|---|
| GET | `/admin/bantuan` | admin | Pengaturan saat ini. |
| PUT | `/admin/bantuan` | admin | `nomor_whatsapp`, `pesan_awal`. |
| GET | `/bantuan` | publik | `nomor_whatsapp`, `pesan_awal`, `tautan_whatsapp`. Jika belum diatur → 404 `Layanan bantuan belum tersedia.` |

Aturan: normalisasi nomor (buang semua karakter non-angka; awalan `0` diganti `62`); valid jika 9–15 digit setelah normalisasi, jika tidak → 422. Server membentuk `tautan_whatsapp` = `https://wa.me/<nomor>?text=<pesan_awal di-urlencode>`. Tabel `bantuan` dianggap satu baris pengaturan (update baris pertama, atau buat jika belum ada), `id_admin` diisi Admin yang mengubah. Catat `log_aktivitas`.

## D. Test (feature test ringkas per modul)

- Semua endpoint admin: tanpa token 401, token User 403.
- Kategori: CRUD sukses, nama duplikat 422, hapus kategori yang dipakai produk 409.
- Penjual: CRUD sukses, validasi enum dan email, nonaktifkan penjual menonaktifkan semua produknya (dan tidak mengaktifkannya kembali saat penjual diaktifkan), DELETE → 405.
- Users: daftar dan detail berfungsi, tidak ada `password` pada respons, POST/PUT/PATCH/DELETE → 405 atau 404.
- Bantuan: normalisasi `0812-3456-7890` → `6281234567890`, tautan terbentuk, nomor tidak valid 422, GET publik 404 jika kosong, GET publik sukses setelah diatur.

## E. Verifikasi dokumentasi

Buka `/docs/api` dan pastikan skema `data` pada endpoint daftar (paginasi) dan detail terbaca dan `meta` paginasi tampil. Jika endpoint paginasi tampil generik, terapkan cara paling sederhana yang didukung Scramble (mis. docblock `@response`), catat sebagai **D10** di `docs/DECISIONS.md`, dan jangan membuat sistem anotasi manual yang besar.

## F. Definition of Done

- [ ] Dikerjakan di branch `stage-03a-master-data`, satu commit per butir tugas, branch sudah di-push.
- [ ] Tugas 0.1–0.3 selesai dan test lama tetap lulus.
- [ ] Semua endpoint tabel di bagian C berfungsi dan tampil di `/docs/api` dengan skema benar (bagian E).
- [ ] Test bagian D lulus.
- [ ] Status Stage 3a → `DONE` di PRD §9 (3b tetap `TODO`), laporan singkat + Draft PR diberikan.

## G. Setelah agent selesai — MANUAL (user)

1. `git checkout stage-03a-master-data && git pull`, lalu `php artisan migrate:fresh --seed` dan `php artisan test`.
2. `php artisan serve`, buka `/docs/api`. Login admin, lalu coba: buat kategori, buat penjual, nonaktifkan penjual, lihat daftar user, atur Bantuan lalu buka `/api/v1/bantuan` tanpa token.
3. Buat Pull Request ke `main`, tempel Draft PR, dan kirim URL PR ke chat untuk direview sebelum merge.