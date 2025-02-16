<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class AuthentificationPassport implements AuthentificationServiceInterface
{
    public function authenticate(Request $request)
    {
        if(Auth::attempt(['login' => $request->login, 'password' => $request->password])){
            // dd($request);
            $user = Auth::user();
            $token = $user->createToken('LaravelPassportAuth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
