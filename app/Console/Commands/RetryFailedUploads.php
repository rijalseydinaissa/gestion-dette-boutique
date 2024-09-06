<?php

namespace App\Console\Commands;

use App\Jobs\UploadClientPhotoJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedUploads extends Command
{
    protected $signature = 'photos:retry-uploads';
    protected $description = 'Relancer le téléchargement des photos qui ont échoué sur Cloudinary';
    public function handle()
    {
        // Récupérer les utilisateurs avec des photos qui ont échoué à se télécharger
        $users = User::where('upload_failed', true)->get();

        if ($users->isEmpty()) {
            $this->info('Aucune photo en attente de re-téléchargement.');
            return;
        }
        foreach ($users as $user) {
            if ($user->photo) {
                // Relancer le job pour chaque utilisateur
                try {
                    UploadClientPhotoJob::dispatch($user->client, $user->photo);
                    $this->info('Job relancé pour l\'utilisateur: ' . $user->id);
                } catch (\Exception $e) {
                    Log::error('Échec lors de la relance du téléchargement pour l\'utilisateur ' . $user->id . ': ' . $e->getMessage());
                }
            }
        }
    }
}
