<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBahanBakusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_bahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_suplayer');
            $table->integer('nomor_order');
            $table->date('tanggal_order');
            $table->enum('status', ['order', 'pending', 'diterima']);
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
        Schema::dropIfExists('order_bahan_bakus');
    }
}
