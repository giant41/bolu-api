<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SatuanController extends Controller
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
            $total_data = Satuan::orderBy('id', 'DESC')->get();
            $satuan = DB::table('satuans')
                ->offset($offset)
                ->limit($limit)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        } else {
            $total_data = Satuan::orderBy('id', 'DESC')
                ->where('nama_satuan', 'like', '%' . $search_word . '%')
                ->orwhere('simbol_satuan', 'LIKE', '%'.$search_word.'%')
                ->get();
            $satuan = DB::table('satuans')
                ->where('nama_satuan', 'LIKE', '%'.$search_word.'%')
                ->orwhere('simbol_satuan', 'LIKE', '%'.$search_word.'%')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('id', 'DESC')
                ->get();
            $total = count($total_data);
        }

        $response = [
            'message' => 'list data satuan',
            'data' => $satuan,
            'total' => $total
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function satuanSelectOption()
    {
        $satuans = Satuan::orderBy('nama_satuan', 'ASC')->get();

        $list_satuan = array();
        $i = 0;
        foreach ($satuans as $satuan) {
            $list_satuan[$i]['id'] =  $satuan->id;
            $list_satuan[$i]['nama_satuan'] =  $satuan->nama_satuan;
            $i++;
        }

        $response = [
            'message' => 'list data satuan',
            'data' => $list_satuan
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
            'nama_satuan' => ['required'],
            'simbol_satuan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $satuan = Satuan::create($request->all());
            $response = [
                'message' => 'data satuan berhasil dibuat',
                'data' => $satuan
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
        $satuan = Satuan::findOrFail($id);
        $response = [
            'message' => 'data satuan detail',
            'data' => $satuan
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
        $satuan = Satuan::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'nama_satuan' => ['required'],
            'simbol_satuan' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $satuan->update($request->all());
            $response = [
                'message' => 'data satuan telah diupdate',
                'data' => $satuan
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
        $satuan = Satuan::findOrFail($id);
        try{
            $satuan->delete();
            $response = [
                'message' => 'data satuan berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
