<?php

namespace App\Domain\Auth\Action;


use App\Domain\Auth\DTO\RefreshTokenDataDto;
use App\Domain\User\Action\FindUserByTokenAction;
use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use App\Domain\User\Repository\IUserAuthRepository;
use App\Infrastructure\Exception\ErrorMessageException;

class RefreshTokenAction
{
    protected $userAuthRepository;
    protected $createUserAuthAction;
    protected $findUserByTokenAction;

    public function __construct(
        IUserAuthRepository $userAuthRepository,
        CreateUserAuthAction $createUserAuthAction,
        FindUserByTokenAction $findUserByTokenAction
    )
    {
        $this->userAuthRepository    = $userAuthRepository;
        $this->createUserAuthAction  = $createUserAuthAction;
        $this->findUserByTokenAction = $findUserByTokenAction;
    }

    /**
     * @param RefreshTokenDataDto $refreshTokenData
     * @return UserAuth
     * @throws ErrorMessageException
     * @throws \App\Infrastructure\Exception\InternalErrorException
     */
    public function handle(RefreshTokenDataDto $refreshTokenData)
    {
        $userAuths = $this->userAuthRepository->getByToken($refreshTokenData->token);

        /** @var UserAuth $userAuth */
        $userAuth = $userAuths->first(function ($userAuth) use ($refreshTokenData) {
            /** @var UserAuth $userAuth */
            return $userAuth->refresh_token === $refreshTokenData->refreshToken;
        });

        if (!$userAuth) {
            throw new ErrorMessageException(trans('auth.failed'));
        }

        $user = $this->findUserByTokenAction->handle($refreshTokenData);
        return $this->createUserAuthAction->handle($user);
    }
}
