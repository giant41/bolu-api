<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderTempBahanBakuDetail extends Model
{
    use HasFactory;

    protected $fillable = ['id_bahan_baku', 'jumlah_pesanan', 'harga_satuan', 'created_by'];

     /**
     * Get all data from order_temp_bahan_baku_details table by user auth.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllData() {
        $userId = Auth::user()->id;
        $orders = DB::table('order_temp_bahan_baku_details')
            ->select('order_temp_bahan_baku_details.jumlah_pesanan', 
                    'order_temp_bahan_baku_details.harga_satuan', 
                    'order_temp_bahan_baku_details.id', 
                    'order_temp_bahan_baku_details.id_bahan_baku', 
                    'bahan_bakus.nama_bahan_baku', 
                    'satuans.simbol_satuan')
            ->orderBy('order_temp_bahan_baku_details.id', 'DESC')
            ->where('order_temp_bahan_baku_details.created_by', $userId)
            ->leftJoin('bahan_bakus', 'order_temp_bahan_baku_details.id_bahan_baku', '=', 'bahan_bakus.id')
            ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
            ->get();

        return $orders;
    }

    /**
     * Get data by bahan_baku_id and user auth.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDataByBahanBakuId($bahan_baku_id) {
        $userId = Auth::user()->id;
        $orders = DB::table('order_temp_bahan_baku_details')
            ->where('id_bahan_baku', $bahan_baku_id)
            ->where('created_by', $userId)
            ->get();
        return $orders;
    }

    /**
     * Remove all data by user auth.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeByUserAuth() {
        $userId = Auth::user()->id;
        $orders = DB::table('order_temp_bahan_baku_details')->where('created_by', '=', $userId)->delete();
        return $orders;
    }

}
