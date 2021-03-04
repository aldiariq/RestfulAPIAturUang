<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Uang;
use App\Models\Catatan;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{

    public function resetpassword(Request $request){
        $validasiInputan = FacadesValidator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validasiInputan->fails()) {
            $keterangan = array(
                'berhasil' => false,
                'pesan' => "Pastikan Inputan Memenuhi Syarat"
            );

            return response()->json([
                $keterangan, 401
            ]);
        } else {

            $resetpassword = Password::sendResetLink(
                $request->only('email')
            );

            if ($resetpassword === Password::RESET_LINK_SENT) {
                $keterangan = array(
                    'berhasil' => true,
                    'pesan' => "Berhasil Mengirim Permintaan Reset Password"
                );
    
                return response()->json([
                    $keterangan, 200
                ]);
            } else {
                $keterangan = array(
                    'berhasil' => false,
                    'pesan' => "Gagal Mengirim Permintaan Reset Password"
                );
    
                return response()->json([
                    $keterangan, 401
                ]);
            }
        }
    }

    public function masuk(Request $request)
    {
        $validasiInputan = FacadesValidator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if ($validasiInputan->fails()) {
            $keterangan = array(
                'berhasil' => false,
                'pesan' => "Pastikan Inputan Memenuhi Syarat",
                'token' => null,
                'user' => null
            );

            return response()->json(
                $keterangan,
                401
            );
        } else {
            $datamasuk = array(
                'email' => $request->email,
                'password' => $request->password
            );

            $masukpengguna = Auth::attempt($datamasuk);

            if ($masukpengguna) {
                $datauser = Auth::user();

                if ($datauser->hasVerifiedEmail()) {
                    $keterangan = array(
                        'berhasil' => true,
                        'pesan' => "Berhasil Masuk",
                        'token' => $datauser->createToken('AuthToken')->accessToken,
                        'user' => $datauser
                    );

                    return response()->json(
                        $keterangan,
                        200
                    );
                } else {
                    $keterangan = array(
                        'berhasil' => false,
                        'pesan' => "Pastikan Email Sudah Terverifikasi",
                        'token' => null,
                        'user' => null
                    );

                    return response()->json(
                        $keterangan,
                        401
                    );
                }
            } else {
                $keterangan = array(
                    'berhasil' => false,
                    'pesan' => "Gagal Masuk",
                    'token' => null,
                    'user' => null
                );

                return response()->json(
                    $keterangan,
                    401
                );
            }
        }
    }

    public function daftar(Request $request)
    {
        $validasiInputan = FacadesValidator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password1' => 'required|min:8',
            'password2' => 'required|min:8|same:password1'
        ]);

        if ($validasiInputan->fails()) {
            $keterangan = array(
                'berhasil' => false,
                'pesan' => "Pastikan Inputan Memenuhi Syarat"
            );

            return response()->json(
                $keterangan,
                401
            );
        } else {
            $datadaftar = array(
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => bcrypt($request->password1)
            );

            $daftarpengguna = User::create($datadaftar);

            if ($daftarpengguna) {

                $datauang = array(
                    'jumlahuang' => 0,
                    'user_id' => $daftarpengguna->id
                );

                $uangawal = Uang::create($datauang);

                $datacatatan = array(
                    'jumlahcatatan' => 0,
                    'jeniscatatan' => 'PEMASUKAN',
                    'user_id' => $daftarpengguna->id
                );

                $catatanawal = Catatan::create($datacatatan);

                if ($uangawal && $catatanawal) {

                    $daftarpengguna->sendEmailVerificationNotification();
                    
                    $keterangan = array(
                        'berhasil' => true,
                        'pesan' => "Berhasil Mendaftarkan Pengguna",
                    );

                    return response()->json(
                        $keterangan,
                        200
                    );
                } else {
                    $keterangan = array(
                        'berhasil' => false,
                        'pesan' => "Gagal Mendaftarkan Pengguna"
                    );

                    return response()->json(
                        $keterangan,
                        401
                    );
                }
            } else {
                $keterangan = array(
                    'berhasil' => false,
                    'pesan' => "Gagal Mendaftarkan Pengguna"
                );

                return response()->json(
                    $keterangan,
                    401
                );
            }
        }
    }

    public function gantipassword(Request $request){
        $validasiInputan = FacadesValidator::make($request->all(), [
            'id' => 'required',
            'passwordlama' => 'required',
            'passwordbaru1' => 'required',
            'passwordbaru2' => 'required|same:passwordbaru1'
        ]);

        if ($validasiInputan->fails()) {
            $keterangan = array(
                'berhasil' => false,
                'pesan' => "Pastikan Inputan Memenuhi Syarat"
            );

            return response()->json([
                $keterangan, 401
            ]);
        } else {
            $datauser = User::find($request->id)->first();

            if (Hash::check($request->passwordlama, $datauser->password)) {
                $datauser->password = bcrypt($request->passwordbaru1);

                if ($datauser->save()) {
                    $keterangan = array(
                        'berhasil' => true,
                        'pesan' => "Berhasil Mengubah Password"
                    );
    
                    return response()->json([
                        $keterangan, 200
                    ]);
                } else {
                    $keterangan = array(
                        'berhasil' => false,
                        'pesan' => "Gagal Mengubah Password"
                    );
    
                    return response()->json([
                        $keterangan, 401
                    ]);
                }
                
            }else {
                $keterangan = array(
                    'berhasil' => false,
                    'pesan' => "Pastikan Password Lama Benar"
                );

                return response()->json([
                    $keterangan, 401
                ]);
            }
        }
    }
}
