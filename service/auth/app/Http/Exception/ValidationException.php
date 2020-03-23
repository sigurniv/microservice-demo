<?php

namespace App\Http\Exception;


use Illuminate\Contracts\Validation\Validator;
use Throwable;

class ValidationException extends \Exception
{
    protected $validator;

    public function __construct(Validator $validator, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->validator = $validator;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Validator
     */
    public function getValidator(): Validator
    {
        return $this->validator;
    }
}
