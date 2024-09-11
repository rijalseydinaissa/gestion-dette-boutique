<?php

namespace App\Observers;

use App\Models\Client;
use App\Events\ClientCreated;

class ClientObserver
{
    /**
     * Handle the Client "created" event.
     */
    public $photo;
    public function created(Client $client): void
    {
        // Déclenchement de l'événement ClientCreated
        // dd("1");
        $request = request();
        $photo = $request->file('user.photo');

        if ($photo) {
            $path = $photo->store('public/photos');
            $client->photo = $path;
        }
        event(new ClientCreated($client, $path ?? null));
    }
    

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}
