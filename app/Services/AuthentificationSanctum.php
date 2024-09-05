<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthentificationSanctum implements AuthentificationServiceInterface
{
    public function authenticate(Request $request)
    {
        if(Auth::attempt(['login' => $request->login, 'password' => $request->password])){
            $user = Auth::user() ;
            $token = $user->createToken('SanctumAuthToken')->plainTextToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
