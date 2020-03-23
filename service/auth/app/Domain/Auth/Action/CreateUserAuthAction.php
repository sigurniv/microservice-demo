<?php

namespace App\Domain\Auth\Action;


use App\Domain\Auth\Model\Token;
use App\Domain\User\Action\SetUserAuthAction;
use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;

class CreateUserAuthAction
{
    protected $generateTokenAction;
    protected $setUserAuthAction;

    public function __construct(
        GenerateTokenAction $generateTokenAction,
        SetUserAuthAction $setUserAuthAction
    )
    {
        $this->generateTokenAction = $generateTokenAction;
        $this->setUserAuthAction   = $setUserAuthAction;
    }

    public function handle(User $user): UserAuth
    {
        $token        = $this->generateTokenAction->handle($user->id, Token::TOKEN_LIFETIME_SECONDS);
        $refreshToken = $this->generateTokenAction->handle($user->id, Token::REFRESH_TOKEN_LIFETIME_SECONDS);

        $userAuth = $this->setUserAuthAction->handle($user, new UserAuth([
            'user_id'                  => $user->id,
            'token'                    => $token->token,
            'token_expires_at'         => $token->expires_at,
            'refresh_token'            => $refreshToken->token,
            'refresh_token_expires_at' => $refreshToken->expires_at,
        ]));

        return $userAuth;
    }
}
