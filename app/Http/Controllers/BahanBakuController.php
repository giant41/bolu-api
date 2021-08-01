<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BahanBakuController extends Controller
{
    public function __construct() {
        $this->bahanBakuTable = New BahanBaku;
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
        
        $bahan_bakus = $this->bahanBakuTable->getAllBahanBaku($search_word, $offset, $limit);

        $bahan_baku = array();
        $i = 0;
        foreach ($bahan_bakus['bahan_baku'] as $bahan) {
            $bahan_baku[$i]['id'] =  $bahan->id;
            $bahan_baku[$i]['nama_bahan_baku'] =  $bahan->nama_bahan_baku;
            $bahan_baku[$i]['stok'] =  $bahan->stok;
            $bahan_baku[$i]['satuan']['id_satuan'] =  $bahan->id_satuan;
            $bahan_baku[$i]['satuan']['nama_satuan'] =  $bahan->nama_satuan;
            $bahan_baku[$i]['satuan']['simbol_satuan'] =  $bahan->simbol_satuan;
            $bahan_baku[$i]['created_at'] =  $bahan->created_at;
            $bahan_baku[$i]['updated_at'] =  $bahan->updated_at;
            $i++;
        }

        $response = [
            'message' => 'list data bahan baku',
            'data' => $bahan_baku,
            'total' => $bahan_bakus['total']
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allBahanBaku()
    {
        $bahan_baku = BahanBaku::orderBy('nama_bahan_baku', 'DESC')->get();

        $response = [
            'message' => 'list data bahan aku',
            'data' => $bahan_baku
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
        $request->request->add(['updated_by' => 0]);

        $validator = Validator::make($request->all(), [
            'id_satuan' => ['required'],
            'nama_bahan_baku' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $bahan_baku = BahanBaku::create($request->all());
            $response = [
                'message' => 'data bahan baku created',
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
        $bahan_bakus = DB::table('bahan_bakus')
            ->select('bahan_bakus.*', 'satuans.nama_satuan', 'satuans.simbol_satuan')
            ->where('bahan_bakus.id', '=', $id)
            ->orderBy('bahan_bakus.nama_bahan_baku', 'DESC')
            ->leftJoin('satuans', 'bahan_bakus.id_satuan', '=', 'satuans.id')
            ->get();

        $bahan_baku = array();
        foreach ($bahan_bakus as $bahan) {
            $bahan_baku['id'] =  $bahan->id;
            $bahan_baku['nama_bahan_baku'] =  $bahan->nama_bahan_baku;
            $bahan_baku['stok'] =  $bahan->stok;
            $bahan_baku['satuan']['id_satuan'] =  $bahan->id_satuan;
            $bahan_baku['satuan']['nama_satuan'] =  $bahan->nama_satuan;
            $bahan_baku['satuan']['simbol_satuan'] =  $bahan->simbol_satuan;
            $bahan_baku['created_at'] =  $bahan->created_at;
            $bahan_baku['updated_at'] =  $bahan->updated_at;
        }

        $response = [
            'message' => 'data bahan baku detail',
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
        $bahan_baku = BahanBaku::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_satuan' => ['required'],
            'nama_bahan_baku' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $userId = Auth::user()->id;
            $bahan_baku = DB::table('bahan_bakus')
            ->where('id', $id)
            ->update(['id_satuan' => $request['id_satuan'], 'nama_bahan_baku' => $request['nama_bahan_baku'], 'updated_by' => $userId, 'updated_at' => date('Y-m-d h:i:s')]);

            $response = [
                'message' => 'data bahan baku telah diupdate',
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
        $bahan_baku = BahanBaku::findOrFail($id);
        try{
            $bahan_baku->delete();
            $response = [
                'message' => 'data bahan baku berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
