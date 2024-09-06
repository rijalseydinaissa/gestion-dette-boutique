<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class CloudinaryService implements CloudinaryServiceInterface
{
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