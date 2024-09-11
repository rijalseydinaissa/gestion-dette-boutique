<?php

namespace App\Services;

use MongoDB\Client as MongoClient;
use App\Models\Dette;
use Illuminate\Support\Facades\Log;

class ArchiveMongoService implements ArchiveDetteInterface
{
    public function archiveSettledDebts()
    {
        $settledDebts = Dette::where('montant', '=', 0)->get();

        $client = new MongoClient(env('MONGO_URI'));
        $collection = $client->selectCollection('archive_db', 'dettes_archives');

        foreach ($settledDebts as $dette) {
            $detteData = [
                'client_id' => $dette->client_id,
                'montant' => $dette->montant,
                'articles' => $dette->articles->toArray(),
                'date' => now()
            ];

            $collection->insertOne($detteData);
            $dette->delete();
        }

        Log::info('Archivage MongoDB terminé.');
    }
}
