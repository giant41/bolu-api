<?php

namespace App\Http\Controllers;

use App\Models\OrderTempBahanBakuDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OrderTempBahanBakuController extends Controller
{
    public function __construct()
    {
        $this->tempOrderTable = new OrderTempBahanBakuDetail;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = $this->tempOrderTable->getAllData();
        $list_order = array();
        $i = 0;
        foreach ($orders as $order) {
            $list_order[$i]['id'] =  $order->id;
            $list_order[$i]['nama_bahan_baku'] =  $order->nama_bahan_baku;
            $list_order[$i]['jumlah'] =  $order->jumlah_pesanan;
            $list_order[$i]['satuan'] =  $order->simbol_satuan;
            $list_order[$i]['harga'] =  $order->harga_satuan;
            $i++;
        }

        $response = [
            'message' => 'list temporary order',
            'data' => $list_order
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
        $userId = Auth::user()->id; 
        $request->request->add(['created_by' => $userId]);
        
        $validator = Validator::make($request->all(), [
            'id_bahan_baku' => ['required'],
            'jumlah_pesanan' => ['required'],
            'harga_satuan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $bahan_baku = $this->tempOrderTable->create($request->all());
            $response = [
                'message' => 'data sementara order bahan baku berhasil disimpan',
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
        $bahan_baku = $this->tempOrderTable->findOrFail($id);
        $response = [
            'message' => 'data order bahan baku detail',
            'data' => $bahan_baku
        ];

        return response()->json($response, Response::HTTP_OK);
    }

        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showByBahanBakuId($id)
    {
        $userId = Auth::user()->id; 
        $bahan_baku = $this->tempOrderTable->getDataByBahanBakuId($id);
        $response = [
            'message' => 'data order bahan baku detail',
            'data' => $bahan_baku
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
        $bahan_baku = $this->tempOrderTable->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_bahan_baku' => ['required'],
            'jumlah_pesanan' => ['required'],
            'harga_satuan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $bahan_baku->update($request->all());
            $response = [
                'message' => 'data order bahan baku telah diupdate',
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
        $bahan_baku = $this->tempOrderTable->findOrFail($id);
        try{
            $bahan_baku->delete();
            $response = [
                'message' => 'data order bahan baku detail berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }

    /**
     * Remove all data by user id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeByUserId()
    {
        $bahan_baku = $this->tempOrderTable->removeByUserAuth();
        $response = [
            'message' => 'data order bahan baku detail berhasil dihapus'
        ];
        return response()->json($response, Response::HTTP_OK);
    }

}


