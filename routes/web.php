<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $keterangan = array(
        'berhasil' => true,
        'pesan' => "Endpoint Utama Restful API Atur Uang"
    );

    return response()->json(
        $keterangan, 200
    );
});
