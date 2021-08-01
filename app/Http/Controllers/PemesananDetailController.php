<?php

namespace App\Http\Controllers;

use App\Models\PemesananDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class PemesananDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pemesanan = PemesananDetail::orderBy('id', 'DESC')->get();

        $response = [
            'message' => 'list data pemesanan detail',
            'data' => $pemesanan
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
            'id_pemesanan' => ['required'],
            'id_produk' => ['required'],
            'jumlah_produk' => ['required'],
            'harga_satuan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $pemesanan = PemesananDetail::create($request->all());
            $response = [
                'message' => 'data pemesanan detail berhasil dibuat',
                'data' => $pemesanan
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
        $pemesanan = pemesananDetail::findOrFail($id);
        $response = [
            'message' => 'data pemesanan detail',
            'data' => $pemesanan
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
        $pemesanan = pemesananDetail::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_pemesanan' => ['required'],
            'id_produk' => ['required'],
            'jumlah_produk' => ['required'],
            'harga_satuan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $pemesanan->update($request->all());
            $response = [
                'message' => 'data pemesanan detail berhasil diUpdate',
                'data' => $pemesanan
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
        $pemesanan = pemesananDetail::findOrFail($id);
        try{
            $pemesanan->delete();
            $response = [
                'message' => 'data pemesanan detail berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
