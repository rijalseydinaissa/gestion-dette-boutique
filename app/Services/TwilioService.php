<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $twilioClient;
    protected $from;

    public function __construct()
    {
        $this->twilioClient = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $this->from = config('services.twilio.from');
    }

    public function sendSms($to, $message)
    {
        return $this->twilioClient->messages->create(
            $to,
            [
                'from' => $this->from,
                'body' => $message,
            ]
        );
    }
}
