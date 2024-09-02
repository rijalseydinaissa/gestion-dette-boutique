<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\RestResponseTrait;
use App\Models\Role;
class UserController extends Controller
{
    //
    use RestResponseTrait;

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nom' => ['required','string','max:255'],
            'prenom' => ['required','string','max:255'],
            'login' => ['required','string','max:255','unique:users'],
            'password' => ['required','string','min:8','confirmed'],
            'etat' => ['required','string'],
            'role' => ['required','numeric'],
        ]);
         $role = Role::find($request->role);
         if (!$role) {
             return response()->json(['error' => 'Role not found'], 404);
         }
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'login' => $request->login,
            'password' =>  $request->password,
            'etat' => $request->etat,
            'role_id' => $role['id'],
        ]);
        $role->users()->save($user);
        return $this->sendResponse(['message' =>'User created successfully']);
    }

    //get all users
    public function index( Request $request){

        $role = $request->query('role');
        $etat = $request->query('etat');

        $query = User::query();
        if (!$role==null) {
            $query->where('role_id', $role);
        }
        if (!$etat==null) {
            $query->where('etat', $etat);
        }
        $users = $query->get();
        // $users = User::all();
        return ApiResponse::sendResponse(UserResource::collection($users)); 
    }

}
