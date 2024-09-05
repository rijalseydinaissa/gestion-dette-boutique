<?php

namespace App\Exceptions;

use Exception;

class UserCreationException extends Exception
{
    protected $message = 'Une erreur est survenue lors de la création de l\'utilisateur.';
}