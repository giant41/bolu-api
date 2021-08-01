<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiDetail extends Model
{
    use HasFactory;

    protected $fillable = ['id_produksi', 'id_resep', 'id_resep_detail', 'jumlah_bahan_baku'];

}
