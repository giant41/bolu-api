<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBahanBakuDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_bahan_baku_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order_bahan_baku');
            $table->foreignId('id_bahan_baku');
            $table->integer('jumlah_pesanan');
            $table->integer('harga_satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_bahan_baku_details');
    }
}
