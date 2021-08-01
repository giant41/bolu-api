<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHargaProduksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('harga_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk');
            $table->double('harga_dasar', 8, 2);
            $table->double('harga_jual', 8, 2);
            $table->timestamps();
            $table->index('id_produk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('harga_produks');
    }
}
