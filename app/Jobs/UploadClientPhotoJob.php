<?php

namespace App\Jobs;

use App\Models\Client;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\LoyaltyCardMail;
use Illuminate\Support\Facades\Mail;

class UploadClientPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $photo;

    /**
     * Create a new job instance.
     *
     * @param Client $client
     * @param \Illuminate\Http\UploadedFile $photo
     * @return void
     */
    public function __construct(Client $client, $photo)
    {
        $this->client = $client;
        $this->photo = $photo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Envoyer directement le fichier à Cloudinary
        try {
            $path = Storage::disk('public')->path($this->photo);
            $uploadedFileUrl = Cloudinary::upload($path)->getSecurePath();
            $this->client->user->update(['photo' => $uploadedFileUrl, 'upload_failed' => false]); // Si succès, reset le champ
            // if ($this->client->user->role_id == 2) {
            //     dd($this->client->user->role_id == 2);
            //     Mail::to($this->client->user->login)->send(new LoyaltyCardMail($this->client));
            // }
        } catch (\Exception $e) {
            $this->client->user->update(['upload_failed' => true]); // Si échec, marquer le téléchargement comme échoué
            Log::error('Erreur lors du téléchargement de la photo sur Cloudinary : ' . $e->getMessage());
        }
    }
}
