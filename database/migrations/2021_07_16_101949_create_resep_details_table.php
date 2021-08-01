<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResepDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resep_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_resep');
            $table->foreignId('id_bahan_baku');
            $table->integer('jumlah_bahan_baku');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->index('id_resep');
            $table->index('id_bahan_baku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resep_details');
    }
}
