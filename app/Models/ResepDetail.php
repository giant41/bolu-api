<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepDetail extends Model
{
    use HasFactory;

    protected $fillable = ['id_resep', 'id_bahan_baku', 'jumlah_bahan_baku', 'keterangan'];

}
