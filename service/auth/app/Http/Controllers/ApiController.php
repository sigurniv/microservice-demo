<?php

namespace App\Http\Controllers;


use App\Http\ApiResponse;
use App\Http\ApiResponseError;
use Illuminate\Contracts\Validation\Validator;

class ApiController extends Controller
{
    public function validatorError(Validator $validator)
    {
        $error = $validator->errors()->first();
        return $this->respond((new ApiResponse())->setError(new ApiResponseError($error, 422)));
    }

    public function respond(ApiResponse $apiResponse)
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
