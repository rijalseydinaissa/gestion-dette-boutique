<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientServiceInterface
{
    public function getAllClients(array $filters): Collection;
    public function createClient(array $data): Client;
    public function getClientById($id): ?Client;
    public function getClientByTelephone(string $telephone): ?Client;
}