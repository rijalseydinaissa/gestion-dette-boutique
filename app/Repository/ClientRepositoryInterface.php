<?php

namespace App\Repository;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepositoryInterface
{
    public function all($filters): Collection;
    public function create(array $clientData, array $userData = null): Client;
    public function find($id): ?Client;
     public function ByTelephone(string $telephone): Client;
}
