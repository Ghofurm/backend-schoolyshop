<?php

namespace Tests\Feature;

use App\Models\Alamat;
use App\Models\DetailPesanan;
use App\Models\Penjual;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_runs_and_seed_data_exists(): void
    {
        $this->seed();

        $this->assertDatabaseHas('admin', ['email' => 'admin@schooly.test']);
        $this->assertDatabaseCount('kategori', 5);
        $this->assertDatabaseCount('penjual', 5);
        $this->assertDatabaseCount('pengajuan_produk', 15);
        $this->assertDatabaseCount('produk', 15);
        $this->assertDatabaseCount('user', 3);
        $this->assertDatabaseCount('alamat', 3);
        $this->assertDatabaseCount('bantuan', 1);
    }

    public function test_relationships_work_properly(): void
    {
        // 1. Relasi Produk -> Penjual & Kategori
        $produk = Produk::factory()->create();
        $this->assertInstanceOf(Penjual::class, $produk->penjual);
        $this->assertNotNull($produk->kategori);
        $this->assertTrue($produk->penjual->produk->contains($produk));

        // 2. Relasi User -> Alamat
        $user = User::factory()->create();
        $alamat = Alamat::factory()->create(['id_user' => $user->id_user]);
        $this->assertInstanceOf(User::class, $alamat->user);
        $this->assertTrue($user->alamat->contains($alamat));

        // 3. Relasi Pesanan -> DetailPesanan & Produk
        $pesanan = Pesanan::factory()->withDetails(2)->create(['id_user' => $user->id_user, 'id_alamat' => $alamat->id_alamat]);
        $this->assertCount(2, $pesanan->detailPesanan);
        $this->assertInstanceOf(DetailPesanan::class, $pesanan->detailPesanan->first());
        $this->assertInstanceOf(Produk::class, $pesanan->detailPesanan->first()->produk);
    }
}
