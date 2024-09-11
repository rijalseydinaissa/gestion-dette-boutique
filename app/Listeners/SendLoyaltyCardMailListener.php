<?php


namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\GenerateQrCodeAndSendMailJob;
use App\Jobs\SendLoyaltyCardMailJob;

class SendLoyaltyCardMailListener
{
    /**
     * Handle the event.
     *
     * @param ClientCreated $event
     * @return void
     */
    public function handle(ClientCreated $event)
    {
        $client = $event->client;
        // Dispatch du job combiné pour générer le QR code et envoyer l'e-mail
        SendLoyaltyCardMailJob::dispatch($client);
        // dd($event);
    }
}

