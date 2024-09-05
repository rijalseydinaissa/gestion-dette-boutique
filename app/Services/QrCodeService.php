<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\LoyaltyCard;

class QrCodeService
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
    public function createLoyaltyCard( string $surname,string $telephone, ?string $photoBase64,$qrCodeBase64): void
    {
        try {
            // Génération du code QR
            // $qrCodeBase64 = $this->generateBase64QrCode($clientId);

            // Création de la carte de fidélité
            LoyaltyCard::create([
                'surname' => $surname,
                'telephone' => $telephone,
                'photo' => $photoBase64,
                'qr_code' => $qrCodeBase64        

            ]);
        } catch (\Exception $e) {
            // Gérer l'exception selon vos besoins
            $e->getMessage();
        }
    }
}
