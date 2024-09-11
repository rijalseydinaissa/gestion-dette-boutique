<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RetryPhotosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Récupérer les utilisateurs avec des photos échouées
        $users = User::where('upload_failed', true)->get();

        if ($users->isEmpty()) {
            Log::info('Aucune photo en attente de re-téléchargement.');
            return;
        }

        foreach ($users as $user) {
            if ($user->photo) {
                try {
                    // Relancer le job pour chaque utilisateur
                    UploadPhotoToCloudinaryJob::dispatch($user, $user->photo);
                    Log::info('Job relancé pour l\'utilisateur: ' . $user->id);
                } catch (\Exception $e) {
                    Log::error('Échec lors de la relance du téléchargement pour l\'utilisateur ' . $user->id . ': ' . $e->getMessage());
                }
            }
        }
    }
}
