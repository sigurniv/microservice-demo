<?php

namespace App\Domain\Auth\Action;


use App\Domain\User\Action\FindUserByEmailAction;
use App\Domain\User\DTO\UserDataDto;
use App\Domain\User\Model\UserAuth;
use App\Infrastructure\Exception\ErrorMessageException;
use Illuminate\Http\Response;

class GetTokenAction
{
    protected $findUserAction;
    protected $createUserAuthAction;

    public function __construct(
        FindUserByEmailAction $findUserAction,
        CreateUserAuthAction $createUserAuthAction
    )
    {
        $this->findUserAction       = $findUserAction;
        $this->createUserAuthAction = $createUserAuthAction;
    }

    /**
     * @param UserDataDto $userData
     * @return UserAuth
     * @throws ErrorMessageException
     */
    public function handle(UserDataDto $userData): UserAuth
    {
        $user = $this->findUserAction->handle($userData);
        if (!$user) {
            throw new ErrorMessageException('model.user.not-found', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userAuth = $this->createUserAuthAction->handle($user);
        if (!$userAuth) {
            throw new ErrorMessageException('error.internal', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $userAuth;
    }
}
