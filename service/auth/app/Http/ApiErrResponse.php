<?php

namespace App\Http;


class ApiErrResponse extends ApiResponse
{
    public function __construct(ApiResponseError $error)
    {
        $this->setError($error);
        parent::__construct([]);
    }
}
