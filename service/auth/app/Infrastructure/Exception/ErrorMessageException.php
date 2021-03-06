<?php

namespace App\Infrastructure\Exception;


use Throwable;

class ErrorMessageException extends \Exception
{
    public function __construct(string $message = "", int $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
