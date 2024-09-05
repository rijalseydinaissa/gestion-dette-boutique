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
use App\Services\AuthentificationServiceInterface;


class AuthController extends Controller
{
    use RestResponseTrait;
    
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        return $this->authService->authenticate($request);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
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
