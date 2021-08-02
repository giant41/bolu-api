<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Resep extends Model
{
    use HasFactory;

    protected $fillable = ['nama_resep', 'keterangan', 'created_by', 'updated_by'];
 

    public function getAllResep($search_word, $offset, $limit) {
        if($search_word == null) {
            $total_data = DB::table('reseps')
                ->select('reseps.*', 'users.name')
                ->leftJoin('users', 'reseps.created_by', '=', 'users.id')
                ->get();

            $resep = DB::table('reseps')
                ->select('reseps.*', 'users.name')
                ->leftJoin('users', 'reseps.created_by', '=', 'users.id')
                ->offset($offset)
                ->limit($limit)
                ->orderBy('reseps.id', 'DESC')
                ->get();
            $total = count($total_data);
        } else {
            $total_data = DB::table('reseps')
                ->select('reseps.*', 'users.name')
                ->leftJoin('users', 'reseps.created_by', '=', 'users.id')
                ->where('reseps.nama_resep', 'like', '%' . $search_word . '%')
                ->orwhere('users.name', 'LIKE', '%'.$search_word.'%')
                ->get();
            $resep = DB::table('reseps')
                ->select('reseps.*', 'users.name')
                ->leftJoin('users', 'reseps.created_by', '=', 'users.id')
                ->where('reseps.nama_resep', 'like', '%' . $search_word . '%')
                ->orwhere('users.name', 'LIKE', '%'.$search_word.'%')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        }

        $data = array(
            'resep' => $resep,
            'total' => $total_data 
        );

        return $data;
    }

    public function getDetail($id) {
        $data_resep = DB::table('reseps')
        ->select('reseps.*', 
                'u1.name as created_name',
                'u2.name as updated_name'
            )

        ->leftJoin('users as u1', 'reseps.created_by', '=', 'u1.id')
        ->leftJoin('users as u2', 'reseps.updated_by', '=', 'u2.id')
        ->where('reseps.id', '=', $id)
        ->get();

        return $data_resep;
    }

}
