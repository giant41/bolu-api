<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemesananDetail extends Model
{
    use HasFactory;

    protected $fillable = ['id_pemesanan', 'id_produk', 'jumlah_produk', 'harga_satuan'];

}
