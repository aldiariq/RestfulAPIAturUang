<?php

namespace App\Http\Controllers\Api\Util;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function kirimpermintaanresetpassword(Request $request)
    {
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
            $status = Password::sendResetLink(
                $request->only('email')
            );
    
            if ($status === Password::RESET_LINK_SENT) {
                $keterangan = array(
                    'berhasil' => true,
                    'pesan' => "Berhasil Mengirim Pemintaan Reset Password"
                );
    
                return response()->json([
                    $keterangan, 200
                ]);
            } else {
                $keterangan = array(
                    'berhasil' => true,
                    'pesan' => "Gagal Mengirim Pemintaan Reset Password"
                );
    
                return response()->json([
                    $keterangan, 401
                ]);
            }
        }
    }

    public function halamanresetpassword($token)
    {
        return view('resetpassword.halamanreset', ['token' => $token]);
    }

    public function aksiresetpassword(Request $request)
    {
        $validasiInputan = FacadesValidator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validasiInputan->fails()) {
            return view('resetpassword.gagal');
        } else {
            $status = Password::reset(
                $request->only('email', 'password', 'token'),
                function ($user, $password) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
        
                    $user->setRememberToken(Str::random(60));
        
                    event(new PasswordReset($user));
                }
            );
        
            if ($status === Password::PASSWORD_RESET) {
                return view('resetpassword.berhasil');
            } else {
                return view('resetpassword.gagal');
            }
        }
    }
}
