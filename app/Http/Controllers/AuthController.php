<?php

namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\RestResponseTrait;
use App\Models\Role;


class AuthController extends Controller
{
    use RestResponseTrait;
    
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'login' => ['required','string','max:255'],
            'password' => ['required','string','min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 402);
        }

        if(Auth::attempt(['login' => $request->login, 'password' => $request->password])){
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

    /**
     * Get the authenticated User.
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    public function register(Request $request)
    {
        $client = Client::find($request->client_id);
        $role = Role::find($request->role);
         if (!$role) {
             return response()->json(['error' => 'Role not found'], 404);
         }
        if ($client && is_null($client->user_id)) {
            $user = User::create([
                'login' => $request->login,
                'password' => $request->password,
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'etat' => $request->etat, 
                'role_id' => $role['id'],
            ]);
            $user->client()->save($client);
            return response()->json([
                'user' => $user,
            ], 201);
        } else {
            return response()->json(['error' => 'Client introuvable ou déjà associé à un utilisateur.'], 422);
        }
    }
}
