<?php

namespace App\Http\Controllers;

use App\Models\ResepDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResepDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resep_details = DB::table('resep_details')
            ->select('resep_details.*', 'reseps.nama_resep', 'bahan_bakus.nama_bahan_baku')
            ->orderBy('resep_details.id', 'DESC')
            ->leftJoin('reseps', 'resep_details.id_resep', '=', 'reseps.id')
            ->leftJoin('bahan_bakus', 'resep_details.id_bahan_baku', '=', 'bahan_bakus.id')
            ->get();

        $resep_detail = array();
        $i = 0;
        foreach ($resep_details as $resep) {
            $resep_detail[$i]['id'] =  $resep->id;
            $resep_detail[$i]['resep']['id_resep'] =  $resep->id_resep;
            $resep_detail[$i]['resep']['nama_resep'] =  $resep->nama_resep;
            $resep_detail[$i]['bahan_baku']['id_bahan_baku'] =  $resep->id_bahan_baku;
            $resep_detail[$i]['bahan_baku']['nama_bahan_baku'] =  $resep->nama_bahan_baku;
            $resep_detail[$i]['bahan_baku']['jumlah_bahan_baku'] =  $resep->jumlah_bahan_baku;
            $resep_detail[$i]['keterangan'] =  $resep->keterangan;
            $resep_detail[$i]['created_at'] =  $resep->created_at;
            $resep_detail[$i]['updated_at'] =  $resep->updated_at;
            $i++;
        }

        $response = [
            'message' => 'list data resep detail',
            'data' => $resep_detail
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
            'id_bahan_baku' => ['required'],
            'jumlah_bahan_baku' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $resep_detail = ResepDetail::create($request->all());
            $response = [
                'message' => 'data resep detail berhasil dibuat',
                'data' => $resep_detail
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
        $resep_details = DB::table('resep_details')
            ->select('resep_details.*', 'reseps.nama_resep', 'bahan_bakus.nama_bahan_baku')
            ->where('resep_details.id', '=', $id)
            ->orderBy('resep_details.id', 'DESC')
            ->leftJoin('reseps', 'resep_details.id_resep', '=', 'reseps.id')
            ->leftJoin('bahan_bakus', 'resep_details.id_bahan_baku', '=', 'bahan_bakus.id')
            ->get();

        $resep_detail = array();
        foreach ($resep_details as $resep) {
            $resep_detail['id'] =  $resep->id;
            $resep_detail['resep']['id_resep'] =  $resep->id_resep;
            $resep_detail['resep']['nama_resep'] =  $resep->nama_resep;
            $resep_detail['bahan_baku']['id_bahan_baku'] =  $resep->id_bahan_baku;
            $resep_detail['bahan_baku']['nama_bahan_baku'] =  $resep->nama_bahan_baku;
            $resep_detail['bahan_baku']['jumlah_bahan_baku'] =  $resep->jumlah_bahan_baku;
            $resep_detail['keterangan'] =  $resep->keterangan;
            $resep_detail['created_at'] =  $resep->created_at;
            $resep_detail['updated_at'] =  $resep->updated_at;
        }

        $response = [
            'message' => 'data resep detail',
            'data' => $resep_detail
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
        $resep_detail = ResepDetail::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'id_resep' => ['required'],
            'id_bahan_baku' => ['required'],
            'jumlah_bahan_baku' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $resep_detail->update($request->all());
            $response = [
                'message' => 'data resep detail telah diupdate',
                'data' => $resep_detail
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
        $resep_detail = ResepDetail::findOrFail($id);
        try{
            $resep_detail->delete();
            $response = [
                'message' => 'data resep detail berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
