<?php

namespace Tests\Feature\Api\Auth;


use App\Domain\User\Action\FindUserByEmailAction;
use App\Domain\User\Action\FindUserByTokenAction;
use App\Domain\User\Model\User;
use Mockery\MockInterface;
use Tests\MockeryDefaultTestCase;

class GetUserTest extends MockeryDefaultTestCase
{
    public function testGetTokenValidatesInput()
    {
        $token = '';

        $response = $this->json('GET', '/api/v1/auth/user', ['token' => $token]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'data',
                'error'
            ]);
    }

    public function testGetTokenReturnsErrorIfUserIsNotFound()
    {
        $token = 'token';
        $this->mock(FindUserByTokenAction::class, function ($mock) {
            /** @var $mock MockInterface */
            $mock->shouldReceive('handle')->once()->andThrow(new \Exception());
        });

        $response = $this->json('GET', '/api/v1/auth/user', ['token' => $token]);

        $response
            ->assertStatus(401)
            ->assertJsonStructure([
                'data',
                'error'
            ]);
    }

    public function testGetTokenReturnsCorrectResponse()
    {
        $token       = 'token';
        $user        = new User();
        $user->id    = 123;
        $user->email = 'email@test.com';

        $this->mock(FindUserByTokenAction::class, function ($mock) use ($user) {
            /** @var $mock MockInterface */
            $mock->shouldReceive('handle')->once()->andReturn($user);
        });

        $response = $this->json('GET', '/api/v1/auth/user', ['token' => $token]);

        $response
            ->assertStatus(200)
            ->assertJson($user->toArray());
    }

}
