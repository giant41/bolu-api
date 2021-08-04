<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ResepDetail extends Model
{
    use HasFactory;

    protected $fillable = ['id_resep', 'id_bahan_baku', 'jumlah_bahan_baku', 'keterangan', 'created_by', 'updated_by'];

    public function getAllResepDetailByResepId($search_word, $id_resep, $offset, $limit) {

        if($search_word == null) {
            $total_data = DB::table('resep_details')
                ->select('resep_details.id')
                ->leftJoin('bahan_bakus', 'resep_details.id_bahan_baku', '=', 'bahan_bakus.id')
                ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                ->leftJoin('users as u1', 'resep_details.created_by', '=', 'u1.id')
                ->leftJoin('users as u2', 'resep_details.updated_by', '=', 'u2.id')
                ->where('resep_details.id_resep', '=', $id_resep)
                ->get();

            $resep = DB::table('resep_details')
                ->select('resep_details.*', 
                    'bahan_bakus.nama_bahan_baku',
                    'bahan_bakus.nama_bahan_baku',
                    'satuans.simbol_satuan',
                    'u1.name as created_by',
                    'u2.name as updated_by'
                )
                ->where('resep_details.id_resep', '=', $id_resep)
                ->leftJoin('bahan_bakus', 'resep_details.id_bahan_baku', '=', 'bahan_bakus.id')
                ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                ->leftJoin('users as u1', 'resep_details.created_by', '=', 'u1.id')
                ->leftJoin('users as u2', 'resep_details.updated_by', '=', 'u2.id')
                ->offset($offset)
                ->limit($limit)
                ->orderBy('resep_details.id', 'DESC')
                ->get();
            $total = count($total_data);
        } else {
            $total_data = DB::table('resep_details')
                ->select('resep_details.id')
                ->leftJoin('bahan_bakus', 'resep_details.id_bahan_baku', '=', 'bahan_bakus.id')
                ->leftJoin('users as u1', 'resep_details.created_by', '=', 'u1.id')
                ->leftJoin('users as u2', 'resep_details.updated_by', '=', 'u2.id')
                ->where('resep_details.id_resep', '=', $id_resep)
                ->where('bahan_bakus.nama_bahan_baku', 'like', '%' . $search_word . '%')
                ->orwhere('u1.name', 'LIKE', '%'.$search_word.'%')
                ->orwhere('u2.name', 'LIKE', '%'.$search_word.'%')
                ->get();
            $resep = DB::table('resep_details')
                ->select('resep_details.*', 
                        'bahan_bakus.nama_bahan_baku',
                        'satuans.simbol_satuan',
                        'u1.name as created_by',
                        'u2.name as updated_by'
                    )
                ->leftJoin('bahan_bakus', 'resep_details.id_bahan_baku', '=', 'bahan_bakus.id')
                ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
                ->leftJoin('users as u1', 'resep_details.created_by', '=', 'u1.id')
                ->leftJoin('users as u2', 'resep_details.updated_by', '=', 'u2.id')
                ->where('resep_details.id_resep', '=', $id_resep)
                ->where('bahan_bakus.nama_bahan_baku', 'like', '%' . $search_word . '%')
                ->orwhere('u1.name', 'LIKE', '%'.$search_word.'%')
                ->orwhere('u2.name', 'LIKE', '%'.$search_word.'%')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('resep_details.id', 'DESC')
                ->get();
            $total = count($total_data);
        }

        $data = array(
            'resep_detail' => $resep,
            'total' => $total_data 
        );

        return $data;
    }

    public function getDetailByResepAndBahan($id_resep, $data_bahan_baku) {
        $data_resep_detail = DB::table('resep_details')
            ->where('id_resep', $id_resep)
            ->where('id_bahan_baku', $data_bahan_baku)
            ->get();

         return $data_resep_detail;   
    } 
}
