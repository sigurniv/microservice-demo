<?php

namespace App\Domain\User\Action;


use App\Domain\Auth\DTO\TokenData;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\IUserAuthRepository;
use App\Infrastructure\Exception\ErrorMessageException;
use Carbon\Carbon;

class FindUserByTokenAction
{
    protected $userAuthRepository;

    public function __construct(IUserAuthRepository $userAuthRepository)
    {
        $this->userAuthRepository = $userAuthRepository;
    }

    /**
     * @param TokenData $tokenData
     * @return User
     * @throws ErrorMessageException
     */
    public function handle(TokenData $tokenData): User
    {
        $userAuth = $this->userAuthRepository->findByToken($tokenData->token);
        if (!$userAuth || $userAuth->token_expires_at->isBefore(new Carbon())) {
            throw new ErrorMessageException(trans('auth.unauthorized'));
        }

        return $userAuth->user;
    }
}
