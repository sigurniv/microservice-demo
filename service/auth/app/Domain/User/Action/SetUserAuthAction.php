<?php

namespace App\Domain\User\Action;


use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use App\Domain\User\Repository\IUserAuthRepository;

class SetUserAuthAction
{
    protected $userAuthRepository;

    public function __construct(IUserAuthRepository $userAuthRepository)
    {
        $this->userAuthRepository = $userAuthRepository;
    }

    public function handle(User $user, UserAuth $userAuth): ?UserAuth
    {
        $auths = $this->userAuthRepository->getUserAuths($user);
        if (count($auths) > 10) {
            $this->userAuthRepository->deleteAuths($user);
        }

        $userAuth = $this->userAuthRepository->addUserAuth($user, $userAuth);
        return $userAuth ?? null;
    }
}
