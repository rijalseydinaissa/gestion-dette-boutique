<?php

namespace App\Traits;

use App\Enums\StateEnum;

trait RestResponseTrait
{
    public function sendResponse($data ,$status, $message ,$codeStatut = 200)
    {
        return response()->json([
            'data' =>$data,
            'status' =>  $status->value,
            'message' => $message,
        ],$codeStatut);
    }
}
