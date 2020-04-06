<?php

namespace Tests\fakes\Domain\User\Repository;


use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use App\Domain\User\Repository\IUserAuthRepository;
use Illuminate\Support\Collection;

class FakeUserAuthRepository implements IUserAuthRepository
{
    /** @var UserAuth[] | Collection */
    protected $userAuths;
    protected $failsToAddAuth = false;

    public function __construct()
    {
        $this->userAuths = new Collection();
    }

    public function getUserAuths(User $user): Collection
    {
        return $this->userAuths->filter(function (UserAuth $userAuth) use ($user) {
            return $userAuth->user_id === $user->id;
        });
    }

    public function deleteAuths(User $user)
    {
        $this->userAuths = $this->userAuths->filter(function (UserAuth $userAuth) use ($user) {
            return $userAuth->user_id !== $user->id;
        });
    }

    public function addUserAuth(UserAuth $userAuth): bool
    {
        if ($this->failsToAddAuth) {
            return false;
        }

        $this->userAuths[$userAuth->user_id] = $userAuth;
        return true;
    }

    public function findByToken(string $token): ?UserAuth
    {
        return $this->userAuths->first(function (UserAuth $userAuth) use ($token) {
            return $userAuth->token === $token;
        });
    }

    public function getByToken(string $token): Collection
    {
        return $this->userAuths->filter(function (UserAuth $userAuth) use ($token) {
            return $userAuth->token === $token;
        });
    }

    public function failsToAddAuth()
    {
        $this->failsToAddAuth = true;
    }
}
