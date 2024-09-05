<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * Une liste des exceptions qui ne devraient pas être rapportées.
     *
     * @var array
     */
    protected $dontReport = [
        ClientCreationException::class,
        UserCreationException::class,
    ];

    /**
     * Une liste des exceptions qui sont susceptibles d'être converties en réponse HTTP.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Enregistrez les rendus d'exceptions dans l'application.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Throwable $e)
    {
        // Gestion des exceptions personnalisées
        if ($e instanceof ClientCreationException) {
            return response()->json([
                'error' => 'Erreur lors de la création du client: ' . $e->getMessage(),
            ], 500);
        }

        if ($e instanceof UserCreationException) {
            return response()->json([
                'error' => 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage(),
            ], 500);
        }

        // Gestion des autres exceptions
        return parent::render($request, $e);
    }

    /**
     * Rapporte une exception à Sentry ou à un autre service de reporting.
     *
     * @param \Throwable $e
     * @return void
     */
    public function report(\Throwable $e)
    {
        if ($this->shouldReport($e)) {
            // Vérifiez si Sentry est configuré et disponible
            if (app()->bound('sentry')) {
                // Envoyez l'exception à Sentry
                app('sentry')->captureException($e);
            }
        }

        // Appel à la méthode parent pour maintenir la logique de reporting par défaut
        parent::report($e);
    }
}
