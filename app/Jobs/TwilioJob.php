<?php

namespace App\Jobs;

use App\Services\TwilioService;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TwilioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TwilioService $twilioService)
    {
        // Récupérer tous les clients
        $clients = Client::with('dettes.paiements')->get();

        foreach ($clients as $client) {
            // Calculer la somme totale des dettes pour chaque client
            $totalDebt = $client->dettes->reduce(function ($carry, $dette) {
                $paidAmount = $dette->paiements->sum('montant');
                return $carry + $dette->montant - $paidAmount;
            }, 0);

            // Préparer le message
            $message = "Bonjour {$client->surname}, le montant total de vos dettes restantes est de " . number_format($totalDebt, 2) . ".";
            // Envoyer le message
            $twilioService->sendSms($client->telephone, $message);
        }
    }
}
