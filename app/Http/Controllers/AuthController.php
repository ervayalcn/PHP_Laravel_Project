<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ], [
            'name.required' => 'Lütfen isminizi giriniz.',
            'email.required' => 'Lütfen mail adresinizi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Lütfen şifre giriniz.',
            'password_confirmation.required' => 'Şifreyi tekrar giriniz.',
            'password_confirmation.same' => 'Şifreler eşleşmiyor.',
        ]);

        $user = new User([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        if ($user->save()) {
            $token = $user->createToken('kle-api')->plainTextToken;
            $user->api_token = $token;
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Kullanıcı başarıyla kaydedildi.',
                'data' => $user,
            ], 201);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Kullanıcı kaydedilirken bir hata oluştu.'
            ], 500);
        }
    }
 
    public function login(Request $request)
{
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Lütfen mail adresinizi giriniz.',
            'password.required' => 'Lütfen şifre giriniz.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('kle-api')->plainTextToken;
            $user->api_token = $token;
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Giriş başarılı.',
                    'user' => $user,
                    'token' => $token,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Girilen bilgilerle eşleşen bir kayıt bulunmamaktadır.',
            ], 401);
        }
    }
}

public function logout(Request $request)
{
    $token = $request->session()->get('token');

    if ($token) {
        $token->delete();
        return response()->json(['message' => 'Başarıyla çıkış yapıldı']);
    } else {
        return response()->json(['message' => 'Kullanıcı girişi bulunamadı'], 401);
    }
}

}
 