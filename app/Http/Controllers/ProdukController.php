<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\HargaProduk;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{

    public function __construct()
    {
        $this->produkTable = new Produk;
        $this->hargaProdukTable = new HargaProduk;
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

        $produks = $this->produkTable->getAllProduk($search_word, $offset, $limit);
        $data_produk = array();
        $i = 0;
        foreach ($produks['produk'] as $produk) {

            $harga = $this->hargaProdukTable->getHargaTerakhir($produk->id);
            $data_produk[$i]['id'] =  $produk->id;
            $data_produk[$i]['id_resep'] =  $produk->id_resep;
            $data_produk[$i]['nama_produk'] =  $produk->nama_resep;
            if(isset($harga[0])) {
                $data_produk[$i]['harga']['harga_dasar'] =  $harga[0]->harga_dasar;
                $data_produk[$i]['harga']['harga_jual'] =  $harga[0]->harga_jual;
                $data_produk[$i]['harga']['created_at'] =  date_format($harga[0]->created_at, "Y-m-d h:i:s");
            } else {
                $data_produk[$i]['harga']['harga_dasar'] =  "";
                $data_produk[$i]['harga']['harga_jual'] =  "";
                $data_produk[$i]['harga']['created_at'] =  "";
            }
            $data_produk[$i]['created_at'] =  $produk->created_at;
            $data_produk[$i]['updated_at'] =  $produk->updated_at;
            $i++;
        }

        $response = [
            'message' => 'list data produk',
            'data' => $data_produk,
            'total' => $produks['total']
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
