<?php

namespace App\Http;


class ApiResponseError
{
    public $message;
    public $errorCode;

    public function __construct(string $message, int $errorCode = 500)
    {
        $this->message   = $message;
        $this->errorCode = $errorCode;
    }
}
