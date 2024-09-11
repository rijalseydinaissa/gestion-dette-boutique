<?php
namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\UploadPhotoToCloudinaryJob;
use Illuminate\Support\Facades\Log;

class UploadPhotoToCloudinaryListener
{
    public function handle(ClientCreated $event)
    {

        $client = $event->client;
        $path=$event->path;
        // dd($path);
        // Vérifier si l'utilisateur a une photo
        //  dd($client->user);
        
            
            // Dispatcher le Job pour uploader la photo
            // Log::info("Dispatch du Job pour le client: " . $client->id);
            // Log::info('Type de fichier reçu : ' . get_class($client->user->photo));
            // dd("1");
            UploadPhotoToCloudinaryJob::dispatch($client, $path);
            // Log::info("Job dispatché avec succès.");

        
        
    }
}
