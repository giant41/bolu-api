<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suplayer extends Model
{
    use HasFactory;

    protected $fillable = ['nama_suplayer', 'alamat', 'no_telp'];

}
