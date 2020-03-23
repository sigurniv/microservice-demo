<?php

namespace Tests\Unit\Domain\User\DTO;


use App\Domain\User\DTO\UserData;
use App\Http\Exception\ValidationException;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserDataTest extends TestCase
{
    public function testConstructor()
    {
        $userData = new UserData('test@gmail.com', '123');
        $this->assertInstanceOf(UserData::class, $userData);
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

        $userData = UserData::fromRequest($request);
        $this->assertInstanceOf(UserData::class, $userData);
    }

    public function requestDataProvider()
    {
        return [
            ['123', '123', true],
            ['test@gmail.com', '123', false],
        ];
    }
}
