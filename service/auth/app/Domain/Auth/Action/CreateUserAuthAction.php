<?php

namespace App\Domain\Auth\Action;


use App\Domain\Auth\Support\JWT\ITokenGenerator;
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

    /**
     * @param User $user
     * @return UserAuth
     * @throws \App\Infrastructure\Exception\InternalErrorException
     */
    public function handle(User $user): UserAuth
    {
        $token        = $this->generateTokenAction->handle($user->id, ITokenGenerator::TOKEN_LIFETIME_SECONDS);
        $refreshToken = $this->generateTokenAction->handle($user->id, ITokenGenerator::REFRESH_TOKEN_LIFETIME_SECONDS);

        $userAuth = $this->setUserAuthAction->handle($user, new UserAuth([
            'user_id'                  => $user->id,
            'token'                    => $token->getToken(),
            'token_expires_at'         => $token->getExpiresAt(),
            'refresh_token'            => $refreshToken->getToken(),
            'refresh_token_expires_at' => $refreshToken->getExpiresAt(),
        ]));

        return $userAuth;
    }
}
