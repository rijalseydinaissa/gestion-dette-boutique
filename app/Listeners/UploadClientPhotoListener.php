<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\UploadClientPhotoJob;

class UploadClientPhotoListener
{
    /**
     * Handle the event.
     *
     * @param  ClientCreated  $event
     * @return void
     */
    public function handle(ClientCreated $event)
    {
        $path = $event->photo->store('uploads', 'public');
        // Déléguer la tâche au Job
        UploadClientPhotoJob::dispatch($event->client, $path);
    }
}
