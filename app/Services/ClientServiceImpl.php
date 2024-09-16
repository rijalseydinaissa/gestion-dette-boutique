<?php

namespace App\Services;

use App\Repository\ClientRepositoryInterface;
use App\Events\ClientCreated;
use Illuminate\Support\Facades\Event;
use App\Models\Client;

class ClientServiceImpl implements ClientServiceInterface
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function createClient(array $data): Client
    {
        $userData = $data['user'] ?? null;
        $client = $this->clientRepository->create($data, $userData);
        // Lever l'événement pour le traitement asynchrone
        // Event::dispatch(new ClientCreated($client));
        return $client;
    }

    public function getAllClients(array $filters = [])
    {
        return $this->clientRepository->all($filters);
    }

    public function getClientById($id): ?Client
    {
        return $this->clientRepository->find($id);
    }

    public function getClientByTelephone(string $telephone): ?Client
    {
        return $this->clientRepository->ByTelephone($telephone);
    }
    public function getClientWithDettes($id): ?Client
    {
        return $this->clientRepository->findClientWithDettes($id);
    }
}
