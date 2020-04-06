<?php

namespace App\Domain\User\Repository;


use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use Illuminate\Support\Collection;

class EloquentUserAuthRepository implements IUserAuthRepository
{
    public function getUserAuths(User $user): Collection
    {
        return $user->auths;
    }

    public function deleteAuths(User $user)
    {
        return $user->auths()->delete();
    }

    public function addUserAuth(UserAuth $userAuth): bool
    {
        return $userAuth->save();
    }

    public function findByToken(string $token): ?UserAuth
    {
        return UserAuth::whereToken($token)->first();
    }

    public function getByToken(string $token): Collection
    {
        return UserAuth::whereToken($token)->get();
    }
}
