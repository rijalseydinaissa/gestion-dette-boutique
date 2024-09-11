<?php

namespace App\Console\Commands;

use App\Jobs\UploadClientPhotoJob;
use App\Jobs\UploadPhotoToCloudinaryJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\RetryPhotosJob;

class RetryFailedUploads extends Command
{
    protected $signature = 'photos:retry-uploads';
    protected $description = 'Relancer le téléchargement des photos qui ont échoué sur Cloudinary';
    public function handle()
    {
        // Appeler le job pour réessayer les uploads échoués
        RetryPhotosJob::dispatch();

        $this->info('Les uploads échoués sont en cours de relance.');
    }
}
