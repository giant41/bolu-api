<?php

namespace App\Http\Controllers;

use App\Models\HargaProduk;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HargaProdukController extends Controller
{

    public function __construct() {
        $this->hargaProdukTable = new HargaProduk;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produks = DB::table('harga_produks')
            ->select('harga_produks.*', 'reseps.nama_resep')
            ->orderBy('produks.id', 'DESC')
            ->leftJoin('produks', 'harga_produks.id_produk', '=', 'produks.id')
            ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
            ->get();

        $produk = array();
        $i = 0;
        foreach ($produks as $prod) {
            $produk[$i]['id'] =  $prod->id;
            $produk[$i]['id_produk'] =  $prod->id_produk;
            $produk[$i]['nama_produk'] =  $prod->nama_resep;
            $produk[$i]['harga_dasar'] =  $prod->harga_dasar;
            $produk[$i]['harga_jual'] =  $prod->harga_jual;
            $produk[$i]['created_at'] =  $prod->created_at;
            $produk[$i]['updated_at'] =  $prod->updated_at;
            $i++;
        }

        $response = [
            'message' => 'list data harga produk',
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
            'id_produk' => ['required'],
            'harga_dasar' => ['required'],
            'harga_jual' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $produk = HargaProduk::create($request->all());
            $response = [
                'message' => 'data harga produk berhasil dibuat',
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
        $produks = DB::table('harga_produks')
            ->select('harga_produks.*', 'reseps.nama_resep')
            ->where('harga_produks.id', '=', $id)
            ->orderBy('produks.id', 'DESC')
            ->leftJoin('produks', 'harga_produks.id_produk', '=', 'produks.id')
            ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
            ->get();

        $produk = array();
        foreach ($produks as $prod) {
            $produk['id'] =  $prod->id;
            $produk['id_produk'] =  $prod->id_produk;
            $produk['nama_produk'] =  $prod->nama_resep;
            $produk['harga_dasar'] =  $prod->harga_dasar;
            $produk['harga_jual'] =  $prod->harga_jual;
            $produk['created_at'] =  $prod->created_at;
            $produk['updated_at'] =  $prod->updated_at;
        }

        $response = [
            'message' => 'data harga produk detail',
            'data' => $produk
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAllDataByProdukId(Request $request)
    {
        $page = $request['page'];
        $limit = $request['per_page'];
        $offset = ($page - 1) * $limit;
        $search_word = $request['keyword'];

        $data_harga_produk = $this->hargaProdukTable->getAllHargaByProdukId($search_word, $request['id'], $offset, $limit);

        $data_harga = array();
        $i = 0;
        foreach ($data_harga_produk['harga'] as $harga) {
            $data_harga[$i]['id'] =  $harga->id;
            $data_harga[$i]['id_produk'] =  $harga->id_produk;
            $data_harga[$i]['harga_dasar'] =  $harga->harga_dasar;
            $data_harga[$i]['harga_jual'] =  $harga->harga_jual;
            $data_harga[$i]['created_at'] =  $harga->created_at;
            $data_harga[$i]['updated_at'] =  $harga->updated_at;
            $i++;
        }

        $response = [
            'message' => 'data harga produk',
            'data' => $data_harga,
            'total' => $data_harga_produk['total']
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
        $produk = HargaProduk::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_produk' => ['required'],
            'harga_dasar' => ['required'],
            'harga_jual' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $produk->update($request->all());
            $response = [
                'message' => 'data harga produk telah diupdate',
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
        $produk = HargaProduk::findOrFail($id);
        try{
            $produk->delete();
            $response = [
                'message' => 'data harga produk berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
