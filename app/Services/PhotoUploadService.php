<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Client;

class PhotoUploadService
{
    /**
     * Upload the user's photo to Cloudinary.
     *
     * @param Client $client
     * @return bool|string
     */
    // public function uploadPhoto(Client $client)
    // {
    //     try {
    //         // Récupérer le chemin de la photo à partir du stockage local
    //         $url = Storage::disk('public')->url($client->user->photo);
    //         // Upload de la photo sur Cloudinary
    //         $uploadedFileUrl = Cloudinary::upload($url)->getSecurePath();
    //         // Mettre à jour l'utilisateur avec l'URL de la photo
    //         $client->user->photo = $uploadedFileUrl;
    //         $client->user->save();

    //         return true;
    //     } catch (\Exception $e) {
    //         Log::error('Erreur lors du téléchargement de la photo sur Cloudinary : ' . $e->getMessage());
    //         return false;
    //     }
    // }

    public function uploadFile($file, $type, $customPath = null)
    {
        $folder = $customPath ?? ($type === 'image' ? 'images' : 'documents');

        $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder
        ]);
        // Log::info('Uploading file', ['file' => $uploadedFile]);
        return $uploadedFile->getSecurePath();
    }

    public function fileToBase64($fileUrl)
    {
        $fileContent = file_get_contents($fileUrl);

        return base64_encode($fileContent);
    }

    public function handleFileUpload($file, $type, $customPath = null)
    {
        $fileUrl = $this->uploadFile($file, $type, $customPath);

        return $this->fileToBase64($fileUrl);
    }
}
        // Convert the uploaded file to Base64
