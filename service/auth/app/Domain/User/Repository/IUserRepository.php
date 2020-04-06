<?php

namespace App\Domain\User\Repository;


use App\Domain\User\Model\User;

interface IUserRepository
{
    public function findByEmail(string $email): ?User;

    public function save(User $user): bool;
}
