<?php

namespace Tests\Feature\Api\Auth;


use Tests\MockeryDefaultTestCase;

class GetTokenTest extends MockeryDefaultTestCase
{
    public function testGetTokenValidatesInput()
    {
        $email    = 'email';
        $password = 'password';

        $response = $this->json('GET', '/api/v1/auth/token', ['email' => $email, 'password' => $password]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'data',
                'error'
            ]);
    }
}
