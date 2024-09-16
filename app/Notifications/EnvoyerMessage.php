<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Broadcasting\SmsChannel;
use App\Services\TwilioService;

class EnvoyerMessage extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $to;
    protected $message;
    public function __construct($to,$message)
    {
        
        $this->to=$to;
        $this->message=$message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database',SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
   

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
  
    public function toSms($notifiable){
        App(TwilioService::class)->send($this->to,$this->message);
        return true;
    }

    public function toDatabase($notifiable){
        return [
            'message' => $this->message,
            'to' => $this->to,
        ];
    }
}
