<?php

namespace App\Http\Response;

class Responder implements IResponder
{
    public function respond(\App\Http\ApiResponse $apiResponse)
    {
        return response()->json(
            [
                'data'  => $apiResponse->getData(),
                'error' => $apiResponse->getError()
            ],
            $apiResponse->getStatusCode(),
            $apiResponse->getHeaders()
        );
    }
}
