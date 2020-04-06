<?php

namespace App\Domain\User\Repository;


use App\Domain\User\Model\User;

class EloquentUserRepository implements IUserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function save(User $user): bool
    {
        return $user->save();
    }
}
