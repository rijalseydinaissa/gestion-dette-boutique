<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface QrCodeMailInterface {
    public function generateBase64QrCode(string $telephone): string;
    // public function createLoyaltyCard(int $clientId, string $surname,string $telephone, ?string $photoBase64,$qrCodeBase64): void;
    public function generateQrCodeAndSendMail(Client $client): void;
}