<?php

namespace App\Http\Controllers;

use App\Models\OrderBahanBaku;
use App\Models\OrderBahanBakuDetail;
use App\Models\OrderTempBahanBakuDetail;
use App\Models\BahanBaku;
use App\Models\BahanBakuDetail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderBahanBakuController extends Controller
{
    public function __construct()
    {
        $this->tempOrderTable = new OrderTempBahanBakuDetail;
        $this->orderBahanBakuTable = new OrderBahanBaku;
        $this->orderBahanBakuDetailTable = new OrderBahanBakuDetail;
        $this->bahanBakuTable = new BahanBaku;
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

        $order_bahan_baku = $this->orderBahanBakuTable->getAllOrderBahanBaku($search_word, $offset, $limit);

        $bahan_baku = array();
        $i = 0;
        foreach ($order_bahan_baku['order_bahan_baku'] as $bahan) {
            $bahan_baku[$i]['id'] =  $bahan->id;
            $bahan_baku[$i]['nama_suplayer'] =  $bahan->nama_suplayer;
            $bahan_baku[$i]['nomor_order'] =  $bahan->nomor_order;
            $bahan_baku[$i]['tanggal_order'] =  $bahan->tanggal_order;
            $bahan_baku[$i]['status'] =  $bahan->status;
            $bahan_baku[$i]['created_by'] =  $bahan->name;
            $bahan_baku[$i]['created_at'] =  $bahan->created_at;

            $count_order_detail = $this->orderBahanBakuDetailTable->getCountOrderByNomorOrder($bahan->nomor_order);
            $bahan_baku[$i]['total_item'] =  $count_order_detail[0]->total_item;
            $bahan_baku[$i]['jumlah_total'] =  $count_order_detail[0]->jumlah_total;
            $i++;
        }

        $response = [
            'message' => 'list data bahan baku',
            'data' => $bahan_baku,
            'total' => $order_bahan_baku['total']
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lastOrder()
    {
        $bahan_baku = DB::table('order_bahan_bakus')
                    ->limit(1)
                    ->orderBy('id', 'DESC')
                    ->get();
        $response = [
            'message' => 'data order bahan baku terakhir',
            'data' => $bahan_baku[0]->nomor_order
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

        $validator = Validator::make($request->all(), [
            'id_suplayer' => ['required'],
            'nomor_order' => ['required'],
            'tanggal_order' => ['required']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            // order save to OrderBahanBaku table
            $bahan_baku = OrderBahanBaku::create($request->all());

            // get all data from tempolary order table
            $list_temp_orders = $this->tempOrderTable->getAllData();
            $list_order = array();
            // $list_bahan_baku_detail = array();

            $list_order['id_order_bahan_baku'] =  $bahan_baku->nomor_order;
            $list_order['id_suplayer'] =  $bahan_baku->id_suplayer;
            $list_order['nomor_order'] =  $bahan_baku->nomor_order;
            $list_order['tanggal_order'] =  $bahan_baku->tanggal_order;

            foreach ($list_temp_orders as $order) {
                // list for order detail
                $list_order['id_bahan_baku'] =  $order->id_bahan_baku;
                $list_order['jumlah_pesanan'] =  $order->jumlah_pesanan;
                $list_order['harga_satuan'] =  $order->harga_satuan;
                $list_order['id_bahan_baku'] =  $order->id_bahan_baku;
                $list_order['harga_satuan'] =  $order->harga_satuan;
                $list_order['jumlah_order'] =  $order->jumlah_pesanan;

                // save temp order to detail order table
                $save_detail_order = OrderBahanBakuDetail::create($list_order);

                // save temp order to bahan baku detail
                $save_to_detail_bahan_baku = BahanBakuDetail::create($list_order);

                // update stok bahan baku
                $bahan_baku = BahanBaku::findOrFail($order->id_bahan_baku);
                $stok = $bahan_baku->stok + $order->jumlah_pesanan;
                $update_Stock = $this->bahanBakuTable->updateStok($order->id_bahan_baku, $stok);
            }
            $response = [
                'message' => 'data order bahan baku berhasil disimpan',
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
