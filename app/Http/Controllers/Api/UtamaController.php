<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Uang;
use App\Models\Catatan;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class UtamaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = $request->user()->id;

        $validasiInputan = FacadesValidator::make($request->all(), [
            'jumlahcatatan' => 'required',
            'jeniscatatan' => 'required|in:PEMASUKAN,PENGELUARAN',
        ]);

        if ($validasiInputan) {
            $datauang = Uang::where('user_id', $user_id)->first();

            if ($request->jeniscatatan == "PEMASUKAN") {
                $datauang->jumlahuang = $datauang->jumlahuang + $request->jumlahcatatan;

                if ($datauang->save()) {
                    $datacatatan = array(
                        'jumlahcatatan' => $request->jumlahcatatan,
                        'jeniscatatan' => $request->jeniscatatan,
                        'user_id' => $user_id
                    );

                    $simpancatatan = Catatan::create($datacatatan);

                    if ($simpancatatan) {
                        $keterangan = array(
                            'berhasil' => true,
                            'pesan' => "Berhasil Menambahkan Catatan Keuangan"
                        );
            
                        return response()->json([
                            $keterangan
                        ]);
                    } else {
                        $keterangan = array(
                            'berhasil' => false,
                            'pesan' => "Gagal Menambahkan Catatan Keuangan"
                        );
            
                        return response()->json([
                            $keterangan
                        ]);
                    }
                    
                } else {
                    $keterangan = array(
                        'berhasil' => false,
                        'pesan' => "Gagal Menambahkan Catatan Keuangan"
                    );
        
                    return response()->json([
                        $keterangan
                    ]);
                }
            } else if($request->jeniscatatan == "PENGELUARAN") {
                if ($datauang->jumlahuang < $request->jumlahcatatan) {
                    $keterangan = array(
                        'berhasil' => false,
                        'pesan' => "Gagal Menambahkan Catatan Keuangan, Pastikan Uang Simpanan Mencukupi"
                    );
        
                    return response()->json([
                        $keterangan
                    ]);
                }else {
                    $datauang->jumlahuang = $datauang->jumlahuang - $request->jumlahcatatan;

                    if ($datauang->save()) {
                        $datacatatan = array(
                            'jumlahcatatan' => $request->jumlahcatatan,
                            'jeniscatatan' => $request->jeniscatatan,
                            'user_id' => $user_id
                        );

                        $simpancatatan = Catatan::create($datacatatan);

                        if ($simpancatatan) {
                            $keterangan = array(
                                'berhasil' => true,
                                'pesan' => "Berhasil Menambahkan Catatan Keuangan"
                            );
                
                            return response()->json([
                                $keterangan
                            ]);
                        } else {
                            $keterangan = array(
                                'berhasil' => false,
                                'pesan' => "Gagal Menambahkan Catatan Keuangan"
                            );
                
                            return response()->json([
                                $keterangan
                            ]);
                        }
                        
                    } else {
                        $keterangan = array(
                            'berhasil' => false,
                            'pesan' => "Gagal Menambahkan Catatan Keuangan"
                        );
            
                        return response()->json([
                            $keterangan
                        ]);
                    }
                    
                }
            }
        } else {
            $keterangan = array(
                'berhasil' => false,
                'pesan' => "Pastikan Inputan Memenuhi Syarat"
            );

            return response()->json([
                $keterangan
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user_id = $request->user()->id;
        $datauang = Uang::where('user_id', $user_id)->get();
        $datacatatan = Catatan::where('user_id', $user_id)->get();

        $keterangan = array(
            'berhasil' => true,
            'pesan' => "Berhasil Mendapatkan Data Keuangan",
            'uang' => $datauang,
            'catatan' => $datacatatan
        );

        return response()->json(
            $keterangan
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function destroy(Request $request)
    {
        $user_id = $request->user()->id;
        $datauang = Uang::where('user_id', $user_id)->first();
        $datauang->jumlahuang = 0;
        $datauang->save();

        $datacatatan = Catatan::where('user_id', $user_id);
        $datacatatan->delete();

        if ($datauang && $datacatatan) {
            $datacatatan = array(
                'jumlahcatatan' => 0,
                'jeniscatatan' => 'PEMASUKAN',
                'user_id' => $user_id
            );

            $catatanawal = Catatan::create($datacatatan);

            if ($catatanawal) {
                $keterangan = array(
                    'berhasil' => true,
                    'pesan' => "Berhasil Mereset Catatan"
                );
    
                return response()->json([
                    $keterangan
                ]);
            }else {
                $keterangan = array(
                    'berhasil' => false,
                    'pesan' => "Gagal Mereset Catatan"
                );
    
                return response()->json([
                    $keterangan
                ]);
            }
        }else {
            $keterangan = array(
                'berhasil' => false,
                'pesan' => "Gagal Mereset Catatan"
            );

            return response()->json([
                $keterangan
            ]);
        }
    }
}
