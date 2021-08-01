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

    public function getOrderItemListByNomorOrder($nomor_order) {
        $list_items = DB::table('order_bahan_baku_details')
            ->select('order_bahan_baku_details.*', 'bahan_bakus.nama_bahan_baku', 'satuans.simbol_satuan')
            ->where('order_bahan_baku_details.id_order_bahan_baku', '=', $nomor_order)
            ->leftJoin('bahan_bakus', 'order_bahan_baku_details.id_bahan_baku', '=', 'bahan_bakus.id')
            ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
            ->get();
        return $list_items;
    }
}
