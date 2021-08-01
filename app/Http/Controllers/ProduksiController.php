<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data_produksi = DB::table('produksis')
            ->select('produksis.*', 'reseps.nama_resep')
            ->orderBy('produksis.id', 'DESC')
            ->leftJoin('produks', 'produksis.id_produk', '=', 'produks.id')
            ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
            ->get();

        $produksi = array();
        $i = 0;
        foreach ($data_produksi as $prod) {
            $produksi[$i]['id'] =  $prod->id;
            $produksi[$i]['id_produk'] =  $prod->id_produk;
            $produksi[$i]['nama_produk'] =  $prod->nama_resep;
            $produksi[$i]['jumlah_produksi'] =  $prod->jumlah_produksi;
            $produksi[$i]['keterangan'] =  $prod->keterangan;
            $produksi[$i]['created_at'] =  $prod->created_at;
            $produksi[$i]['updated_at'] =  $prod->updated_at;
            $i++;
        }

        $response = [
            'message' => 'list data produksi',
            'data' => $produksi
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
            'jumlah_produksi' => ['required'],
            'keterangan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // mengambil data resep untuk prores produksi
        $data_resep = DB::table('produks')
            ->select('produks.id_resep', 
                    'resep_details.id AS id_resep_detail', 
                    'resep_details.id AS id_bahan_baku', 
                    'resep_details.jumlah_bahan_baku', 
                    'bahan_bakus.stok AS stok_bahan_baku')
            ->orderBy('resep_details.id', 'DESC')
            ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
            ->leftJoin('resep_details', 'reseps.id', '=', 'resep_details.id_resep')
            ->leftJoin('bahan_bakus', 'resep_details.id_bahan_baku', '=', 'bahan_bakus.id')
            ->where('produks.id', '=', $request['id_produk'])
            ->get();

        try{
            if(count($data_resep) !== 0) {
                $produksi = Produksi::create($request->all());

                $canceld = 0;
                foreach ($data_resep as $resep) {

                    // jumlah bahan aku yang dibutuhkan
                    $bahan_baku = $resep->jumlah_bahan_baku * $request['jumlah_produksi'];

                    // jika stok bahan baku tersedia
                    if($resep->stok_bahan_baku >= $bahan_baku) {

                        DB::table('produksi_details')->insert([
                                'id_produksi' => $produksi['id'], 
                                'id_resep' => $resep->id_resep,
                                'id_resep_detail' => $resep->id_resep_detail,
                                'id_bahan_baku' => $resep->id_bahan_baku,
                                'jumlah_bahan_baku' => $bahan_baku
                            ]
                        );

                        // mengurangi stok bahan baku
                        $stok_bahan_baku = $resep->stok_bahan_baku - $bahan_baku;
                        // update stok aktual bahan baku 
                        DB::table('bahan_bakus')
                            ->where('id', $resep->id_bahan_baku)
                            ->update(['stok' => $stok_bahan_baku]);
                    } else {

                        $cancel_produksi = DB::table('produksi_details')
                            ->where('id_produksi', '=', $produksi['id'])
                            ->get();

                        foreach ($cancel_produksi as $cancel) {

                            // mengembalikan stok bahan baku ke stok awal
                            $bahan_baku = BahanBaku::findOrFail($cancel->id_bahan_baku);
                            $stok_bahan_baku = $bahan_baku->stok + $cancel->jumlah_bahan_baku;
                            $bahan_baku->update(['stok' => $stok_bahan_baku]);
                            
                            // hapus data produksi detail 
                            $hapus_produksi_detail = DB::table('produksi_details')
                                ->where('id', '=', $cancel->id)
                                ->delete();
                        }   
                        
                        $canceld = 1;
                    }

                }

                // periksa proses input data produksi dan ketersediaan stok bahan baku
                if($canceld == 0) {
                    $response = [
                        'message' => 'data produksi berhasil dibuat',
                        'data' => $produksi
                    ];
                } else {
                    // hapus data produksi  
                    $hapus_produksi = DB::table('produksis')
                    ->where('id', '=', $produksi->id)
                    ->delete();

                    $response = [
                        'message' => 'data produksi gagal dibuat, salah satu stok bahan tidak mencukupi'
                    ];                  
                }

            } else {
                $response = [
                    'message' => 'data produksi gagal dibuat'
                ];
            }
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

        $data_produksi = DB::table('produksis')
        ->select('produksis.*', 'reseps.id AS id_resep', 'reseps.nama_resep')
        ->leftJoin('produks', 'produksis.id_produk', '=', 'produks.id')
        ->leftJoin('reseps', 'produks.id_resep', '=', 'reseps.id')
        ->where('produksis.id', '=', $id)
        ->get();

        $produk = array();
        foreach ($data_produksi as $prod) {
            $produk['id'] =  $prod->id;
            $produk['id_produk'] =  $prod->id_produk;
            $produk['jumlah_produksi'] =  $prod->jumlah_produksi;
            $produk['resep']['id_resep'] =  $prod->id_resep;
            $produk['resep']['nama_resep'] =  $prod->nama_resep;

            $data_produksi_detail = DB::table('produksi_details')
                ->select(
                    'produksi_details.id_bahan_baku', 
                    'produksi_details.jumlah_bahan_baku', 
                    'bahan_bakus.nama_bahan_baku')
                ->leftJoin('bahan_bakus', 'produksi_details.id_bahan_baku', '=', 'bahan_bakus.id')
                ->where('produksi_details.id_produksi', '=', $prod->id)
                ->get();
            
                $bahan_baku = 0;
            foreach ($data_produksi_detail as $produksi_detail) {
                $produk['bahan_baku'][$bahan_baku]['id_bahan_baku'] =  $produksi_detail->id_bahan_baku;
                $produk['bahan_baku'][$bahan_baku]['nama_bahan_baku'] =  $produksi_detail->nama_bahan_baku;
                $produk['bahan_baku'][$bahan_baku]['jumlah_bahan_baku'] =  $produksi_detail->jumlah_bahan_baku;
                $bahan_baku++;
            }        

            $produk['created_at'] =  $prod->created_at;
            $produk['updated_at'] =  $prod->updated_at;
        }


        $response = [
            'message' => 'data produksi detail',
            'data' => $produk
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     $produk = Produk::findOrFail($id);
    //     $validator = Validator::make($request->all(), [
    //         'id_resep' => ['required'],
    //         'keterangan' => ['required']
    //     ]);

    //     if($validator->fails()) {
    //         return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
    //     }

    //     try{
    //         $produk->update($request->all());
    //         $response = [
    //             'message' => 'data produk telah diupdate',
    //             'data' => $produk
    //         ];

    //         return response()->json($response, Response::HTTP_OK);

    //     } catch (QueryException $e) {

    //         return response()->json([
    //             'message' => "Failed" . $e->errorInfo
    //         ]);
    //     }
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     $produk = Produk::findOrFail($id);
    //     try{
    //         $produk->delete();
    //         $response = [
    //             'message' => 'data produk berhasil dihapus'
    //         ];

    //         return response()->json($response, Response::HTTP_OK);
    //     } catch (QueryException $e) {
    //         return response()->json([
    //             'message' => "Failed" . $e->errorInfo
    //         ]);
    //     }
    // }
}
