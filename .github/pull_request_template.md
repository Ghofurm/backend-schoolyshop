## Ringkasan

<!-- Jelaskan secara singkat apa yang berubah dan mengapa -->

## Perubahan utama

- 

## Cara menguji

1. `git checkout <branch> && git pull`
2. `php artisan migrate:fresh --seed`
3. `php artisan test`
4. `php artisan serve` → cek `/docs/api`

## Checklist Definition of Done

- [ ] Semua tugas stage selesai dan sesuai PRD §3 (konvensi) dan §7 (aturan bisnis).
- [ ] Test relevan lulus.
- [ ] Endpoint baru muncul di `/docs/api` dengan contoh request/respons yang benar.
- [ ] Tidak ada secret di kode. `.env.example` diperbarui bila ada variabel baru.
- [ ] Status stage di PRD §9 diperbarui, commit dibuat, laporan singkat diberikan.

## Catatan untuk reviewer

<!-- Hal yang perlu diperhatikan, trade-off, atau risiko -->
