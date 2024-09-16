<?php

namespace App\Services;





interface NotificationServiceInterface
{
    public function send(string $telephone, string $message): bool;
}
