<?php
namespace App\Repository;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Exceptions\ClientCreationException;
use App\Exceptions\UserCreationException;
use Illuminate\Support\Facades\Log;

class ClientRepositoryImpl implements ClientRepositoryInterface
{
    public function all($filters): Collection
    {
        $query = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname'])
            ->allowedIncludes(['user']);
        
        if (isset($filters['compte'])) {
            if ($filters['compte'] === 'oui') {
                $query->whereNotNull('user_id');
            } elseif ($filters['compte'] === 'non') {
                $query->whereNull('user_id');
            }
        }
        if (isset($filters['active'])) {
            if ($filters['active'] === 'oui') {
                $query->whereHas('user', function($q) {
                    $q->where('etat', true);
                });
            } elseif ($filters['active'] === 'non') {
                $query->whereHas('user', function($q) {
                    $q->where('etat', false);
                });
            }
        }
        return $query->get();
    }

    public function create(array $clientData, array $userData = null): Client
    {
        DB::beginTransaction();
        try {
            $request = request();
            $clientData = $request->only(['surname', 'adresse', 'telephone', 'categorie_id']);
            if ($clientData['categorie_id'] != 2 && $request->filled('max_montant')) {
                throw new \Exception('Un client qui n\'est pas Silver ne peut pas avoir de max_montant.');
            }
            $clientData['max_montant'] = $clientData['categorie_id'] == 2 ? $request->input('max_montant') : null;
            $client = Client::create($clientData);
            if ($userData) {
                $user = User::create($userData);
                $user->client()->save($client);
                // return $user;
            }
            DB::commit();
            return $client->load('user');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du client ou de l\'utilisateur : ' . $e->getMessage());
            if ($userData) {
                throw new UserCreationException();
            }
            throw new ClientCreationException();
        }
    }
    

    public function find($id): ?Client
    {
        return Client::find($id);
    }

    public function ByTelephone(string $telephone): ?Client
{
    return Client::where('telephone', $telephone)->first();
}

    public function make(array $data): Client
    {
        return Client::make($data);  // Utilise make() d'Eloquent ici
    }
    public function findClientWithDettes($id): ?Client
    {
        return Client::with('dettes')->find($id);
    }
}
