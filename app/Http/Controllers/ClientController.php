<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;

use Spatie\QueryBuilder\QueryBuilder;
use App\Traits\ApiResponse;
use Exception;

class ClientController extends Controller
{
    use RestResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Détermine si on doit filtrer les clients ayant un compte utilisateur
        $include = $request->has('include') ? [$request->input('include')] : [];
        
        $query = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname'])
            ->allowedIncludes(['user']);
        
        // Filtrage selon le paramètre 'compte'
        if ($request->query('compte') === 'oui') {
            $query->whereNotNull('user_id');
        } elseif ($request->query('compte') === 'non') {
            $query->whereNull('user_id');
        }
    
        // Filtrage selon le paramètre 'active'
        if ($request->query('active') === 'oui') {
            $query->whereHas('user', function($q) {
                $q->where('etat', true);
            });
        } elseif ($request->query('active') === 'non') {
            $query->whereHas('user', function($q) {
                $q->where('etat', false);
            });
        }
    
        $clients = $query->get();
    
        return ApiResponse::SendResponse(ClientResource::collection($clients));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        try {
            DB::beginTransaction();
            // Traitement de la photo
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('photos', 'public');
                } else {
                    $photoPath = null;
                }
                $clientRequest = $request->only('surname', 'adresse', 'telephone');
                $clientRequest['photo'] = $photoPath; 
                $client = Client::create($clientRequest);

            if ( $request->has('user')){
                $user = User::create([
                    'nom' => $request->input('user.nom'),
                    'prenom' => $request->input('user.prenom'),
                    'login' => $request->input('user.login'),
                    'password' => $request->input('user.password'),
                    'etat' => $request->input('user.etat'),
                    'role_id' => $request->input('user.role'),
                ]);
                $user->client()->save($client);
            }
            DB::commit();
            return $this->sendResponse(new ClientResource($client),);
        }catch (Exception $e){
            DB::rollBack();
             return $this->sendResponse(new ClientResource($e->getMessage()),);
    }


    }

  
    public function show($id, Request $request)
    {
        $client = Client::find($id);
        
        if ($client) {
            if ($request->segment(4) === 'user') { // Vérifie si 'user' est le 4ème segment de l'URL
                $user = $client->user; // Assurez-vous que la relation 'user' est définie dans le modèle Client
                return ApiResponse::SendResponse([
                    'client' => new ClientResource($client),
                    'user' => new UserResource($user),
                ]);
            } else {
                return ApiResponse::SendResponse(new ClientResource($client));
            }
        } else {
            return ApiResponse::SendResponse(null, 'Client non trouvé', 404);
        }
    }
    
        
    public function getByTelephone(Request $request)
    {
        // Valider l'entrée
        $request->validate([
            'telephone' => 'required|string|regex:/^7[05678][0-9]{7}$/'
        ]);
        $client = Client::where('telephone', $request->input('telephone'))->first();
        if ($client) {
            return $this->sendResponse(new ClientResource($client));
        } else {
            return $this->sendError('Client non trouvé', [], 404);
        }
    }

}
