<?php

namespace App\Jobs;

use App\Models\Client;
use App\Facades\SmsServiceFacade as SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EnvoyerMessageDetteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Crée une nouvelle instance du job.
     */
    public function __construct()
    {
        //
    }

    /**
     * Exécute le job.
     */
    public function handle(): void
    {
        $clients = Client::with('dettes')->get();
        foreach ($clients as $client) {
            $totalDettes = $client->dettes->filter(function ($dette) {
                return $dette->montant_restant > 0;
            })->sum('montant_restant');
            if ($totalDettes > 0) {
                $nom = $client->user ? $client->user->prenom . ' ' . $client->user->nom : $client->surnom;
                $message = "Bonjour {$nom}, vous avez un total de {$totalDettes} FCFA de dettes chez DIOP E-SHOP.";
                SmsService::sendMessage('DIOL Walo-SHOP', '+221'.$client->telephone, $message);
            }
        }
    }
}
