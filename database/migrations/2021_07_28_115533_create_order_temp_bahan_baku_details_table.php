<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTempBahanBakuDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_temp_bahan_baku_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan_baku');
            $table->double('jumlah_pesanan', 8, 2);
            $table->integer('harga_satuan');
            $table->foreignId('created_by');
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
        Schema::dropIfExists('order_temp_bahan_baku_details');
    }
}
