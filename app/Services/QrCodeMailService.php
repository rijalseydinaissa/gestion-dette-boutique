<?php

namespace App\Services;

use App\Facades\QrCodeMailFacade;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Facades\QrCodeFacade;
use App\Mail\LoyaltyCardMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Client;

class QrCodeMailService
{
    /**
     * Génère un QR code en base64 pour un texte donné.
     *
     * @param string $text
     * @return string
     */
    public function generateBase64QrCode(string $telephone): string
    {

        $render = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $qrCode = new Writer($render);
        $Qr= $qrCode->writeString($telephone);
         return 'data:image/png;base64,' . base64_encode($Qr);
    }

    /**
     * Crée une carte de fidélité pour un client.
     *
     * @param int $clientId
     * @param string $surname
     * @param string $telephone
     * @param string|null $photoBase64
     * @return void
     */
    // public function createLoyaltyCard( string $surname,string $telephone, ?string $photoBase64,$qrCodeBase64): void
    // {
    //     try {
    //         // Génération du code QR
    //         // $qrCodeBase64 = $this->generateBase64QrCode($clientId);

    //         // Création de la carte de fidélité
    //         LoyaltyCard::create([
    //             'surname' => $surname,
    //             'telephone' => $telephone,
    //             'photo' => $photoBase64,
    //             'qr_code' => $qrCodeBase64        

    //         ]);
    //     } catch (\Exception $e) {
    //         // Gérer l'exception selon vos besoins
    //         $e->getMessage();
    //     }
    // }
    public function generateQrCodeAndSendMail(Client $client): void
    {
        try {
            // Générer le QR code
            $qrCodeBase64 = $this->generateBase64QrCode($client->telephone);
            $client->qrcode = $qrCodeBase64;
            
            $client->save();
            // Log::info($client->user);
            // Envoyer l'e-mail de carte de fidélité si le client a le rôle spécifique
            if ($client->user->role_id == 1) {
                Mail::to($client->user->login)->send(new LoyaltyCardMail($client));
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du QR code ou de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }

    
}
