<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuplayerController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\BahanBakuDetailController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\ResepDetailController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\HargaProdukController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\PemesanController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PemesananDetailController;
use App\Http\Controllers\OrderTempBahanBakuController;
use App\Http\Controllers\OrderBahanBakuController;

use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Publict routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::post('/satuan/list', [SatuanController::class, 'index']);

// Protacted routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    // Route::get('/suplayer', [SuplayerController::class, 'index']);
    // Route::post('/suplayer', [SuplayerController::class, 'store']);
    // Route::get('/suplayer/{id}', [SuplayerController::class, 'show']);
    // Route::put('/suplayer/{id}', [SuplayerController::class, 'update']);
    // Route::delete('/suplayer/{id}', [SuplayerController::class, 'destroy']);
    
    // route data suplayer
    Route::post('/suplayer/list', [SuplayerController::class, 'index']);
    Route::get('/suplayer/alldata', [SuplayerController::class, 'allSuplayer']);
    Route::resource('/suplayer', SuplayerController::class)->except(['create', 'edit', 'index']);

    // route data satuan
    Route::resource('/satuan', SatuanController::class)->except(['create', 'edit', 'index']);
    Route::post('/satuan/list', [SatuanController::class, 'index']);
    Route::get('/satuan', [SatuanController::class, 'satuanSelectOption']);

    // route data aset 
    Route::resource('/aset', AssetController::class)->except(['create', 'edit', 'index']);
    Route::post('/aset/list', [AssetController::class, 'index']);

    // route data bahan baku
    Route::get('/bahan-baku/alldata', [BahanBakuController::class, 'allBahanBaku']);
    Route::post('/bahan-baku/list', [BahanBakuController::class, 'index']);
    Route::resource('/bahan-baku', BahanBakuController::class)->except(['create', 'edit', 'index', 'allBahanBaku']);

    
    // route data detail
    Route::resource('/bahan-baku-detail', BahanBakuDetailController::class)->except(['create', 'edit', 'index']);
    Route::post('/bahan-baku-detail/list', [BahanBakuDetailController::class, 'index']);


    // route temporary order
    Route::get('/temp-order/by-bahan-baku/{id}', [OrderTempBahanBakuController::class, 'showByBahanBakuId']);
    Route::get('/temp-order/remove-by-userid', [OrderTempBahanBakuController::class, 'removeByUserId']);
    Route::resource('/temp-order', OrderTempBahanBakuController::class)->except(['create', 'edit']);

    // route order bahan baku
    Route::post('/order-bahan-baku/list', [OrderBahanBakuController::class, 'index']);
    Route::get('/order-bahan-baku/last-order', [OrderBahanBakuController::class, 'lastOrder']);
    Route::resource('/order-bahan-baku', OrderBahanBakuController::class)->except(['create', 'edit', 'index']);


    Route::resource('/resep', ResepController::class)->except(['create', 'edit']);
    Route::resource('/resep-detail', ResepDetailController::class)->except(['create', 'edit']);
    Route::resource('/produk', ProdukController::class)->except(['create', 'edit']);
    Route::resource('/harga-produk', HargaProdukController::class)->except(['create', 'edit']);
    Route::resource('/produksi', ProduksiController::class)->except(['create', 'edit', 'update', 'destroy']);

    Route::resource('/pemesan', PemesanController::class)->except(['create', 'edit']);
    Route::resource('/pemesanan', PemesananController::class)->except(['create', 'edit']);
    Route::resource('/pemesanan-detail', PemesananDetailController::class)->except(['create', 'edit']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/{id}', [AuthController::class, 'getUser']);

});

