<?php

namespace App\Jobs;

use App\Services\NotificationManager;
use App\Models\Client;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeeklyDebtReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificationManager;

    public function __construct(NotificationManager $notificationManager)
    {
        $this->notificationManager = $notificationManager;
    }

    public function handle()
    {
        // Récupérer les clients avec des dettes non soldées
        $clients = Client::whereHas('dettes', function ($query) {
            $query->nonSoldes();
        })->get();

        foreach ($clients as $client) {
            $montantRestant = $client->dettes->sum('montant_restant');
            $message = "Vous avez une dette de {$montantRestant} à payer.";

            // Envoyer la notification par SMS
            if ($this->notificationManager->sendNotification($client->telephone, $message)) {
                // Enregistrer la notification dans la base de données
                Notification::create([
                    'client_id' => $client->id,
                    'message' => $message,
                ]);
            }
        }
    }
}
