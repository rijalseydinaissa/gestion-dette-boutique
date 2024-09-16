<?php

namespace App\Broadcasting;

use App\Notifications\EnvoyerMessage;
use App\Services\TwilioService;

class SmsChannel
{
    protected $TwilioService;
    /**
     * Create a new channel instance.
     */
    public function __construct(TwilioService $TwilioService)
    {
        $this->TwilioService = $TwilioService;
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function send($notifiable,EnvoyerMessage $notification)
    {
        $message=$notification->toSms($notifiable);
        $recipient=$notifiable->routeNotificationFor('sms');

        $this->TwilioService->send($recipient,$message);
    }
}
