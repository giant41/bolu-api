<?php

namespace App\Http\Controllers;

use App\Models\Suplayer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SuplayerController extends Controller
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
            $total_data = Suplayer::orderBy('id', 'DESC')->get();
            $suplayer = DB::table('suplayers')
                ->offset($offset)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        } else {
            $total_data = Suplayer::orderBy('id', 'DESC')
                ->where('nama_suplayer', 'like', '%' . $search_word . '%')
                ->orwhere('alamat', 'LIKE', '%'.$search_word.'%')
                ->orwhere('no_telp', 'LIKE', '%'.$search_word.'%')
                ->get();
            $suplayer = DB::table('suplayers')
                ->where('nama_suplayer', 'like', '%' . $search_word . '%')
                ->orwhere('alamat', 'LIKE', '%'.$search_word.'%')
                ->orwhere('no_telp', 'LIKE', '%'.$search_word.'%')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        }

        $response = [
            'message' => 'list data suplayer',
            'data' => $suplayer,
            'total' => $total
        ];

        return response()->json($response, Response::HTTP_OK);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allSuplayer()
    {
        $suplayer = Suplayer::orderBy('nama_suplayer', 'DESC')->get();

        $response = [
            'message' => 'list data bahan aku',
            'data' => $suplayer
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
            'nama_suplayer' => ['required'],
            'alamat' => ['required'],
            'no_telp' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $suplayer = Suplayer::create($request->all());
            $response = [
                'message' => 'data suplayer created',
                'data' => $suplayer
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
        $suplayer = Suplayer::findOrFail($id);
        $response = [
            'message' => 'data suplayer detail',
            'data' => $suplayer
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
        $suplayer = Suplayer::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nama_suplayer' => ['required'],
            'alamat' => ['required'],
            'no_telp' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $suplayer->update($request->all());
            $response = [
                'message' => 'data suplayer Updated',
                'data' => $suplayer
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
        $suplayer = Suplayer::findOrFail($id);
        try{
            $suplayer->delete();
            $response = [
                'message' => 'data suplayer deleted'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
