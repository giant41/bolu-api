<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HargaProduk extends Model
{
    use HasFactory;

    protected $fillable = ['id_produk', 'harga_dasar', 'harga_jual'];

    public function getHargaTerakhir($id_produk) {
        $harga = HargaProduk::select(
            'harga_dasar',
            'harga_jual',
            'created_at'
        )
        ->where('id_produk', $id_produk)
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get();

        return $harga;
    }

    public function getAllHargaByProdukId($search_word, $id_produk, $offset, $limit) {

        if($search_word == null) {
            $total_data = DB::table('harga_produks')
                ->select('id')
                ->where('id_produk', $id_produk)
                ->get();

            $data_harga = DB::table('harga_produks')
                ->where('id_produk', $id_produk)
                ->offset($offset)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        } else {
            $total_data = DB::table('harga_produks')
                ->select('id')
                ->where('id_produk', $id_produk)
                ->where('harga_dasar', 'like', '%' . $search_word . '%')
                ->orwhere('harga_jual', 'LIKE', '%'.$search_word.'%')
                ->get();
            $data_harga = DB::table('harga_produks')
                ->where('id_produk', $id_produk)
                ->where('harga_dasar', 'like', '%' . $search_word . '%')
                ->orwhere('harga_jual', 'LIKE', '%'.$search_word.'%')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        }

        $data = array(
            'harga' => $data_harga,
            'total' => $total 
        );

        return $data;
    }
}
