<?php

namespace App\Domain\Auth\DTO;


use App\Http\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RefreshTokenData extends TokenData
{
    public $token;
    public $refreshToken;

    /**
     * RefreshTokenData constructor.
     * @param string $token
     * @param string $refreshToken
     * @throws ValidationException
     */
    public function __construct(string $token, string $refreshToken)
    {
        parent::__construct($token);
        static::validateInput(['refreshToken' => $refreshToken]);
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param Request $request
     * @return RefreshTokenData
     * @throws ValidationException
     */
    public static function fromRequest(Request $request)
    {
        return new RefreshTokenData($request->get('token'), $request->get('refreshToken'));
    }

    /**
     * @param array $input
     * @throws ValidationException
     */
    protected static function validateInput(array $input)
    {
        $validator = Validator::make($input, [
            'refreshToken' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
