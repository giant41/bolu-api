<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BahanBaku extends Model
{
    use HasFactory;

    protected $fillable = ['id_satuan', 'nama_bahan_baku', 'stok', 'created_by', 'updated_by'];
      
    public function getAllBahanBaku($search_word, $offset, $limit) {
        if($search_word == null) {
            $total_data = DB::table('bahan_bakus')
                    ->select('bahan_bakus.*', 'satuans.nama_satuan', 'satuans.simbol_satuan')
                    ->orderBy('bahan_bakus.nama_bahan_baku', 'DESC')
                    ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                    ->get();

            $bahan_bakus = DB::table('bahan_bakus')
                    ->select('bahan_bakus.*', 'satuans.nama_satuan', 'satuans.simbol_satuan')
                    ->orderBy('bahan_bakus.nama_bahan_baku', 'DESC')
                    ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy('id', 'DESC')
                    ->get();

            $total = count($total_data);
        } else {
            $total_data = DB::table('bahan_bakus')
                        ->select('bahan_bakus.*', 'satuans.nama_satuan', 'satuans.simbol_satuan')
                        ->orderBy('bahan_bakus.nama_bahan_baku', 'DESC')
                        ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                        ->where('bahan_bakus.nama_bahan_baku', 'like', '%' . $search_word . '%')
                        ->orwhere('satuans.nama_satuan', 'like', '%' . $search_word . '%')
                        ->get();
            $bahan_bakus = DB::table('bahan_bakus')
                        ->select('bahan_bakus.*', 'satuans.nama_satuan', 'satuans.simbol_satuan')
                        ->orderBy('bahan_bakus.nama_bahan_baku', 'DESC')
                        ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                        ->where('bahan_bakus.nama_bahan_baku', 'like', '%' . $search_word . '%')
                        ->orwhere('satuans.nama_satuan', 'like', '%' . $search_word . '%')
                        ->limit($limit)
                        ->offset($offset)
                        ->orderBy('id', 'DESC')
                        ->get();
            $total = count($total_data);
        }

        $data = array(
            'bahan_baku' => $bahan_bakus,
            'total' => $total 
        );

        return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    
    public function updateStok($id_bahan_baku, $stok) {
        $userId = Auth::user()->id;
        $update_stok = DB::table('bahan_bakus')
            ->where('id', $id_bahan_baku)
            ->update(['stok' => $stok, 'updated_by' => $userId,  'updated_at' => date('Y-m-d h:i:s')]);
        return $update_stok;    
    }

}
