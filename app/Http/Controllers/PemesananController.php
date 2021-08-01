<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Models\PemesananDetail;
use App\Models\Pemesan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data_pemesanan = Pemesanan::orderBy('id', 'DESC')->get();
        
        $pemesaan = array();
        $no=0;
        foreach ($data_pemesanan as $data) {
            $data_pemesan = Pemesan::findOrFail($data->id_pemesan);
            $pemesanan[$no]['id'] = $data->id;
            $pemesanan[$no]['nomor_faktur'] = $data->nomor_faktur;
            $pemesanan[$no]['pemesan']['id_pemesan'] = $data_pemesan->id;        
            $pemesanan[$no]['pemesan']['nama_pemesan'] =  $data_pemesan->nama_pemesan;


            $pemesanan_details = DB::table('pemesanan_details')
                ->select(
                    'jumlah_produk', 
                    'harga_satuan')
                ->where('id_pemesanan', "=", $data->id)
                ->get();

            $j = 0;
            $harga_total = 0;
            foreach ($pemesanan_details as $detail) {
                $harga_subtotal = $detail->harga_satuan * $detail->jumlah_produk;
                $harga_total = $harga_total + $harga_subtotal;
                $j++;
            }
            $pemesanan[$no]['jumlah_item'] =  $j;
            $pemesanan[$no]['harga_total'] =  $harga_total;

            $pemesanan[$no]['created_at'] = $data->created_at;
            $pemesanan[$no]['updated_at'] = $data->updated_at;
            $no++;
        }



        $response = [
            'message' => 'list data pemesanan',
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
            'id_pemesan' => ['required'],
            'nomor_faktur' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $pemesanan = Pemesanan::create($request->all());
            $response = [
                'message' => 'data pemesanan berhasil dibuat',
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
        $data_pemesanan = pemesanan::findOrFail($id);

        $data_pemesan = pemesan::findOrFail($data_pemesanan->id_pemesan);

        $pemesanan_details = DB::table('pemesanan_details')
            ->select(
                'pemesanan_details.id_produk', 
                'reseps.nama_resep', 
                'pemesanan_details.jumlah_produk', 
                'pemesanan_details.harga_satuan')
            ->orderBy('pemesanan_details.id', 'DESC')
            ->leftJoin('produks', 'pemesanan_details.id_produk', '=', 'produks.id')
            ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
            ->where('pemesanan_details.id_pemesanan', "=", $id)
            ->get();

        $pemesanan = array();
        $pemesanan['id'] = $data_pemesanan->id;
        $pemesanan['nomor_faktur'] = $data_pemesanan->nomor_faktur;
        $pemesanan['pemesan']['id_pemesan'] = $data_pemesanan->id_pemesan;        
        $pemesanan['pemesan']['nama_pemesan'] =  $data_pemesan->nama_pemesan;
        $pemesanan['pemesan']['alamat'] =  $data_pemesan->alamat;
        $pemesanan['pemesan']['no_telp'] =  $data_pemesan->no_telp;

        $i = 0;
        $harga_total = 0;
        foreach ($pemesanan_details as $detail) {
            $harga_subtotal = $detail->harga_satuan * $detail->jumlah_produk;
            $harga_total = $harga_total + $harga_subtotal;

            $pemesanan['pemesanan']['produk'][$i]['id_produk'] =  $detail->id_produk;
            $pemesanan['pemesanan']['produk'][$i]['nama_produk'] =  $detail->nama_resep;
            $pemesanan['pemesanan']['produk'][$i]['jumlah_produk'] =  $detail->jumlah_produk;
            $pemesanan['pemesanan']['produk'][$i]['harga_satuan'] =  $detail->harga_satuan;
            $pemesanan['pemesanan']['produk'][$i]['harga_subtotal'] =  $harga_subtotal;
            $i++;
        }
        
        $pemesanan['pemesanan']['harga_total'] = $harga_total;
        $pemesanan['created_at'] = $data_pemesanan->created_at;
        $pemesanan['updated_at'] = $data_pemesanan->updated_at;

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
        $pemesanan = pemesanan::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_pemesan' => ['required'],
            'nomor_faktur' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $pemesanan->update($request->all());
            $response = [
                'message' => 'data pemesanan berhasil diUpdate',
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
        $pemesanan = pemesanan::findOrFail($id);
        try{
            $pemesanan->delete();
            $response = [
                'message' => 'data pemesanan berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
