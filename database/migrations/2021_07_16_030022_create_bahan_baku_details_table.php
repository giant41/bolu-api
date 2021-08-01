<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBahanBakuDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bahan_baku_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan_baku');
            $table->foreignId('id_suplayer');
            $table->integer('nomor_order');
            $table->double('harga_satuan', 8, 2);
            $table->integer('jumlah_order');
            $table->date('tanggal_order');
            $table->timestamps();
            $table->index('id_bahan_baku');
            $table->index('id_suplayer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bahan_baku_details');
    }
}
