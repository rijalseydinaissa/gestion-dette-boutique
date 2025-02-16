<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Facades\ClientServiceFacade;
use App\Models\User;
use App\Services\ClientServiceImpl;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use App\Facades\ClientServiceInterface;
use App\Http\Resources\UserResource;
use App\Repository\ClientRepositoryInterface;
use App\Services\ClientServiceInterface;

use Spatie\QueryBuilder\QueryBuilder;
use App\Traits\ApiResponse;
use Exception;


class ClientController extends Controller
{
    use RestResponseTrait;
    protected $clientService;

    public function __construct(ClientServiceImpl $clientService)
    {
        $this->clientService = $clientService;
    }
    
        public function index(Request $request)
        {
            $filters = [
                'compte' => $request->query('compte'),
                'active' => $request->query('active'),
            ];
            $clients = ClientServiceFacade::getAllClients($filters);
            return response()->json(ClientResource::collection($clients));
        }
        public function store(StoreClientRequest $request){
            $clientData = $request->all();
            $client = ClientServiceFacade::createClient($clientData);
            return response()->json(new ClientResource($client), 201);
        }
        public function show($id){
            $client = ClientServiceFacade::getClientById($id);
            if(!$client){
                return $this->errorResponse('Client not found', 404);
            }
            return response()->json(new ClientResource($client));
        }

        public function getByTelephone(Request $request)
        {
            // Valider que le numéro de téléphone est présent dans le corps de la requête
            $validatedData = $request->validate([
                'telephone' => 'required|string'
            ]);
            $telephone = $validatedData['telephone'];
            $client = ClientServiceFacade::getClientByTelephone($telephone);
            if (!$client) {
                return ('Client not found');
            }
            return response()->json(new ClientResource($client));
        }
        public function showClientWithUser($id)
        {
            $client = Client::with('user')->findOrFail($id);
            if (!$client) {
                return $this->errorResponse('Client not found', 404);
            }
            return response()->json([
                'client' => new ClientResource($client),
                'user' => new UserResource($client->user) // Retourner l'utilisateur associé
            ]);
        }
        // getClientWithDettes

        public function paiementsByDette($id)
        {
            try {
                // Récupérer la dette avec ses paiements associés
                $dette = $this->clientService->getClientWithDettes($id);
        
                if (!$dette) {
                    return response()->json([
                        'statut' => 'echec',
                        'message' => 'Aucune dette trouvée avec cet ID'
                    ], 404);
                }
        
                return response()->json([
                    'statut' => 'success',
                    'data' => [
                        'dette' => $dette,  // Inclure l'objet dette
                        'paiements' => $dette->paiements  // Inclure les paiements associés
                    ]
                ], 200);
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }
        

}
    




