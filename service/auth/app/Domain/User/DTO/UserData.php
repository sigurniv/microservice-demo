<?php

namespace App\Domain\User\DTO;


use App\Http\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webmozart\Assert\Assert;

class UserData
{
    public $email;
    public $password;

    /**
     * UserData constructor.
     * @param string $email
     * @param string $password
     * @throws ValidationException
     */
    public function __construct(string $email, string $password)
    {
        static::validateInput(['email' => $email, 'password' => $password]);

        $this->email    = $email;
        $this->password = $password;
    }

    /**
     * @param Request $request
     * @return UserData
     * @throws ValidationException
     */
    public static function fromRequest(Request $request): UserData
    {
        return new UserData($request->get('email'), $request->get('password'));
    }

    /**
     * @param array $input
     * @throws ValidationException
     */
    protected static function validateInput(array $input)
    {
        $validator = Validator::make($input, [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

    }
}
