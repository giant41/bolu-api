<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ResepController extends Controller
{
    public function __construct()
    {
        $this->resepTable = new Resep;
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

        $data_resep = $this->resepTable->getAllResep($search_word, $offset, $limit);
        $array_resep = array();
        $i = 0;
        foreach ($data_resep['resep'] as $resep) {
            $array_resep[$i]['id'] =  $resep->id;
            $array_resep[$i]['nama_resep'] =  $resep->nama_resep;
            $array_resep[$i]['keterangan'] =  $resep->keterangan;
            $array_resep[$i]['created_by'] =  $resep->name;
            $array_resep[$i]['created_at'] =  $resep->created_at;
            $i++;
        }    

        $response = [
            'message' => 'list data resep',
            'data' => $array_resep,
            'total' => $data_resep['total']
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
            'nama_resep' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $resep = Resep::create($request->all());
            $response = [
                'message' => 'data resep berhasil dibuat',
                'data' => $resep
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
        $resep = Resep::findOrFail($id);

        $response = [
            'message' => 'data resep detail',
            'data' => $resep
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDetail($id)
    {
        $data_resep = $this->resepTable->getDetail($id);

        $resep = array();
        $resep['nama_resep'] = $data_resep[0]->nama_resep;
        $resep['keterangan'] = $data_resep[0]->keterangan;
        $resep['created_by'] = $data_resep[0]->created_name;
        $resep['updated_by'] = $data_resep[0]->updated_name;
        $resep['created_at'] = $data_resep[0]->created_at;
        $resep['updated_at'] = $data_resep[0]->updated_at;

        $response = [
            'message' => 'data resep detail',
            'data' => $resep
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
        $resep = Resep::findOrFail($id);
        $userId = Auth::user()->id; 
        $request->request->add(['updated_by' => $userId]);
        $validator = Validator::make($request->all(), [
            'nama_resep' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $resep->update($request->all());
            $response = [
                'message' => 'data resep telah diupdate',
                'data' => $resep
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
        $resep = Resep::findOrFail($id);
        try{
            $resep->delete();
            $response = [
                'message' => 'data resep berhasil dihapus'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed" . $e->errorInfo
            ]);
        }
    }
}
