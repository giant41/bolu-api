<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produks = DB::table('produks')
            ->select('produks.*', 'reseps.nama_resep')
            ->orderBy('produks.id', 'DESC')
            ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
            ->get();

        $produk = array();
        $i = 0;
        foreach ($produks as $prod) {
            $produk[$i]['id'] =  $prod->id;
            $produk[$i]['id_resep'] =  $prod->id_resep;
            $produk[$i]['nama_produk'] =  $prod->nama_resep;
            $produk[$i]['keterangan'] =  $prod->keterangan;
            $produk[$i]['created_at'] =  $prod->created_at;
            $produk[$i]['updated_at'] =  $prod->updated_at;
            $i++;
        }

        $response = [
            'message' => 'list data produk',
            'data' => $produk
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
            'id_resep' => ['required'],
            'keterangan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $produk = Produk::create($request->all());
            $response = [
                'message' => 'data resep detail berhasil dibuat',
                'data' => $produk
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
        $produks = DB::table('produks')
            ->select('produks.*', 'reseps.nama_resep')
            ->where('produks.id', '=', $id)
            ->orderBy('produks.id', 'DESC')
            ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
            ->get();

        $produk = array();
        foreach ($produks as $prod) {
            $produk['id'] =  $prod->id;
            $produk['id_resep'] =  $prod->id_resep;
            $produk['nama_produk'] =  $prod->nama_resep;
            $produk['keterangan'] =  $prod->keterangan;
            $produk['created_at'] =  $prod->created_at;
            $produk['updated_at'] =  $prod->updated_at;
        }

        $response = [
            'message' => 'data produk detail',
            'data' => $produk
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
        $produk = Produk::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_resep' => ['required'],
            'keterangan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $produk->update($request->all());
            $response = [
                'message' => 'data produk telah diupdate',
                'data' => $produk
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
        $produk = Produk::findOrFail($id);
        try{
            $produk->delete();
            $response = [
                'message' => 'data produk berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
