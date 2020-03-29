<?php

namespace App\Domain\User\DTO;


use App\Http\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webmozart\Assert\Assert;

class UserDataDto
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
     * @return UserDataDto
     * @throws ValidationException
     */
    public static function fromRequest(Request $request): UserDataDto
    {
        $email = $request->get('email', '');
        $email = is_null($email) ? '' : $email;

        $password = $request->get('password', '');
        $password = is_null($password) ? '' : $password;

        return new UserDataDto(
            $email,
            $password
        );
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
