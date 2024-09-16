<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Dette;
use Carbon\Carbon;

class SendSmsReminder extends Command
{
    protected $signature = 'sms:send-reminders';
    protected $description = 'Envoie un rappel SMS aux clients avec des dettes non soldées toutes les 20 minutes.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Récupérer les dettes non soldées
        $dettesNonSoldees = Dette::whereRaw('(SELECT SUM(montant) FROM paiements WHERE paiements.dette_id = dettes.id) < dettes.montant')
            ->with('client')
            ->get();

        $smsService = env('SMS_SERVICE');

        foreach ($dettesNonSoldees as $dette) {
            $client = $dette->client;
            $montantRestant = $dette->montant - $dette->paiements->sum('montant');
            $message = "Bonjour {$client->surnom}, vous avez une dette de {$montantRestant} FCFA. Veuillez la régler dans les plus brefs délais.";

            try {
                if ($smsService === 'twilio') {
                    // Configuration de Twilio
                    $twilioSid = env('TWILIO_SID');
                    $twilioAuthToken = env('TWILIO_AUTH_TOKEN');
                    $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');

                    $twilioClient = new \Twilio\Rest\Client($twilioSid, $twilioAuthToken);

                    // Envoi du SMS avec Twilio
                    $twilioClient->messages->create(
                        $client->telephone, // Numéro du client
                        [
                            'from' => $twilioPhoneNumber,
                            'body' => $message
                        ]
                    );

                    $this->info("SMS envoyé à {$client->surnom} ({$client->telephone}) avec Twilio.");
                } elseif ($smsService === 'infobip') {
                    // Configuration d'Infobip
                    $infobipApiKey = env('INFOBIP_API_KEY');
                    $infobipPhoneNumber = env('INFOBIP_PHONE_NUMBER');

                    $response = Http::withHeaders([
                        'Authorization' => 'App ' . $infobipApiKey,
                        'Content-Type' => 'application/json',
                    ])->post('https://api.infobip.com/sms/1/text/single', [
                        'from' => $infobipPhoneNumber,
                        'to' => $client->telephone,
                        'text' => $message
                    ]);

                    if ($response->successful()) {
                        $this->info("SMS envoyé à {$client->surnom} ({$client->telephone}) avec Infobip.");
                    } else {
                        $this->error("Erreur lors de l'envoi du SMS à {$client->surnom} avec Infobip: " . $response->body());
                    }
                }
            } catch (\Exception $e) {
                $this->error("Erreur lors de l'envoi du SMS à {$client->surnom}: " . $e->getMessage());
            }
        }

        $this->info('Tous les rappels SMS ont été envoyés.');
    }
}