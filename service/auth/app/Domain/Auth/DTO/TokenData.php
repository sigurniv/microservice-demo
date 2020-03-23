<?php

namespace App\Domain\Auth\DTO;


use App\Http\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TokenData
{
    public $token;

    /**
     * TokenData constructor.
     * @param string $token
     * @throws ValidationException
     */
    public function __construct(string $token)
    {
        static::validateInput(['token' => $token]);
        $this->token = $token;
    }

    /**
     * @param Request $request
     * @return TokenData
     * @throws ValidationException
     */
    public static function fromRequest(Request $request)
    {
        return new TokenData($request->get('token'));
    }

    /**
     * @param array $input
     * @throws ValidationException
     */
    protected static function validateInput(array $input)
    {
        $validator = Validator::make($input, [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
