<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Produk extends Model
{
    use HasFactory;

    protected $fillable = ['id_resep'];

    public function getAllProduk($search_word, $offset, $limit) {
        if($search_word == null) {
            $total_data = DB::table('produks')
                ->select('produks.id')
                ->get();

            $produk = DB::table('produks')
                ->select('produks.*', 'reseps.nama_resep')
                ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
                ->offset($offset)
                ->limit($limit)
                ->orderBy('produks.id', 'DESC')
                ->get();
            $total = count($total_data);
        } else {
            $total_data = DB::table('produks')
                ->select('produks.id')
                ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
                ->where('reseps.nama_resep', 'like', '%' . $search_word . '%')
                ->get();
            $produk = DB::table('produks')
                ->select('produks.*', 'reseps.nama_resep')
                ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
                ->where('reseps.nama_resep', 'like', '%' . $search_word . '%')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        }

        $data = array(
            'produk' => $produk,
            'total' => $total 
        );

        return $data;
    }

    public function addProduk($id_resep) {
        $add_produk = DB::table('produks')->insert([
            'id_resep' => $id_resep,
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s')
        ]);

        return $add_produk;
    }
 
}
