<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class ApiResponseMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            // Extraire le contenu actuel de la réponse
            $data = $response->getData(true);
            $statusCode = $response->getStatusCode();
            $status = $statusCode >= 200 && $statusCode < 300 ? 'success' : 'echec';

            // Réformater la réponse en utilisant le trait ApiResponse
            return self::SendResponse($data, $status, $statusCode);
        }

        // Si la réponse n'est pas une JsonResponse, la retourner telle quelle
        return $response;
    }
}
