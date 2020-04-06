<?php

namespace App\Domain\User\Action;


use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use App\Domain\User\Repository\IUserAuthRepository;
use App\Infrastructure\Exception\InternalErrorException;

class SetUserAuthAction
{
    protected $userAuthRepository;

    public function __construct(IUserAuthRepository $userAuthRepository)
    {
        $this->userAuthRepository = $userAuthRepository;
    }

    /**
     * @param User $user
     * @param UserAuth $userAuth
     * @return UserAuth|null
     * @throws InternalErrorException
     */
    public function handle(User $user, UserAuth $userAuth): ?UserAuth
    {
        $auths = $this->userAuthRepository->getUserAuths($user);
        if (count($auths) > 10) {
            $this->userAuthRepository->deleteAuths($user);
        }

        $result = $this->userAuthRepository->addUserAuth($userAuth);
        if (!$result) {
            throw new InternalErrorException('failed to add user auth');
        }
        return $userAuth ?? null;
    }
}
