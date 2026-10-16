<?php

namespace Database\Seeders;

use App\Enums\JenisPenjual;
use App\Enums\StatusAkun;
use App\Enums\StatusPengajuan;
use App\Enums\StatusPenjual;
use App\Models\Admin;
use App\Models\Alamat;
use App\Models\Bantuan;
use App\Models\Kategori;
use App\Models\PengajuanProduk;
use App\Models\Penjual;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        $adminPassword = env('SEED_ADMIN_PASSWORD', 'password');
        $admin = Admin::updateOrCreate(
            ['email' => 'admin@schooly.test'],
            [
                'nama' => 'Admin Schooly Shop',
                'password' => Hash::make($adminPassword),
            ]
        );

        // 2. 5 Kategori
        $kategoriList = ['Seragam Sekolah', 'Alat Tulis & Buku', 'Makanan & Minuman', 'Kerajinan Tangan', 'Merchandise Sekolah'];
        $kategoriModels = [];
        foreach ($kategoriList as $namaKat) {
            $kategoriModels[] = Kategori::create([
                'nama_kategori' => $namaKat,
            ]);
        }

        // 3. 5 Penjual
        $penjualData = [
            [
                'nama' => 'Ahmad Siswa',
                'jenis_penjual' => JenisPenjual::SISWA,
                'kelas_jabatan' => 'XII RPL 1',
                'email' => 'ahmad@siswa.test',
                'kontak' => '081234567801',
                'status' => StatusPenjual::AKTIF,
            ],
            [
                'nama' => 'Budi Guru',
                'jenis_penjual' => JenisPenjual::GURU,
                'kelas_jabatan' => 'Guru Kewirausahaan',
                'email' => 'budi@guru.test',
                'kontak' => '081234567802',
                'status' => StatusPenjual::AKTIF,
            ],
            [
                'nama' => 'Citra Karyawan',
                'jenis_penjual' => JenisPenjual::KARYAWAN,
                'kelas_jabatan' => 'Staf Tata Usaha',
                'email' => 'citra@karyawan.test',
                'kontak' => '081234567803',
                'status' => StatusPenjual::AKTIF,
            ],
            [
                'nama' => 'Dewi Siswa',
                'jenis_penjual' => JenisPenjual::SISWA,
                'kelas_jabatan' => 'XI Tata Boga',
                'email' => 'dewi@siswa.test',
                'kontak' => '081234567804',
                'status' => StatusPenjual::AKTIF,
            ],
            [
                'nama' => 'Eko Guru',
                'jenis_penjual' => JenisPenjual::GURU,
                'kelas_jabatan' => 'Pembina Koperasi',
                'email' => 'eko@guru.test',
                'kontak' => '081234567805',
                'status' => StatusPenjual::AKTIF,
            ],
        ];

        $penjualModels = [];
        foreach ($penjualData as $pData) {
            $penjualModels[] = Penjual::create($pData);
        }

        // 4. 15 Produk (masing-masing lewat pengajuan berstatus disetujui)
        $namaProdukList = [
            'Seragam Putih Abu-abu OSIS',
            'Batik Khas Sekolah Lengan Panjang',
            'Topi dan Dasi Resmi Sekolah',
            'Buku Modul Praktikum RPL',
            'Paket Spidol & Alat Tulis',
            'Tempat Pensil Rajut Handmade',
            'Keripik Singkong Renyah Rasa Balado',
            'Kue Kering Nastar Homemade',
            'Roti Coklat Keju Fresh Oven',
            'Gantungan Kunci Logo Sekolah Akrilik',
            'Tote Bag Kanvas Lukis Tangan',
            'Gelang Rajut Kreatif Karya Siswa',
            'Tumbler Minum Eksklusif Schooly',
            'Notebook Agenda Hardcover',
            'Stiker Pack Maskot Sekolah',
        ];

        foreach ($namaProdukList as $idx => $nama) {
            $penjual = $penjualModels[$idx % count($penjualModels)];
            $kategori = $kategoriModels[$idx % count($kategoriModels)];
            $harga = 15000 + ($idx * 5000);
            $stok = 20 + ($idx * 2);
            $berat = 200 + ($idx * 50);

            $pengajuan = PengajuanProduk::create([
                'id_penjual' => $penjual->id_penjual,
                'id_admin' => $admin->id_admin,
                'id_kategori' => $kategori->id_kategori,
                'nama_produk' => $nama,
                'deskripsi' => 'Deskripsi untuk produk berkualitas ' . $nama,
                'harga_usulan' => $harga,
                'stok_usulan' => $stok,
                'foto' => 'produk/' . ($idx + 1) . '.jpg',
                'status_pengajuan' => StatusPengajuan::DISETUJUI,
                'alasan_penolakan' => null,
                'tgl_pengajuan' => now()->subDays(5),
            ]);

            Produk::create([
                'id_penjual' => $penjual->id_penjual,
                'id_kategori' => $kategori->id_kategori,
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'nama_produk' => $nama,
                'deskripsi' => 'Deskripsi untuk produk berkualitas ' . $nama,
                'harga' => $harga,
                'stok' => $stok,
                'berat_gram' => $berat,
                'foto' => 'produk/' . ($idx + 1) . '.jpg',
                'status_aktif' => true,
            ]);
        }

        // 5. 3 User dengan 1 alamat masing-masing
        for ($u = 1; $u <= 3; $u++) {
            $user = User::create([
                'nama' => 'Pembeli ' . $u,
                'email' => 'user' . $u . '@schooly.test',
                'no_hp' => '08987654320' . $u,
                'password' => Hash::make('password'),
                'status_akun' => StatusAkun::AKTIF,
                'tanggal_daftar' => now()->subDays(10),
            ]);

            Alamat::create([
                'id_user' => $user->id_user,
                'nama_penerima' => $user->nama,
                'no_hp_penerima' => $user->no_hp,
                'alamat_lengkap' => 'Jl. Pemuda No. ' . (10 + $u) . ', RT 01 / RW 02, Semarang',
                'area_id_biteship' => 'IDNP10IDNC393IDND4703IDZ50249',
                'kode_pos' => '50249',
                'alamat_utama' => true,
            ]);
        }

        // 6. 1 baris bantuan
        $phone = '6281234567890';
        $message = 'Halo Admin Schooly Shop, saya ingin bertanya seputar layanan toko.';
        Bantuan::create([
            'id_admin' => $admin->id_admin,
            'nomor_whatsapp' => $phone,
            'pesan_awal' => $message,
            'tautan_whatsapp' => 'https://wa.me/' . $phone . '?text=' . rawurlencode($message),
        ]);
    }
}
