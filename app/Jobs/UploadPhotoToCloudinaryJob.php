<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\PhotoUploadService;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UploadPhotoToCloudinaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $photo;

    /**
     * Create a new job instance.
     *
     * @param Client $client
     * @param \Illuminate\Http\UploadedFile $photo
     */
    public function __construct(Client $client, $photo)
    {
        // dd('2');
        $this->client = $client;
        $this->photo = $photo;
    }

    /**
     * Execute the job.
     */
//     public function handle(PhotoUploadService $photoUploadService)
// {
//     try {
//         // Upload de la photo via le service
//         if ($this->photo instanceof \Illuminate\Http\UploadedFile) {
//             $uploadedUrl = $photoUploadService->uploadFile($this->photo, 'image');
//         } else {
//             Log::error('Le fichier passé n\'est pas valide.');
//             return;
//         }

//         if ($uploadedUrl) {
//             // Mettre à jour l'utilisateur avec l'URL de la photo
//             $this->client->user->photo = $uploadedUrl;
//             $this->client->user->save();
//         } else {
//             Log::error('Échec lors du téléchargement de la photo.');
//         }
//     } catch (\Exception $e) {
//         Log::error('Erreur dans UploadPhotoToCloudinaryJob : ' . $e->getMessage());
//     }
// }
public function handle()
{
     try {
        // dd($this->client);
        $absolutePath = Storage::path($this->photo);
        // dd($absolutePath);
        if (!$absolutePath || !file_exists($absolutePath)) {
            throw new \Exception('Le fichier n\'existe pas ou le chemin est invalide.');
        }
        $uploadedFileUrl = Cloudinary::upload($absolutePath)->getSecurePath();
      
        $this->client->user->update(['photo' => $uploadedFileUrl, 'upload_failed' => false]); 

    } catch (\Exception $e) {
        $localFilePath = Storage::url($absolutePath); // Obtenir l'URL relative pour l'accès public

        // Mettre à jour le chemin de la photo avec le chemin local dans la base de données
        $this->client->user->update([
            'photo' => $this->photo,
            'upload_failed' => true // Marquer l'échec de l'upload sur Cloudinary
        ]);

        // Loguer l'erreur
        Log::error('Erreur lors du téléchargement de la photo sur Cloudinary : ' . $e->getMessage());
}
}
}