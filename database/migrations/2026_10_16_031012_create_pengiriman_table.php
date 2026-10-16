<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id('id_pengiriman');
            $table->foreignId('id_pesanan')->unique()->constrained('pesanan', 'id_pesanan')->onDelete('restrict');
            $table->foreignId('id_admin')->constrained('admin', 'id_admin')->onDelete('restrict');
            $table->string('biteship_order_id')->nullable();
            $table->string('kurir');
            $table->string('layanan');
            $table->string('no_resi')->nullable();
            $table->unsignedBigInteger('biaya_kirim');
            $table->string('status_pengiriman');
            $table->timestamp('tgl_kirim')->nullable();
            $table->timestamp('tgl_diterima')->nullable();
            $table->timestamps();

            $table->index('status_pengiriman');
            $table->index('tgl_diterima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};
