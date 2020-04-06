<?php

namespace App\Domain\User\Repository;


use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use Illuminate\Support\Collection;

interface IUserAuthRepository
{
    public function getUserAuths(User $user): Collection;

    public function deleteAuths(User $user);

    public function addUserAuth(UserAuth $userAuth): bool;

    public function findByToken(string $token): ?UserAuth;

    public function getByToken(string $token): Collection;
}
