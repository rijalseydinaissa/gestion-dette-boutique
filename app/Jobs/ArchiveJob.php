<?php

namespace App\Jobs;

use App\Models\Dette;
use MongoDB\Client as MongoClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;

class ArchiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dette;

    /**
     * Create a new job instance.
     *
     * @param Dette $dette
     */
    public function __construct(Dette $dette)
    {
        $this->dette = $dette;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Connexion à MongoDB
        $client = new MongoClient(env('MONGO_URI'));
        $collection = $client->selectCollection('archive_db', 'dettes_archives');

        // Préparer les données à archiver
        $detteData = [
            'client_id' => $this->dette->client_id,
            'montant' => $this->dette->montant,
            'articles' => $this->dette->articles->toArray(), // récupérer les articles
            'date' => now()
        ];

        // Archiver la dette et ses articles dans MongoDB
        $collection->insertOne($detteData);

        // Supprimer la dette de la base de données SQL
        // $this->dette->delete();
    }
}
