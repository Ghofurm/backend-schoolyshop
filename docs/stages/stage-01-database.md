# Stage 1 — Database (Migration, Model, Enum, Seeder)

**Tujuan:** seluruh struktur data PRD §5 tersedia di database lengkap dengan model, relasi, enum, factory, dan data awal. Belum ada endpoint.

## A. MANUAL (user) — sebelum agent

1. Pastikan Stage 0 berstatus `DONE` dan `php artisan migrate` berjalan tanpa error di database kosong.
2. Putuskan kembali D2 (nama tabel mengikuti ERD) dan D3 (MySQL atau PostgreSQL). Jika ingin konvensi Laravel biasa, ubah D2 di PRD §2 **sebelum** agent mulai karena mahal diubah setelahnya.

## B. AGENT — tugas

1. **Enum** di `app/Enums` sesuai PRD §6 (backed string enum). `StatusPengiriman` belum dibuat (stage 7).
2. **Migration** satu file per tabel PRD §5, diurutkan berdasarkan ketergantungan FK:
   `kategori, penjual, admin, user, pengajuan_produk, produk, alamat, keranjang, pesanan, detail_pesanan, pembayaran, pengiriman, riwayat_pengiriman, bantuan, retur, riwayat_retur, pengembalian_dana, log_aktivitas`, ditambah tabel `notifications` bawaan Laravel (`php artisan notifications:table`).
   - Uang `unsignedBigInteger`. Kolom enum disimpan sebagai `string` (bukan tipe `enum` DB) agar DB-agnostic.
   - Unique: email (admin, user), `kode_pesanan`, `order_id_midtrans`, `pembayaran.id_pesanan`, `pengiriman.id_pesanan`, `produk.id_pengajuan`, `keranjang(id_user,id_produk)`.
   - FK `onDelete`: `restrict` secara default; `cascade` hanya untuk `keranjang` (dari user), `alamat` (dari user), dan tabel riwayat.
   - Index pada kolom yang sering difilter: `status_pesanan`, `status_pembayaran`, `status_aktif`, `id_kategori`, `batas_bayar`, `tgl_diterima`, `status` (retur).
   - Penamaan PK/FK persis PRD §5, termasuk `retur_id`, `pesanan_id`, dll. pada `retur` dan `pengembalian_dana`.
3. **Model** di `app/Models`: set `$table`, `$primaryKey`, `$fillable`, `casts` (enum, bool, JSON untuk `bukti_foto` dan `data`), dan semua relasi sesuai tabel relasi antar entitas (user→alamat/keranjang/pesanan/retur, pesanan→detail_pesanan/pembayaran/pengiriman/retur, pengiriman→riwayat_pengiriman, retur→riwayat_retur/pengembalian_dana, pembayaran→pengembalian_dana, penjual→produk/pengajuan_produk, kategori→produk/pengajuan_produk, pengajuan_produk→produk).
   - `Admin` dan `User` memperluas `Authenticatable`, memakai `HasApiTokens` dan `Notifiable`, `password` di-hash lewat cast, dan `password` disembunyikan (`hidden`).
4. **Factory** untuk: kategori, penjual, user, alamat, produk, pesanan (dengan detail), pembayaran.
5. **Seeder** (`DatabaseSeeder` memanggil seeder lain):
   - 1 admin: `admin@schooly.test` / `password` (hanya untuk dev, kata sandi dibaca dari env `SEED_ADMIN_PASSWORD` bila ada).
   - 5 kategori, 5 penjual, 15 produk (masing-masing lewat pengajuan berstatus `disetujui`), 3 user dengan 1 alamat, 1 baris `bantuan`.
6. **Test** minimal: satu test yang memastikan `migrate:fresh --seed` berhasil dan relasi utama berfungsi (produk→penjual, pesanan→detail_pesanan, user→alamat).

## C. Definition of Done

- [ ] `php artisan migrate:fresh --seed` sukses tanpa error.
- [ ] Semua tabel PRD §5 ada, kolom dan nama sesuai.
- [ ] Setiap model punya relasi yang diminta; test relasi lulus.
- [ ] Tidak ada raw SQL spesifik DB.
- [ ] Status Stage 1 → `DONE`, Stage 2 → `TODO` (menunggu file stage dari user).

## D. Setelah agent selesai — MANUAL (user)

1. Jalankan `php artisan migrate:fresh --seed`.
2. Buka database di DBeaver atau TablePlus, cek tabel, kolom, dan relasi FK. Opsional: generate ERD dari DB lalu bandingkan dengan ERD di dokumen analisis.
3. Jika ada yang tidak sesuai, laporkan satu pesan ringkas ke agent (nama tabel + kolom yang salah). Jangan minta rewrite total.
4. Commit lalu minta stage berikutnya.
