<?php

namespace Tests\Unit\Domain\User\DTO;


use App\Domain\User\DTO\UserDataDto;
use App\Http\Exception\ValidationException;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserDataDtoTest extends TestCase
{
    public function testConstructorMakesInstance()
    {
        $userData = new UserDataDto('test@gmail.com', '123');
        $this->assertInstanceOf(UserDataDto::class, $userData);
    }

    /**
     * @dataProvider requestDataProvider
     * @param string $email
     * @param string $password
     * @param bool $expectedException
     * @throws ValidationException
     */
    public function testFromRequestValidatesRequest(string $email, string $password, bool $expectedException)
    {
        $request = new Request(['email' => $email, 'password' => $password]);

        if ($expectedException) {
            $this->expectException(ValidationException::class);
        }

        $userData = UserDataDto::fromRequest($request);
        $this->assertInstanceOf(UserDataDto::class, $userData);
    }

    public function requestDataProvider()
    {
        return [
            ['123', '123', true],
            ['test@gmail.com', '123', false],
        ];
    }
}
