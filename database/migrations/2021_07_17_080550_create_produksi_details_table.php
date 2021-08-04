<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduksiDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produksi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produksi');
            $table->foreignId('id_resep');
            $table->foreignId('id_resep_detail');
            $table->foreignId('id_bahan_baku');
            $table->double('jumlah_bahan_baku', 8, 2);
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
        Schema::dropIfExists('produksi_details');
    }
}
