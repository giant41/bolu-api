<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BahanBakuDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_bahan_baku', 
        'id_suplayer', 
        'nomor_order', 
        'harga_satuan',
        'jumlah_order',
        'tanggal_order'
    ];

    public function getAllBahanBakuDetail($search_word, $bahan_baku_id, $offset, $limit) {
        if($search_word == null) {
            $total_data = DB::table('bahan_baku_details')
                ->select(
                        'bahan_baku_details.*', 
                        'bahan_bakus.nama_bahan_baku',
                        'suplayers.nama_suplayer',
                        'suplayers.alamat',
                        'suplayers.no_telp',
                        'satuans.simbol_satuan'
                    )  
                ->where('bahan_baku_details.id_bahan_baku', '=', $bahan_baku_id)      
                ->orderBy('bahan_baku_details.id', 'DESC')
                ->leftJoin('bahan_bakus', 'bahan_baku_details.id_bahan_baku', '=', 'bahan_bakus.id')
                ->leftJoin('suplayers', 'bahan_baku_details.id_suplayer', '=', 'suplayers.id')
                ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                ->get();

            $bahan_baku_details = DB::table('bahan_baku_details')
                    ->select(
                            'bahan_baku_details.*', 
                            'bahan_bakus.nama_bahan_baku',
                            'suplayers.nama_suplayer',
                            'suplayers.alamat',
                            'suplayers.no_telp',
                            'satuans.simbol_satuan'
                        )  
                    ->where('bahan_baku_details.id_bahan_baku', '=', $bahan_baku_id)      
                    ->orderBy('bahan_baku_details.id', 'DESC')
                    ->leftJoin('bahan_bakus', 'bahan_baku_details.id_bahan_baku', '=', 'bahan_bakus.id')
                    ->leftJoin('suplayers', 'bahan_baku_details.id_suplayer', '=', 'suplayers.id')
                    ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy('id', 'DESC')
                    ->get();

            $total = count($total_data);
        } else {
            $total_data = DB::table('bahan_baku_details')
                    ->select(
                            'bahan_baku_details.*', 
                            'bahan_bakus.nama_bahan_baku',
                            'suplayers.nama_suplayer',
                            'suplayers.alamat',
                            'suplayers.no_telp',
                            'satuans.simbol_satuan'
                        )      
                    ->orderBy('bahan_baku_details.id', 'DESC')
                    ->leftJoin('bahan_bakus', 'bahan_baku_details.id_bahan_baku', '=', 'bahan_bakus.id')
                    ->leftJoin('suplayers', 'bahan_baku_details.id_suplayer', '=', 'suplayers.id')
                    ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                    ->where('bahan_baku_details.id_bahan_baku', '=', $bahan_baku_id)  
                    ->where('bahan_baku_details.nomor_order', 'like', '%' . $search_word . '%')
                    ->orwhere('suplayers.nama_suplayer', 'like', '%' . $search_word . '%')
                    ->get();
            $bahan_baku_details = DB::table('bahan_baku_details')
                    ->select(
                            'bahan_baku_details.*', 
                            'bahan_bakus.nama_bahan_baku',
                            'suplayers.nama_suplayer',
                            'suplayers.alamat',
                            'suplayers.no_telp',
                            'satuans.simbol_satuan'
                        )  
                            
                    ->orderBy('bahan_baku_details.id', 'DESC')
                    ->leftJoin('bahan_bakus', 'bahan_baku_details.id_bahan_baku', '=', 'bahan_bakus.id')
                    ->leftJoin('suplayers', 'bahan_baku_details.id_suplayer', '=', 'suplayers.id')
                    ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                    ->where('bahan_baku_details.id_bahan_baku', '=', $bahan_baku_id) 
                    ->where('bahan_baku_details.nomor_order', 'like', '%' . $search_word . '%')
                    ->orwhere('suplayers.nama_suplayer', 'like', '%' . $search_word . '%')
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('id', 'DESC')
                    ->get();
            $total = count($total_data);
        }

        $data = array(
            'bahan_baku_details' => $bahan_baku_details,
            'total' => $total 
        );

        return $data;
    }

    public function getBahanBakuDetailById($id) {
        $bahan_baku_details = DB::table('bahan_baku_details')
            ->select(
                    'bahan_baku_details.*', 
                    'bahan_bakus.nama_bahan_baku',
                    'suplayers.nama_suplayer',
                    'suplayers.alamat',
                    'suplayers.no_telp'
                )
            ->where('bahan_baku_details.id', '=', $id)
            ->orderBy('bahan_baku_details.id', 'DESC')
            ->leftJoin('bahan_bakus', 'bahan_baku_details.id_bahan_baku', '=', 'bahan_bakus.id')
            ->leftJoin('suplayers', 'bahan_baku_details.id_suplayer', '=', 'suplayers.id')
            ->get();

        return $bahan_baku_details;
    }
    
}
