<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface PhotoUploadServiceInterface {
    // public function uploadPhoto(Client $client);
    // public function getImageAsBase64(string $path): string;
    /**
     * Upload a file to a specific folder on Cloudinary
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @param string|null $customPath
     * @return string The secure URL of the uploaded file
     */
    public function uploadFile($file, $type, $customPath = null);

    /**
     * Convert a file URL to its Base64 representation
     *
     * @param string $fileUrl
     * @return string The Base64 encoded file content
     */
    public function fileToBase64($fileUrl);

    /**
     * Handle file upload and return the Base64 encoded version of the uploaded file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @param string|null $customPath
     * @return string The Base64 encoded file content
     */
    public function handleFileUpload($file, $type, $customPath = null);
}