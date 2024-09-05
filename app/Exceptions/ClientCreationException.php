<?php

// app/Exceptions/ClientCreationException.php
namespace App\Exceptions;

use Exception;

class ClientCreationException extends Exception
{
    protected $message = 'Une erreur est survenue lors de la création du client.';
}


// app/Exceptions/UserCreationException.php

