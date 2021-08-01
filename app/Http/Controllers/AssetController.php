<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
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

        if($search_word == null) {
            $total_data = Asset::orderBy('id', 'DESC')->get();
            $asset = DB::table('assets')
                ->offset($offset)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        } else {
            $total_data = Asset::orderBy('id', 'DESC')
                ->where('nama_asset', 'like', '%' . $search_word . '%')
                ->orwhere('type', 'LIKE', '%'.$search_word.'%')
                ->orwhere('keterangan', 'LIKE', '%'.$search_word.'%')
                ->get();
            $asset = DB::table('assets')
                ->where('nama_asset', 'like', '%' . $search_word . '%')
                ->orwhere('type', 'LIKE', '%'.$search_word.'%')
                ->orwhere('keterangan', 'LIKE', '%'.$search_word.'%')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        }

        $response = [
            'message' => 'list data Aset',
            'data' => $asset,
            'total' => $total
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
            'nama_asset' => ['required'],
            'type' => ['required'],
            'jumlah' => ['required'],
            'satuan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $asset = Asset::create($request->all());
            $response = [
                'message' => 'data aset created',
                'data' => $asset
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
        $asset = Asset::findOrFail($id);
        $response = [
            'message' => 'data aset detail',
            'data' => $asset
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
        $asset = Asset::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nama_asset' => ['required'],
            'type' => ['required'],
            'jumlah' => ['required'],
            'satuan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $asset->update($request->all());
            $response = [
                'message' => 'data aset berhasil diUpdate',
                'data' => $asset
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
        $asset = Asset::findOrFail($id);
        try{
            $asset->delete();
            $response = [
                'message' => 'data aset erhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
