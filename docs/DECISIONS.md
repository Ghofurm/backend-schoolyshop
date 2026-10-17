# Keputusan Teknis (Architecture Decisions)

Dokumen ini mencatat keputusan teknis yang disepakati untuk Schooly Shop API.

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
| D9 | Schema dokumentasi API Scramble di `/docs/api` otomatis mendeteksi objek data via instance `JsonResource` yang di-pass langsung ke `ApiResponse::success(data: ...)`. |

