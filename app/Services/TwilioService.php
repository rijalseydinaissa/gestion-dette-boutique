<?php

namespace App\Services;

use Twilio\Rest\Client;



class TwilioService implements NotificationServiceInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function send(string $telephone, string $message): bool
    {
        try {
            $this->client->messages->create(
                '+221778170068', 
                [
                    'from' => env('TWILIO_PHONE_NUMBER'), 
                    'body' => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            // Log error
            return false;
        }
    }
}
