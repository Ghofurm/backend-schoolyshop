# PRD — Schooly Shop API (Backend)

> Dokumen kerja untuk AI coding agent. Baca file ini + SATU file stage aktif saja.
> Jangan membaca ulang seluruh repo atau dokumen analisis kecuali diminta.
> **Langkah PERTAMA setiap stage: buat branch baru (lihat "Git Workflow" di §0). Jangan pernah bekerja langsung di `main`.**

## 0. Aturan Kerja Agent (WAJIB)

### Git Workflow (lakukan PALING PERTAMA, sebelum mengubah apa pun)

Proyek memakai alur branch + Pull Request. Satu stage = satu branch = satu PR. PR dibuat dan di-merge MANUAL oleh user.

1. `git status` harus bersih. Jika ada perubahan yang belum di-commit, berhenti dan tanyakan ke user.
2. `git checkout main && git pull origin main`. Stage sebelumnya harus berstatus `DONE` di §9 pada `main` (artinya PR-nya sudah di-merge). Jika belum, berhenti dan beri tahu user.
3. Buat branch baru dari `main`: `git checkout -b stage-XX-nama-singkat` (contoh: `stage-02-auth`). Jika branch itu sudah ada (melanjutkan atau memperbaiki hasil review), cukup `git checkout` ke branch tersebut. Perbaikan dari review PR dikerjakan di branch yang sama sebagai commit tambahan, bukan branch baru.
4. Semua pekerjaan dan commit HANYA di branch itu. Commit kecil per unit kerja yang logis dengan format `stage-XX: ringkasan singkat`.
5. DILARANG: commit atau push langsung ke `main`, merge, rebase ke `main`, force-push, membuat/menutup/meng-merge Pull Request, atau menghapus branch.
6. Di akhir stage: `git push -u origin <nama-branch>`. Jika push gagal (tidak ada akses), berhenti dan beri tahu user.

### Aturan Umum

1. Kerjakan hanya stage berstatus `IN PROGRESS` di §9. Detailnya ada di `docs/stages/stage-XX-*.md`. Jangan mulai stage yang file-nya belum ada.
2. Ikuti urutan tugas di file stage. Jangan menambah fitur, paket, atau refactor di luar scope.
3. Gunakan `php artisan make:*` untuk boilerplate. Jangan menulis ulang file yang sudah benar.
4. Jalankan hanya test yang relevan (`php artisan test --filter=...`), bukan seluruh suite berulang kali.
5. Bagian bertanda **MANUAL (user)** dikerjakan user, bukan agent. Jika terblokir karena itu, berhenti dan sebutkan apa yang dibutuhkan.
6. Jika ada ketidakjelasan, pilih opsi paling sederhana yang sesuai PRD, catat 1 baris di `docs/DECISIONS.md`, lanjutkan. Bertanya hanya jika benar-benar terblokir.
7. Setelah stage selesai: centang Definition of Done, ubah status stage di §9 menjadi `DONE` (di branch stage, bukan di `main`), commit, push branch, lalu lapor maksimal 10 baris (apa yang dibuat, cara mencoba, hal yang perlu dicek user) ditambah blok **Draft PR**: judul `Stage XX: ...` dan deskripsi 3-5 baris yang bisa ditempel user. Lalu BERHENTI. Jangan mulai stage berikutnya sebelum user menyatakan PR sudah di-merge.
8. Komentar kode dan laporan singkat. Jangan membuat README panjang atau dokumentasi di luar yang diminta.
9. Dokumentasi API dibuat otomatis oleh Scramble dari kode, jadi setiap endpoint WAJIB memakai Form Request (input) dan API Resource (output) dengan return type yang jelas.
10. Jangan pernah menulis API key/secret ke kode atau commit. Hanya lewat `.env` dan `config/*.php`.

## 1. Ringkasan Produk

Schooly Shop adalah e-commerce berbasis web yang menjual produk milik siswa, guru, dan karyawan sekolah kepada masyarakat umum. Penjualan dijalankan unit Schooly Shop sekolah yang berperan sebagai **Admin**. Repo ini hanya **backend REST API** (tanpa frontend).

Aktor: **Admin**, **User** (pembeli). Sistem eksternal: **Midtrans** (pembayaran, refund), **Biteship** (ongkir, resi, tracking, retur). Pihak pendukung tanpa akses sistem: Penjual/Supplier (tidak punya akun; datanya diinput Admin), Kurir. Bantuan = tautan WhatsApp yang diatur Admin.

## 2. Keputusan Teknis (default; boleh diubah user sebelum stage terkait dimulai)

| ID | Keputusan |
|----|-----------|
| D1 | Laravel versi terpasang, Sanctum (token), Scramble (`dedoc/scramble`) untuk OpenAPI/dokumentasi di `/docs/api`. Jika dosen mewajibkan Swagger UI klasik, ganti ke `darkaonline/l5-swagger` di stage 10. |
| D2 | Nama tabel, PK, dan FK **persis seperti ERD** (singular, bahasa Indonesia). Tiap model wajib set `$table` dan `$primaryKey`. |
| D3 | DB: MySQL nantinya akan migrasi ke cloud seperti supabase atau neon. Kode WAJIB DB-agnostic (tanpa raw SQL spesifik DB). Dev default MySQL. |
| D4 | Tabel tambahan `log_aktivitas` (keamanan: persetujuan produk, perubahan status, webhook, refund). Notifikasi in-app memakai tabel bawaan Laravel `notifications`. |
| D5 | Tabel tambahan `riwayat_retur` (riwayat tracking pengiriman retur, struktur sama dengan `riwayat_pengiriman`). |
| D6 | Dokumen analisis ambigu soal "Diproses". Keputusan: webhook pembayaran berhasil mengubah pesanan menjadi **`dibayar`**; tombol "Proses Pesanan" Admin mengubahnya menjadi **`diproses`**. |
| D7 | Pilihan kurir/layanan User disimpan di `pesanan` (`kurir_dipilih`, `layanan_dipilih`) karena baris `pengiriman` baru dibuat Admin kemudian. |
| D8 | Dua model auth terpisah: `Admin` dan `User`, keduanya `HasApiTokens`. Token memakai ability `admin` atau `user`. Middleware alias `role.admin` dan `role.user`. |

## 3. Konvensi

- Base path `/api/v1`. Admin: `/api/v1/admin/*`. Publik: auth, katalog. Webhook: `/api/v1/webhooks/midtrans` dan `/api/v1/webhooks/biteship`.
- Envelope respons: `{"success": bool, "message": string, "data": any|null, "meta": {...}|null, "errors": {...}|null}`. Error validasi = 422.
- Uang: integer rupiah (`unsignedBigInteger`). Waktu: zona `Asia/Jakarta`, output ISO 8601.
- Struktur: validasi di `app/Http/Requests`, output di `app/Http/Resources`, logika bisnis di `app/Services`, integrasi eksternal di `app/Services/External/{MidtransService,BiteshipService}`, enum di `app/Enums`. Controller tipis.
- Operasi multi-tabel dibungkus `DB::transaction`. Pengurangan/pengembalian stok memakai `lockForUpdate`.
- Pagination: `?page=&per_page=` (default 15). Upload ke disk `public`, jpg/png/webp, maks 2 MB.
- Webhook: verifikasi keaslian, idempoten (aman diproses berulang), catat di `log_aktivitas`, balas 200 untuk event valid.
- Scheduler: `pesanan:kedaluwarsa` (tiap menit), `pesanan:konfirmasi-otomatis` (tiap 15 menit). Queue driver `database`. Mail driver `log` saat dev.
- Konstanta bisnis di `config/schooly.php`: batas bayar 60 menit, konfirmasi otomatis 48 jam, batas retur 48 jam, alamat asal sekolah.
- Test: feature test untuk endpoint utama; `Http::fake()` untuk Midtrans dan Biteship (jangan memanggil API asli saat test).

## 4. Hak Akses

- **Publik (tanpa login):** register, login, lihat katalog/cari/filter/detail produk, info bantuan.
- **User:** profil dan alamat, keranjang, checkout, bayar, riwayat pesanan, invoice, tracking, konfirmasi terima, ajukan retur dan lihat status retur, notifikasi.
- **Admin:** semua pengelolaan di §7. Admin **hanya boleh melihat** data User (tidak ada endpoint create/update/delete User).

## 5. Model Data

Format: `tabel(PK; kolom...)`. FK ditulis `→tabel`. Semua tabel juga memakai `timestamps()`.

```
kategori(id_kategori; nama_kategori)
penjual(id_penjual; nama, jenis_penjual[siswa|guru|karyawan], kelas_jabatan, email, kontak, status[aktif|nonaktif])
admin(id_admin; nama, email unique, password)
user(id_user; nama, email unique, no_hp, password, status_akun[aktif|nonaktif], tanggal_daftar)
pengajuan_produk(id_pengajuan; →penjual, →admin, →kategori, nama_produk, deskripsi, harga_usulan, stok_usulan, foto, status_pengajuan, alasan_penolakan null, tgl_pengajuan)
produk(id_produk; →penjual, →kategori, →pengajuan unique null, nama_produk, deskripsi, harga, stok, berat_gram, foto, status_aktif bool)
alamat(id_alamat; →user, nama_penerima, no_hp_penerima, alamat_lengkap, area_id_biteship, kode_pos, alamat_utama bool)
keranjang(id_keranjang; →user, →produk, jumlah)   unique(id_user,id_produk)
pesanan(id_pesanan; →user, →alamat, kode_pesanan unique, subtotal, ongkir, total_bayar, status_pesanan, jenis_konfirmasi[manual|otomatis] null, kurir_dipilih, layanan_dipilih, tgl_pesanan, tgl_selesai null)
detail_pesanan(id_detail; →pesanan, →produk, jumlah, harga_satuan, subtotal)   -- harga_satuan = snapshot harga saat checkout
pembayaran(id_pembayaran; →pesanan unique, order_id_midtrans unique, metode_pembayaran null, jumlah_bayar, status_pembayaran, batas_bayar, waktu_bayar null)
pengiriman(id_pengiriman; →pesanan unique, →admin, biteship_order_id, kurir, layanan, no_resi, biaya_kirim, status_pengiriman, tgl_kirim, tgl_diterima null)
   -- tgl_diterima = waktu status "terkirim" dari Biteship; awal hitungan 48 jam konfirmasi/retur
riwayat_pengiriman(id_riwayat; →pengiriman, status, keterangan, waktu_update)
bantuan(id_bantuan; →admin, nomor_whatsapp, pesan_awal, tautan_whatsapp)
retur(retur_id; pesanan_id→pesanan, detail_pesanan_id→detail_pesanan, user_id→user, admin_id→admin null, jumlah, kategori_alasan, keterangan, bukti_foto, status, alasan_keputusan null, biaya_retur null, penanggung_biaya[unit|user] null, biteship_order_id_retur null, nomor_resi_retur null, kondisi_barang null, layak_jual bool null, tanggal_pengajuan, tanggal_diterima null)
riwayat_retur(id; retur_id→retur, status, keterangan, waktu_update)
pengembalian_dana(refund_id; retur_id→retur, pembayaran_id→pembayaran, nominal_barang, potongan_biaya_retur, nominal_ongkir_dikembalikan, nominal_refund, metode_refund[midtrans|manual], refund_id_midtrans null, rekening_tujuan null, status, tanggal_proses null)
log_aktivitas(id; aktor_tipe, aktor_id null, aksi, deskripsi, data json null)
```

Catatan: `bukti_foto` di `retur` disimpan sebagai JSON array path (minimal 1 foto). `foto` produk boleh satu path.

## 6. Enum (backed string enum di `app/Enums`)

- `StatusPesanan`: `menunggu_pembayaran, dibayar, diproses, dikirim, selesai, dalam_pengembalian, kedaluwarsa, bermasalah`
- `StatusPembayaran`: `menunggu, berhasil, gagal, kedaluwarsa`
- `StatusPengajuan`: `menunggu, disetujui, ditolak`
- `StatusPengiriman`: dipetakan dari status Biteship di stage 7 (agent baca dokumentasi Biteship saat itu)
- `StatusRetur`: `menunggu_verifikasi, disetujui, ditolak, barang_dikirim_balik, barang_diterima, selesai`
- `KategoriAlasanRetur`: `kesalahan_unit` (rusak/cacat, tidak sesuai pesanan, jumlah kurang), `alasan_user` (tidak sesuai harapan, salah pilih)
- `StatusRefund`: `diproses, selesai, selesai_manual, gagal`

## 7. Aturan Bisnis Inti

1. Katalog hanya menampilkan produk `status_aktif` dengan stok > 0 yang berasal dari pengajuan **disetujui**. Menyetujui pengajuan otomatis membuat baris `produk`. Menonaktifkan penjual menonaktifkan semua produknya. Stok habis otomatis menonaktifkan produk sementara.
2. Admin menginput pengajuan (data penjual + produk), memutuskan setujui/tolak (tolak wajib alasan), lalu menekan "Kirim Pemberitahuan" yang mengirim email hasil ke penjual.
3. Produk yang pernah dipesan tidak dihapus permanen, hanya dinonaktifkan. Kategori yang masih dipakai tidak boleh dihapus.
4. Keranjang: jumlah dibatasi stok. Checkout hanya untuk User login dengan minimal 1 item dan alamat. Ongkir dihitung server dari Biteship (User tidak bisa mengubah nominalnya).
5. Membuat pesanan: validasi stok, **kurangi stok saat pesanan dibuat**, status `menunggu_pembayaran`, `batas_bayar` = +60 menit. Lewat batas: pesanan `kedaluwarsa`, pembayaran `kedaluwarsa`, stok dikembalikan.
6. Pesanan hanya boleh diproses/dikirim jika pembayaran `berhasil`. Alur status: `menunggu_pembayaran → dibayar (webhook) → diproses (Admin) → dikirim (Admin buat pengiriman) → selesai`.
7. Konfirmasi terima oleh User, atau otomatis 48 jam setelah `pengiriman.tgl_diterima` (`jenis_konfirmasi` mencatat manual/otomatis). Ditunda selama ada retur aktif (status pesanan `dalam_pengembalian`).
8. Retur: hanya jika pesanan sudah terkirim dan belum lewat 48 jam; per item, jumlah ≤ dibeli − sudah diretur; wajib ≥ 1 foto. `alasan_user` hanya untuk produk non-makanan/minuman dan belum dipakai.
9. Biaya retur dan nilai refund:

| Komponen | kesalahan_unit | alasan_user |
|---|---|---|
| Biaya kirim retur | ditanggung unit | ditanggung User, dipotong dari refund |
| Nilai refund | harga item × jumlah | harga item × jumlah − biaya retur |
| Ongkir pesanan awal | dikembalikan hanya jika SELURUH item pesanan diretur | tidak dikembalikan |

10. Refund hanya diproses setelah barang retur diterima Admin dan dinyatakan sesuai klaim. Nominal refund tidak boleh melebihi pembayaran berhasil. Metode tanpa refund otomatis → Admin transfer manual dan catat (`selesai_manual`). Barang layak jual kembali ke stok; rusak tidak.
11. Rekap penjualan per periode (harian/mingguan/bulanan) dan per penjual = pesanan berpembayaran berhasil dikurangi refund yang diproses pada periode yang sama.
12. Bantuan: Admin mengatur nomor dan pesan awal WhatsApp; sistem membentuk tautan `https://wa.me/<nomor>?text=<urlencoded>`.

## 8. Integrasi Eksternal (ringkas)

- **Midtrans (sandbox, Snap):** buat transaksi (`order_id_midtrans` unik), webhook diverifikasi `sha512(order_id + status_code + gross_amount + server_key)`, refund via API berdasarkan order ID asal.
- **Biteship (Testing Mode):** autocomplete area (untuk alamat), cek tarif (area asal sekolah → area tujuan, berat total), buat order + resi, webhook status, buat order retur (asal alamat User → tujuan sekolah). Detail endpoint dan payload: agent membaca dokumentasi resmi Biteship/Midtrans saat mengerjakan stage terkait.
- Env: `MIDTRANS_SERVER_KEY, MIDTRANS_CLIENT_KEY, MIDTRANS_IS_PRODUCTION=false, BITESHIP_API_KEY, BITESHIP_BASE_URL, SCHOOL_ORIGIN_AREA_ID, SCHOOL_ORIGIN_NAME, SCHOOL_ORIGIN_PHONE, SCHOOL_ORIGIN_ADDRESS, SCHOOL_ORIGIN_POSTAL`.

## 9. Daftar Stage

| # | Stage | Isi | Status | File |
|---|-------|-----|--------|------|
| 0 | Fondasi | Paket, config, envelope respons, error JSON, health check, docs endpoint | DONE | `docs/stages/stage-00-fondasi.md` |
| 1 | Database | Migration, model, relasi, enum, factory, seeder | DONE | `docs/stages/stage-01-database.md` |
| 2 | Auth | Register/login/logout, profil, role admin vs user, perbaikan hasil review Stage 0-1 | DONE | `docs/stages/stage-02-auth.md` |
| 3 | Master data Admin | Penjual, kategori, pengajuan, produk, harga/stok, lihat user, bantuan, email | TODO | (belum dibuat) |
| 4 | Katalog, alamat, keranjang | Katalog publik + cari/filter, alamat, keranjang | TODO | (belum dibuat) |
| 5 | Biteship tarif + checkout | Service Biteship, area, ongkir, buat pesanan, stok | TODO | (belum dibuat) |
| 6 | Midtrans | Snap, webhook + signature, kedaluwarsa, scheduler | TODO | (belum dibuat) |
| 7 | Fulfilment Admin | Proses pesanan, buat pengiriman, webhook Biteship, tracking, konfirmasi, invoice | TODO | (belum dibuat) |
| 8 | Retur dan refund | Pengajuan, verifikasi, retur Biteship, refund Midtrans/manual | TODO | (belum dibuat) |
| 9 | Laporan | Dashboard, rekap penjualan, notifikasi | TODO | (belum dibuat) |
| 10 | Finalisasi | Test, hardening, rapikan dokumentasi API, siap demo | TODO | (belum dibuat) |

## 10. Definition of Done Umum (setiap stage)

- [ ] Semua tugas stage selesai dan sesuai §3 (konvensi) dan §7 (aturan bisnis).
- [ ] Test relevan lulus.
- [ ] Endpoint baru muncul di `/docs/api` dengan contoh request/respons yang benar.
- [ ] Tidak ada secret di kode. `.env.example` diperbarui bila ada variabel baru.
- [ ] Dikerjakan di branch `stage-XX-*` (bukan `main`) dan branch sudah di-push.
- [ ] Status stage di §9 diperbarui, laporan singkat + Draft PR diberikan.
