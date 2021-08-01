<?php

namespace App\Http\Controllers;

use App\Models\BahanBakuDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BahanBakuDetailController extends Controller
{
    public function __construct() {
        $this->bahanBakuDetailTable = New BahanBakuDetail;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request['page'];
        $limit = $request['per_page'];
        $offset = ($page - 1) * $limit;
        $search_word = $request['keyword'];

        $bahan_baku_details = $this->bahanBakuDetailTable->getAllBahanBakuDetail($search_word, $request['id'], $offset, $limit);

        $bahan_baku_detail = array();
        $i = 0;
        foreach ($bahan_baku_details['bahan_baku_details'] as $bahan_baku) {
            $bahan_baku_detail[$i]['id'] =  $bahan_baku->id;
            $bahan_baku_detail[$i]['bahan_baku']['id'] =  $bahan_baku->id_bahan_baku;
            $bahan_baku_detail[$i]['bahan_baku']['nama_bahan_baku'] =  $bahan_baku->nama_bahan_baku;
            $bahan_baku_detail[$i]['bahan_baku']['harga_satuan'] =  $bahan_baku->harga_satuan;
            $bahan_baku_detail[$i]['bahan_baku']['jumlah_order'] =  $bahan_baku->jumlah_order;
            $bahan_baku_detail[$i]['bahan_baku']['simbol_satuan'] =  $bahan_baku->simbol_satuan;
            $bahan_baku_detail[$i]['suplayer']['id'] =  $bahan_baku->id_suplayer;
            $bahan_baku_detail[$i]['suplayer']['nama_suplayer'] =  $bahan_baku->nama_suplayer;
            $bahan_baku_detail[$i]['suplayer']['alamat'] =  $bahan_baku->alamat;
            $bahan_baku_detail[$i]['suplayer']['no_telp'] =  $bahan_baku->no_telp;
            $bahan_baku_detail[$i]['order']['nomor_order'] =  $bahan_baku->nomor_order;
            $bahan_baku_detail[$i]['order']['tanggal_order'] =  $bahan_baku->tanggal_order;
            $bahan_baku_detail[$i]['created_at'] =  $bahan_baku->created_at;
            $bahan_baku_detail[$i]['updated_at'] =  $bahan_baku->updated_at;
            $i++;
        }

        $response = [
            'message' => 'list data bahan baku detail',
            'data' => $bahan_baku_detail,
            'total' => $bahan_baku_details['total']
        ];

        return response()->json($response, Response::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_bahan_baku' => ['required'],
            'id_suplayer' => ['required'],
            'nomor_order' => ['required'],
            'harga_satuan' => ['required'],
            'jumlah_order' => ['required'],
            'tanggal_faktur' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $bahan_baku = BahanBakuDetail::create($request->all());
            $response = [
                'message' => 'data bahan baku detail berhasil dibuat',
                'data' => $bahan_baku
            ];

            return response()->json($response, Response::HTTP_CREATED);

        } catch (QueryException $e) {

            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bahan_baku_details = $this->bahanBakuDetailTable->getBahanBakuDetailById();
        $bahan_baku_detail = array();
        foreach ($bahan_baku_details as $bahan_baku) {
            $bahan_baku_detail['id'] =  $bahan_baku->id;
            $bahan_baku_detail['bahan_baku']['id'] =  $bahan_baku->id_bahan_baku;
            $bahan_baku_detail['bahan_baku']['nama_bahan_baku'] =  $bahan_baku->nama_bahan_baku;
            $bahan_baku_detail['bahan_baku']['harga_satuan'] =  $bahan_baku->harga_satuan;
            $bahan_baku_detail['bahan_baku']['jumlah_order'] =  $bahan_baku->jumlah_order;
            $bahan_baku_detail['suplayer']['id'] =  $bahan_baku->id_suplayer;
            $bahan_baku_detail['suplayer']['nama_suplayer'] =  $bahan_baku->nama_suplayer;
            $bahan_baku_detail['suplayer']['alamat'] =  $bahan_baku->alamat;
            $bahan_baku_detail['suplayer']['no_telp'] =  $bahan_baku->no_telp;
            $bahan_baku_detail['order']['nomor_order'] =  $bahan_baku->nomor_order;
            $bahan_baku_detail['order']['tanggal_order'] =  $bahan_baku->tanggal_faktur;
            $bahan_baku_detail['created_at'] =  $bahan_baku->created_at;
            $bahan_baku_detail['updated_at'] =  $bahan_baku->updated_at;
        }

        $response = [
            'message' => 'data bahan baku detail',
            'data' => $bahan_baku_detail
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bahan_baku = BahanBakuDetail::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_bahan_baku' => ['required'],
            'id_suplayer' => ['required'],
            'nomor_order' => ['required'],
            'harga_satuan' => ['required'],
            'jumlah_order' => ['required'],
            'tanggal_order' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $bahan_baku->update($request->all());
            $response = [
                'message' => 'data bahan baku detail telah diupdate',
                'data' => $bahan_baku
            ];

            return response()->json($response, Response::HTTP_OK);

        } catch (QueryException $e) {

            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bahan_baku = BahanBakuDetail::findOrFail($id);
        try{
            $bahan_baku->delete();
            $response = [
                'message' => 'data bahan baku detail berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
