<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderBahanBaku extends Model
{
    use HasFactory;

    protected $fillable = ['id_suplayer', 'nomor_order', 'tanggal_order', 'status', 'created_by'];

    public function getAllOrderBahanBaku($search_word, $offset, $limit) {
        if($search_word == null) {
            $total_data = DB::table('order_bahan_bakus')
                    ->select(
                        'order_bahan_bakus.*', 
                        'suplayers.nama_suplayer', 
                        'users.name')
                    ->orderBy('order_bahan_bakus.id', 'DESC')
                    ->leftJoin('suplayers', 'order_bahan_bakus.id_suplayer', '=', 'suplayers.id')
                    ->leftJoin('users', 'order_bahan_bakus.created_by', '=', 'users.id')
                    ->get();

            $order_bahan_baku = DB::table('order_bahan_bakus')
                    ->select('order_bahan_bakus.*', 'suplayers.nama_suplayer', 'users.name')
                    ->leftJoin('suplayers', 'order_bahan_bakus.id_suplayer', '=', 'suplayers.id')
                    ->leftJoin('users', 'order_bahan_bakus.created_by', '=', 'users.id')
                    ->orderBy('order_bahan_bakus.id', 'DESC')
                    ->offset($offset)
                    ->limit($limit)
                    ->get();

            $total = count($total_data);
        } else {
            $total_data = DB::table('order_bahan_bakus')
                        ->select('order_bahan_bakus.*', 'suplayers.nama_suplayer', 'users.name')
                        ->leftJoin('suplayers', 'order_bahan_bakus.id_suplayer', '=', 'suplayers.id')
                        ->leftJoin('users', 'order_bahan_bakus.created_by', '=', 'users.id')
                        ->orderBy('order_bahan_bakus.id', 'DESC')
                        ->where('order_bahan_bakus.nomor_order', 'like', '%' . $search_word . '%')
                        ->orwhere('order_bahan_bakus.tanggal_order', 'like', '%' . $search_word . '%')
                        ->orwhere('suplayers.nama_suplayer', 'like', '%' . $search_word . '%')
                        ->orwhere('users.name', 'like', '%' . $search_word . '%')
                        ->get();
            $order_bahan_baku = DB::table('order_bahan_bakus')
                        ->select('order_bahan_bakus.*', 'suplayers.nama_suplayer', 'users.name')
                        ->leftJoin('suplayers', 'order_bahan_bakus.id_suplayer', '=', 'suplayers.id')
                        ->leftJoin('users', 'order_bahan_bakus.created_by', '=', 'users.id')
                        ->orderBy('order_bahan_bakus.id', 'DESC')
                        ->where('order_bahan_bakus.nomor_order', 'like', '%' . $search_word . '%')
                        ->orwhere('order_bahan_bakus.tanggal_order', 'like', '%' . $search_word . '%')
                        ->orwhere('suplayers.nama_suplayer', 'like', '%' . $search_word . '%')
                        ->orwhere('users.name', 'like', '%' . $search_word . '%')
                        ->limit($limit)
                        ->offset($offset)
                        ->orderBy('id', 'DESC')
                        ->get();
            $total = count($total_data);
        }

        $data = array(
            'order_bahan_baku' => $order_bahan_baku,
            'total' => $total 
        );

        return $data;
    }

    public function orderBahanBakuDetail($id) {
        $order_detail = DB::table('order_bahan_bakus')
            ->select('order_bahan_bakus.*', 'suplayers.nama_suplayer', 'users.name')
            ->where('order_bahan_bakus.id', '=', $id)
            ->leftJoin('suplayers', 'order_bahan_bakus.id_suplayer', '=', 'suplayers.id')
            ->leftJoin('users', 'order_bahan_bakus.created_by', '=', 'users.id')
            ->get();

        return $order_detail;
    }
}
