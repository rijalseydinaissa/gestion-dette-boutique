<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Format the JSON response.
     *
     * @param mixed $data
     * @param string $status
     * @param int $statusCode
     * @return JsonResponse
     */

     
    public static function SendResponse($data, $status , $statusCode = 200)
    {
        return response()->json([
            'statut' => $status,
            'data' => $data
        ], $statusCode);
    }
}
