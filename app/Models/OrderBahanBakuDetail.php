<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderBahanBakuDetail extends Model
{
    use HasFactory;

    protected $fillable = ['id_order_bahan_baku', 'id_bahan_baku', 'jumlah_pesanan', 'harga_satuan'];

    public function getCountOrderByNomorOrder($nomor_order) {
        $count_data = OrderBahanBakuDetail::select(
            DB::raw('COUNT(id) AS total_item'), 
            DB::raw('SUM(jumlah_pesanan*harga_satuan) AS jumlah_total'))
            ->where('id_order_bahan_baku', $nomor_order)
            ->get();
        return $count_data;                
    }
}
